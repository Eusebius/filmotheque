<?php
/**
 * getimdb.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the content for the page proposing a match on imdb for a movie.
 */
/*
  Filmothèque
  Copyright (C) 2012-2015 Eusebius (eusebius@eusebius.fr)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License along
  with this program; if not, write to the Free Software Foundation, Inc.,
  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

//TODO manage exceptions when there is no Internet connectivity

require_once('includes/declarations.inc.php');
require_once('includes/initialization.inc.php');
use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\Movie;
use Eusebius\Filmotheque\Util;
Auth::ensurePermission('write');

$id_movie_string = filter_input(INPUT_GET, 'id_movie', FILTER_SANITIZE_NUMBER_INT);

if ($id_movie_string !== false && $id_movie_string !== NULL && $id_movie_string !== '') {

    if ((string) (int) $id_movie_string == $id_movie_string) {
        $id_movie = (int) $id_movie_string;
    } else {
        // Return to home page if movie ID is not a number
        Util::gotoMainPage();
    }

    $movie = new Movie($id_movie);

    $conn = Util::getDbConnection();

    echo '<h2>' . $movie->getTitle() . " - lier à une fiche IMDb</h2>\n";
    
    $xml = new DomDocument();
    
    $imdb_id = filter_input(INPUT_GET, 'imdb_id', FILTER_SANITIZE_STRING);
    if ($imdb_id !== false && $imdb_id !== NULL && $imdb_id !== '') {
        $xml->load('http://myapifilms.com/imdb/idIMDB?idIMDB=' . $imdb_id . '&format=xml&token=' . $apiToken);
        $item = $xml->getElementsByTagName('movie')->item(0);
    } else {
        $query = 'http://myapifilms.com/imdb/idIMDB?title=' . $movie->getTitle() . '&format=xml&limit=1&token=' . $apiToken;
        //Util::debug($query);
        $xml->load($query);
        $item = $xml->getElementsByTagName('movie')->item(0);
    }

    //Util::debug($xml);
    //Util::debug($xml->saveXML());

    if ($item != null) {
        if (!isset($_SESSION['imdbdata'])) {
            $_SESSION['imdbdata'] = array();
        }
        $_SESSION['imdbdata'][$id_movie] = $xml->saveXML();
        //die($_SESSION['imdbdata'][$id_movie]);
        $originaltitle = $item->getElementsByTagName('title')->item(0)->nodeValue;
        if ($imdb_id == '') {
            $imdb_id = $item->getElementsByTagName('idIMDB')->item(0)->nodeValue;
        }
        $cover = $item->getElementsByTagName('urlPoster')->item(0);
        if ($cover != null) {
            $cover = $cover->nodeValue;
        }
        $year = $item->getElementsByTagName('year')->item(0)->nodeValue;
        ?>

        <table border="1">
            <tr>
                <td colspan="2" align="center">
                    <?php
                    if ($cover != null) {
                        echo '<img src="' . $cover . '"/>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Titre original&nbsp;:</td>
                <td><?php echo $originaltitle; ?></td>
            </tr>
            <tr>
                <td>Identifiant IMDb&nbsp;:</td>
                <td><a href="http://www.imdb.com/title/<?php echo $imdb_id; ?>/"><?php echo $imdb_id; ?></a></td>
            </tr>
            <tr>
                <td>Année&nbsp;:</td>
                <td><?php echo $year; ?></td>
            </tr>
            <tr>
                <td>Genres&nbsp;:</td>
                <td>
                    <?php
                    $genres = $item->getElementsByTagName('genres')->item(0);
                    $genreElements = $genres->getElementsByTagName('genre');
                    foreach ($genreElements as $genre) {
                        echo $genre->nodeValue . ' ';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <form action="scripts/dolinkimdb.php" method="POST">
                        <input type="hidden" name="id_movie" value="<?php echo $movie->getID(); ?>" />
                        <input type="hidden" name="imdb_id" value="<?php echo $imdb_id; ?>" />
                        <input type="submit" value="Utiliser cette fiche" />
                    </form>
                    <br />
                    <form action="" method="GET">
                        <input type="hidden" name="page" value="getimdb" />
                        <input type="hidden" name="id_movie" value="<?php echo $movie->getID(); ?>" />
                        <input type="text" name="imdb_id"/>
                        <input type="submit" value="Saisir un identifiant IMDb" />
                    </form>
                </td>
            </tr>
        </table>

        <?php
    } else {
        ?>

        <table border="1">
            <tr>
                <td colspan="2" align="center">
                    <strong>Fiche non trouvée</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <form action="" method="GET">
                        <input type="hidden" name="page" value="getimdb" />
                        <input type="hidden" name="id_movie" value="<?php echo $movie->getID(); ?>" />
                        <input type="text" name="imdb_id"/>
                        <input type="submit" value="Saisir un identifiant IMDb" />
                    </form>
                </td>
            </tr>
        </table>
        <?php
    }

    echo '<p><a href="?page=moviedetails&id_movie=' . $movie->getID()
    . '">Retour à la page du film</a></p>';
} else {
    // Return to home page if no movie is specified
    Util::gotoMainPage();
}
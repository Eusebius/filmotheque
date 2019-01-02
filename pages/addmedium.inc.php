<?php
/**
 * addmedium.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the content for the medium creation page.
 */
/*
  Filmothèque
  Copyright (C) 2012-2019 Eusebius (eusebius@eusebius.fr)

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

if (__FILE__ === $_SERVER["SCRIPT_FILENAME"]) {
    header('Location: ../');
    die();
}

require_once('includes/declarations.inc.php');
require_once('includes/initialization.inc.php');

use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\Util;

Auth::ensurePermission('write');

//TODO make a static method to obtain id_movie or id_medium from GET

$id_movie_string = filter_input(INPUT_GET, 'id_movie', FILTER_SANITIZE_NUMBER_INT);

if ($id_movie_string !== false && $id_movie_string !== NULL && $id_movie_string !== '') {

    if ((string) (int) $id_movie_string == $id_movie_string) {
        $id_movie = (int) $id_movie_string;
    } else {
        // Return to home page if movie ID is not a number
        Util::gotoMainPage();
    }

    $movie = Util::getMovieInSession($id_movie);
    ?>
    <h3>Création d'un support pour le film numéro <?php echo $id_movie; //'     ?></h3>
    <form action="scripts/docreatemedium.php" method="POST">
        <input type="hidden" name="id_movie" value="<?php echo $id_movie; ?>" />
        <table>
            <tr><td>Titre du film&nbsp;:</td><td><?php echo $movie->getTitle(); ?></td></tr>
            <tr><td>Conteneur&nbsp;:</td><td>
                    <select name="container">
                        <?php
                        $conn = Util::getDbConnection();
                        try {
                            $types = $conn->prepare('select distinct `container` from `containers` order by `container`');
                            $types->execute();
                            $typeArray = $types->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal('Error while retrieving containers: ' . $e->getMessage());
                        }
                        foreach ($typeArray as $type) {
                            if ($type['container'] != '') {
                                echo '<option value="' . $type['container'] . '" ';
                                echo '>' . $type['container'] . '</option>' . "\n";
                            }
                        }
                        ?>
                    </select>
                </td></tr>
            <tr><td>Largeur en pixels&nbsp;:</td><td><input type="text" name="width" /></td></tr>
            <tr><td>Hauteur en pixels&nbsp;:</td><td><input type="text" name="height" /></td></tr>
            <tr><td>Commentaires&nbsp;:</td><td><input type="text" name="comment" /></td></tr>
            <?php
            $conn2 = Util::getDbConnection();
            try {
                $next = $conn2->prepare('SELECT shelfmark+1 next FROM `media` m WHERE not exists (select shelfmark from media where media.shelfmark = m.shelfmark+1) and m.shelfmark is not null order by next limit 1');
                $next->execute();
                if ($next->rowCount() == 0) {
                    $nextShelfmark = 0;
                } else {
                    $nextArray = $next->fetchall(PDO::FETCH_ASSOC);
                    $nextShelfmark = $nextArray[0]['next'];
                }
            } catch (PDOException $e) {
                Util::fatal('Error while retrieving the next available shelfmark: ' . $e->getMessage());
            }
            ?>
            <tr><td>Cote&nbsp;:</td><td><input type="text" name="shelfmark" value="<?php echo $nextShelfmark; ?>" /></td>
            <!-- <td>(première cote disponible&nbsp;: <?php echo $nextShelfmark; ?>)</td></tr> -->
            <tr><td>Pistes audio&nbsp;:</td><td>
                    <select name="audio[]" multiple>
                        <?php
                        try {
                            $languages = $conn->prepare('select distinct language from `languages`');
                            $languages->execute();
                            $languageArray = $languages->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal('Error while retrieving available languages: ' . $e->getMessage());
                        }
                        foreach ($languageArray as $lang) {
                            echo '<option value="' . $lang['language'] . '" ';
                            echo '>' . $lang['language'] . '</option>' . "\n";
                        }
                        ?>
                    </select>
                </td></tr>
            <tr><td>Sous-titres&nbsp;:</td><td>
                    <select name="subs[]" multiple>
                        <?php
                        foreach ($languageArray as $lang) {
                            echo '<option value="' . $lang['language'] . '" ';
                            echo '>' . $lang['language'] . '</option>' . "\n";
                        }
                        ?>
                    </select>
                </td></tr>
            <tr><td></td><td></td></tr>
            <tr><td colspan="2"><hr /></td></tr>
            <tr><td colspan="2"><center><input type="submit" value="Créer" /></center></td></tr>
        </table>
    </form>

    <br /><br />
    <a href=".">Retour à la page principale</a>

    <?php
    //$movie->dump();
} else {
    // Return to home page if no movie is specified
    Util::gotoMainPage();
}
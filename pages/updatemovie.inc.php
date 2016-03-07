<?php
/**
 * updatemovie.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the content for the movie update form.
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

require_once('includes/declarations.inc.php');
require_once('includes/initialization.inc.php');

use Eusebius\Filmotheque\Auth;
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

    //Is it really necessary?
    unset($_SESSION['movie']);

    $movie = Util::getMovieInSession($id_movie);
    ?>
    <h3>Mise à jour du film numéro <?php echo $id_movie; ?></h3>
    <form action="scripts/doupdatemovie.php" method="POST">
        <input type="hidden" name="id_movie" value="<?php echo $id_movie; ?>" />
        <table>
            <tr><td>Titre&nbsp;:</td><td><input type="text" name="title" value="<?php echo $movie->getTitle(); ?>" /></td></tr>
            <tr><td>Année&nbsp;:</td><td><input type="text" name="year" value="<?php echo $movie->getYear(); ?>" /></td></tr>
            <tr><td>Réalisateur(s)&nbsp;:</td><td>
                    <select name="makers[]" multiple>
                        <?php
                        $conn = Util::getDbConnection();
                        try {
                            $selectedMakers = $conn->prepare('select `id_person`, `name` from `persons` natural join `movies-makers` natural join `movies` WHERE id_movie=? order by name');
                            $selectedMakers->execute(array($id_movie));
                            $selectedMakersArray = $selectedMakers->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal($e->getMessage());
                        }
                        foreach ($selectedMakersArray as $maker) {
                            echo '<option value="' . $maker['id_person'] . '" selected>' . $maker['name'] . '</option>' . "\n";
                        }
                        try {
                            $otherMakers = $conn->prepare('SELECT id_person, name FROM `persons` WHERE id_person NOT IN (SELECT `id_person` FROM `persons` NATURAL JOIN `movies-makers` NATURAL JOIN `movies` WHERE id_movie =?) order by name');
                            $otherMakers->execute(array($id_movie));
                            $otherMakersArray = $otherMakers->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal($e->getMessage());
                        }
                        foreach ($otherMakersArray as $maker) {
                            echo '<option value="' . $maker['id_person'] . '">' . $maker['name'] . '</option>' . "\n";
                        }
                        ?>
                    </select>
                </td></tr>
            <tr><td>Acteur(s)&nbsp;:</td><td>
                    <select name="actors[]" multiple>
                        <?php
                        $conn2 = Util::getDbConnection();
                        try {
                            $selectedActors = $conn2->prepare('select `id_person`, `name` from `persons` natural join `movies-actors` natural join `movies` WHERE id_movie=? order by name');
                            $selectedActors->execute(array($id_movie));
                            $selectedActorsArray = $selectedActors->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal($e->getMessage());
                        }
                        foreach ($selectedActorsArray as $actor) {
                            echo '<option value="' . $actor['id_person'] . '" selected>' . $actor['name'] . '</option>' . "\n";
                        }
                        try {
                            $otherActors = $conn2->prepare('SELECT id_person, name FROM `persons` WHERE id_person NOT IN (SELECT `id_person` FROM `persons` NATURAL JOIN `movies-actors` NATURAL JOIN `movies` WHERE id_movie =?) order by name');
                            $otherActors->execute(array($id_movie));
                            $otherActorsArray = $otherActors->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal($e->getMessage());
                        }
                        foreach ($otherActorsArray as $actor) {
                            echo '<option value="' . $actor['id_person'] . '">' . $actor['name'] . '</option>' . "\n";
                        }
                        ?>
                    </select>
                </td></tr>
            <tr><td>Catégorie(s)&nbsp;:</td><td>
                    <select name="categories[]" multiple>
                        <?php
                        try {
                            $cats = $conn->prepare('select category from categories');
                            $cats->execute();
                            $catArray = $cats->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal($e->getMessage());
                        }
                        foreach ($catArray as $cat) {
                            echo '<option value="' . $cat['category'] . '"';
                            if (in_array($cat['category'], $movie->getCategories())) {
                                echo ' selected';
                            }
                            echo '>' . $cat['category'] . '</option>' . "\n";
                        }
                        ?>
                    </select>
                </td></tr>
            <tr><td>Shortlist(s)&nbsp;:</td><td>
                    <select name="shortlists[]" multiple>
                        <?php
                        try {
                            $shortlists = $conn->prepare('select id_shortlist, listname from shortlists');
                            $shortlists->execute();
                            $slArray = $shortlists->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal($e->getMessage());
                        }
                        foreach ($slArray as $sl) {
                            echo '<option value="' . $sl['id_shortlist'] . '"';
                            if (in_array($sl['listname'], $movie->getShortlists())) {
                                echo ' selected';
                            }
                            echo '>' . $sl['listname'] . '</option>' . "\n";
                        }
                        ?>
                    </select>
                </td></tr>
            <tr><td colspan="2"><hr /></td></tr>
            <tr><td>Note sur 5&nbsp;:</td><td><input type="text" name="rating" value="<?php echo $movie->getRating(); ?>"/></td></tr>
            <tr><td>Vu le (jj/mm/aaaa)&nbsp;:</td><td><input type="text" name="lastseen" value="<?php echo $movie->getFormattedLastseen(); ?>"/></td></tr>
            <tr><td></td><td></td></tr>
            <tr><td colspan="2"><hr /></td></tr>
            <tr><td colspan="2"><center><input type="submit" value="Mettre à jour" /></center></td></tr>
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
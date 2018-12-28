<?php
/**
 * updatemedium.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the content for the medium update form.
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
use Eusebius\Filmotheque\Medium;
use Eusebius\Filmotheque\Util;

Auth::ensurePermission('write');

$id_medium_string = filter_input(INPUT_GET, 'id_medium', FILTER_SANITIZE_NUMBER_INT);

if ($id_medium_string !== false && $id_medium_string !== NULL && $id_medium_string !== '') {

    if ((string) (int) $id_medium_string == $id_medium_string) {
        $id_medium = (int) $id_medium_string;
    } else {
        // Return to home page if medium ID is not a number
        Util::gotoMainPage();
    }

    // Is it really necessary?
    // Can we not reuse the object in session if it is here?
    unset($_SESSION['medium']);

    $medium = new Medium($id_medium);
    if (isset($_SESSION['movie'])) {
        $medium = new Medium($id_medium, $_SESSION['movie']);
    } else {
        $medium = new Medium($id_medium);
        $medium->retrieveMovie();
    }
    $movie = $medium->getMovie();
    $_SESSION['medium'] = $medium;
    ?>
    <h3>Mise à jour du support numéro <?php echo $id_medium; ?></h3>
    <form action="scripts/doupdatemedium.php" method="POST">
        <input type="hidden" name="id_medium" value="<?php echo $id_medium; ?>" />
        <table>
            <tr><td>Titre du film&nbsp;:</td><td><?php echo $movie->getTitle(); ?></td></tr>
            <tr><td>Conteneur&nbsp;:</td><td>
                    <select name="container">
                        <?php
                        $conn = Util::getDbConnection();
                        try {
                            $containers = $conn->prepare('select distinct container from `containers`');
                            $containers->execute();
                            $containerArray = $containers->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal('Error while listing containers: ' . $e->getMessage());
                        }
                        foreach ($containerArray as $container) {
                            if ($container['container'] != '') {
                                echo '<option value="' . $container['container'] . '" ';
                                if ($container['container'] == $medium->getContainer()) {
                                    echo 'selected';
                                }
                                echo '>' . $container['container'] . '</option>' . "\n";
                            }
                        }
                        ?>
                    </select>
                </td></tr>
            <tr><td>Largeur en pixels&nbsp;:</td><td><input type="text" name="width" value="<?php echo $medium->getWidth(); ?>"/></td></tr>
            <tr><td>Hauteur en pixels&nbsp;:</td><td><input type="text" name="height" value="<?php echo $medium->getHeight(); ?>"/></td></tr>
            <tr><td>Commentaires&nbsp;:</td><td><input type="text" name="comment" value="<?php echo $medium->getComment(); ?>"/></td></tr>
            <?php
            try {
                $next = $conn->prepare('SELECT shelfmark+1 next FROM `media` m WHERE not exists (select shelfmark from media where media.shelfmark = m.shelfmark+1) and m.shelfmark is not null order by next limit 1');
                $next->execute();
                if ($next->rowCount() == 0) {
                    Util::fatal('Error while trying to get next shelfmark (no entries in database)');
                }
                $nextArray = $next->fetchall(PDO::FETCH_ASSOC);
                $nextShelfmark = $nextArray[0]['next'];
            } catch (PDOException $e) {
                Util::fatal('Error while trying to get next shelfmark: ' . $e->getMessage());
            }
            ?>
            <tr><td>Cote&nbsp;:</td><td><input type="text" name="shelfmark" value="<?php echo $medium->getShelfmark(); ?>"/></td><td>(première cote disponible&nbsp;: <?php echo $nextShelfmark; ?>)</td></tr>
            <tr><td>Pistes audio&nbsp;:</td><td>
                    <select name="audio[]" multiple>
                        <?php
                        $conn2 = Util::getDbConnection();
                        try {
                            $languages = $conn2->prepare('select distinct language from `languages`');
                            $languages->execute();
                            $languageArray = $languages->fetchall(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            Util::fatal('Error while listing languages: ' . $e->getMessage());
                        }
                        foreach ($languageArray as $lang) {
                            echo '<option value="' . $lang['language'] . '" ';
                            if (in_array($lang['language'], $medium->getAudio())) {
                                echo 'selected';
                            }
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
                            if (in_array($lang['language'], $medium->getSubs())) {
                                echo 'selected';
                            }
                            echo '>' . $lang['language'] . '</option>' . "\n";
                        }
                        ?>
                    </select>
                </td></tr>
            <tr><td></td><td></td></tr>
            <tr><td colspan="2"><hr /></td></tr>
            <tr><td colspan="2"><center><input type="submit" value="Mettre à jour" /></center></td></tr>
        </table>
    </form>

    <br /><br />
    <a href=".">Retour à la page principale</a>

    <?php
} else {
    // Return to home page if no medium is specified
    Util::gotoMainPage();
}
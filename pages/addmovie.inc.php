<?php
/**
 * addmovie.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the content for the movie creation page.
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
?>
<h3>Création d'un nouveau film</h3><?php //'    ?>
<form action="scripts/docreatemovie.php" method="POST">
    <input type="hidden" name="id_movie" value="<?php echo $id_movie; ?>" />
    <table>
        <tr><td>Titre&nbsp;:</td><td><input type="text" name="title" /></td></tr>
        <tr><td>Année&nbsp;:</td><td><input type="text" name="year" /></td></tr>
        <tr><td>Réalisateur(s)&nbsp;:</td><td>
                <select name="makers[]" multiple>
                    <?php
                    $conn = Util::getDbConnection();
                    try {
                        $persons = $conn->prepare('SELECT id_person, name FROM `persons` order by name');
                        $persons->execute();
                        $personArray = $persons->fetchall(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        Util::fatal('Error while listing all persons: ' . $e->getMessage());
                    }
                    foreach ($personArray as $person) {
                        echo '<option value="' . $person['id_person'] . '">' . $person['name'] . '</option>' . "\n";
                    }
                    ?>
                </select>
            </td></tr>
        <tr><td>Acteur(s)&nbsp;:</td><td>
                <select name="actors[]" multiple>
                    <?php
                    foreach ($personArray as $person) {
                        echo '<option value="' . $person['id_person'] . '">' . $person['name'] . '</option>' . "\n";
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
                        Util::fatal('Error while listing categories: ' . $e->getMessage());
                    }
                    foreach ($catArray as $cat) {
                        echo '<option value="' . $cat['category'] . '"';
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
                        Util::fatal('Error while listing shortlists: ' . $e->getMessage());
                    }
                    foreach ($slArray as $sl) {
                        echo '<option value="' . $sl['id_shortlist'] . '"';
                        echo '>' . $sl['listname'] . '</option>' . "\n";
                    }
                    ?>
                </select>
            </td></tr>
        <tr><td colspan="2"><hr /></td></tr>
        <tr><td>Note sur 5&nbsp;:</td><td><input type="text" name="rating" /></td></tr>
        <tr><td>Vu le (jj/mm/aaaa)&nbsp;:</td><td><input type="text" name="lastseen" /></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td colspan="2"><hr /></td></tr>
        <tr><td colspan="2"><center><input type="submit" value="Créer le film" /></center></td></tr>
    </table>
</form>

<br /><br />
<a href=".">Retour à la page principale</a>
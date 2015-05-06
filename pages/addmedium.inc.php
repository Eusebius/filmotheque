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
    Copyright (C) 2012-2013 Eusebius (eusebius@eusebius.fr)

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

require_once('includes/required.inc.php');

if (isset($_GET['id_movie']) && $_GET['id_movie'] != '') {

  if((string)(int)$_GET['id_movie'] == $_GET['id_movie']) {
    $id_movie = (int)$_GET['id_movie'];
  }
  else {
  // Return to home page if movie ID is not a number
    gotoMainPage();
  }
  
  $movie = getMovieInSession($id_movie);

  ?>
<h3>Création d'un support pour le film numéro <?php echo $id_movie; //' ?></h3>
<form action="docreatemedium.php" method="POST">
  <input type="hidden" name="id_movie" value="<?php echo $id_movie; ?>" />
  <table>
  <tr><td>Titre du film&nbsp;:</td><td><?php echo $movie->getTitle(); ?></td></tr>
  <tr><td>Type&nbsp;:</td><td>
  <select name="type">
  <?php
  $conn = db_ensure_connected();
  $types = $conn->prepare('select distinct type from `media`');
  $types->execute();
  $typeArray = $types->fetchall(PDO::FETCH_ASSOC);
  foreach ($typeArray as $type) {
    if ($type['type'] != '') {
      echo '<option value="' . $type['type'] . '" ';
      echo '>' . $type['type'] . '</option>' . "\n";
    }
  }
  ?>
  </select>
  </td></tr>
  <tr><td>Largeur en pixels&nbsp;:</td><td><input type="text" name="width" /></td></tr>
  <tr><td>Hauteur en pixels&nbsp;:</td><td><input type="text" name="height" /></td></tr>
  <tr><td>Commentaires&nbsp;:</td><td><input type="text" name="comment" /></td></tr>
  <?php

    $conn2 = db_ensure_connected();
    $next = $conn2->prepare('SELECT shelfmark+1 next FROM `media` m WHERE not exists (select shelfmark from media where media.shelfmark = m.shelfmark+1) and m.shelfmark is not null order by next limit 1');
    $next->execute();
    if ($next->rowCount() == 0) {
      fatal('Impossible de trouver la prochaine cote disponible');
    }
    $nextArray = $next->fetchall(PDO::FETCH_ASSOC);
    $nextShelfmark = $nextArray[0]['next'];
  ?>
  <tr><td>Cote&nbsp;:</td><td><input type="text" name="shelfmark" value="<?php echo $nextShelfmark; ?>" /></td>
  <!-- <td>(première cote disponible&nbsp;: <?php echo $nextShelfmark; ?>)</td></tr> -->
  <tr><td>Pistes audio&nbsp;:</td><td>
  <select name="audio[]" multiple>
  <?php
  $languages = $conn->prepare('select distinct language from `languages`');
  $languages->execute();
  $languageArray = $languages->fetchall(PDO::FETCH_ASSOC);
  foreach ($languageArray as $lang) {
    echo '<option value="' . $lang['language'] . '" ';
    echo '>' . $lang['language'] . '</option>' . "\n";
  } ?>
  </select>
  </td></tr>
  <tr><td>Sous-titres&nbsp;:</td><td>
  <select name="subs[]" multiple>
  <?php
  foreach ($languageArray as $lang) {
    echo '<option value="' . $lang['language'] . '" ';
    echo '>' . $lang['language'] . '</option>' . "\n";
  } ?>
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
}
else {
  // Return to home page if no movie is specified
  gotoMainPage();
}
<?php
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

  $_SESSION['movie'] = new Movie($id_movie);
  $movie = $_SESSION['movie'];

  $conn = db_ensure_connected();

  echo '<h2>Détails d\'un film</h2>' . "\n";

  echo '<table><tr><td>';
  $cover = $movie->getCoverFileName();
  if (file_exists($cover)) {
    echo '<img src="' . $cover . '" />';
  }
  echo '</td><td>';

  echo '<table border="1"><tr><th colspan="2" align="center">'
    . $movie->getTitle() . '</th></tr>' . "\n";

  $originalTitle = $movie->getOriginalTitle();
  if ($originalTitle != '') {
    echo '<tr><td colspan="2" align="center"><em>'
      . $movie->getOriginalTitle() . '</em></td></tr>' . "\n";
  }

  echo '<tr><td>Année&nbsp;:</td><td>' . $movie->getYear() . '</td></tr>' . "\n" ;

  echo '<tr><td>Réalisateur(s)&nbsp;:</td><td>';
  $makers = $movie->getMakers();
  $nMakers = count($makers);
  if ($nMakers > 0) {
    echo $makers[0];
  }
  for ($i = 1; $i < $nMakers; $i++) {
    echo ', ' . $makers[$i];
  }
  echo "</td></tr>\n";

  echo '<tr><td>Catégories&nbsp;:</td><td>';
  $categories = $movie->getCategories();
  $ncat = count($categories);
  if ($ncat > 0) {
    echo $categories[0];
  }
  for ($i = 1; $i < $ncat; $i++) {
    echo ', ' . $categories[$i];
  }
  echo "</td></tr>\n";

  echo '<tr><td>Acteurs&nbsp;:</td><td>';
  $actors = $movie->getActors();
  $nActors = count($actors);
  if ($nActors > 0) {
    echo $actors[0];
  }
  for ($i = 1; $i < $nActors; $i++) {
    echo ', ' . $actors[$i];
  }
  echo "</td></tr>\n";

  echo '<tr><td colspan="2">&nbsp;</td></tr>' . "\n" ;

  echo '<tr><td>Note sur 5&nbsp;:</td><td>' . $movie->getRating() . '</td></tr>' . "\n" ;

  echo '<tr><td>Shortlists&nbsp;:</td><td>';
  $shortlistArray = $movie->getShortlists();
  $nLists = count($shortlistArray);
  if ($nLists > 0) {
    echo $shortlistArray[0];
  }
  for ($i = 1; $i < $nLists; $i++) {
    echo ', ' . $shortlistArray[$i];
  }
  echo "</td></tr>\n";

  echo '<tr><td>Vu le&nbsp;:</td><td>';
  echo $movie->getFormattedLastseen();
  echo '</td></tr>' . "\n" ;
  echo '</table>' . "\n";
 
  echo '</td></tr></table>';

  echo "<br /><br />\n";
  echo '<a href="?page=updatemovie&id_movie=' . $id_movie . '">Mettre à jour la fiche du film</a>';
  echo "<br /><br />\n";
  if ($movie->getIMDbID() == '') {
    echo '<a href="?page=getimdb&id_movie=' . $id_movie . '">Lier à une fiche IMDb</a>';
    echo "<br /><br />\n";
  }
  echo '<a href="dodeletemovie.php?id_movie=' . $id_movie . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ' . $movie->getTitle() . ' ?\')"><font face="red"><strong>!!! - Supprimer le film</strong></font></a>';

  // Fetching corresponding media
  $movie->retrieveMedia();
  $mediaArray = $movie->getMedia();

  echo '<h3>Supports correspondants</h3>';
  
  // TODO à faire rentrer dans la classe Medium
  $getMediaBorrowers = $conn->prepare('select * from `media-borrowers` natural join borrowers where id_medium = ? and backdate is null');

  echo '<p><a href="?page=addmedium&id_movie=' 
    . $id_movie . '">Ajouter un nouveau support</a></p>';

  echo '<table border="1">'
    . '<tr><th align="center">Cote</th>'
    . '<th align="center">Type</th>'
    . '<th align="center">Dimensions</th>'
    . '<th align="center">Langues audio</th>'
    . '<th align="center">Langues sous-titres</th>'
    . '<th align="center">Commentaires</th>'
    . '<th align="center">Emprunté par</th></tr>'
    . "\n";

  foreach ($mediaArray as $medium) {
    $quality=$medium->getQuality();
    echo '<tr>';
    echo '<td align="center" bgcolor="'.$colour[$quality].'">' . $medium->getShelfmark() . '</td>';
    echo '<td align="center" bgcolor="'.$colour[$quality].'">' . $medium->getType() . '</td>';
    echo '<td align="center" bgcolor="'.$colour[$quality].'">';
    echo ($medium->getWidth() == '' ? '?' : $medium->getWidth());
    echo '&nbsp;x&nbsp;';
    echo ($medium->getHeight() == '' ? '?' : $medium->getHeight());
    echo '</td>';
    echo '<td align="center" bgcolor="'.$colour[$quality].'">';
    $audioArray = $medium->getAudio();
    $nAudio = count($audioArray);
    if ($nAudio > 0) {
    echo $audioArray[0];
    }
    for ($i = 1; $i < $nAudio; $i++) {
      echo ', ' . $audioArray[$i];
    }
    echo '</td>';
    echo '<td align="center" bgcolor="'.$colour[$quality].'">';
    $subArray = $medium->getSubs();
    $nSubs = count($subArray);
    if ($nSubs > 0) {
    echo $subArray[0];
    }
    for ($i = 1; $i < $nSubs; $i++) {
      echo ', ' . $subArray[$i];
    }
    echo '</td>';
    echo '<td align="center" bgcolor="'.$colour[$quality].'">' . $medium->getComment() . '</td>';
    echo '<td align="center" bgcolor="'.$colour[$quality].'">';
    $getMediaBorrowers->execute(array($medium->getID()));
    $borrowerArray = $getMediaBorrowers->fetchall(PDO::FETCH_ASSOC);
    if (count($borrowerArray) > 0 ) {
      echo $borrowerArray[0]['borrowername'] . ', le ';
      $date = DateTime::createFromFormat('Y-m-d', $borrowerArray[0]['loandate']);
      echo $date->format('d/m/Y');
    }
    echo '</td>';
    echo '<td bgcolor="'.$colour[$quality].'"><a href="?page=updatemedium&id_medium=' . $medium->getID() . '">'
      . 'Mettre à jour le support'
      . '</a></td>';
    echo '<td bgcolor="'.$colour[$quality].'"><a href="dodeletemedium.php?id_medium=' . $medium->getID() . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce support ?\')">'
      . '<font color="red">Supprimer le support</font>'
      . '</a></td>';
    //echo '<td>' . $quality . '</td>';
    echo "</tr>\n";
  }

  echo "</table>\n";
  
  echo "<br /><br />\n"
    . '<a href=".">Retour à la page principale</a>';


}
else {
  // Return to home page if no movie is specified
  gotoMainPage();
}

?>
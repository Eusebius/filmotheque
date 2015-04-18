<?php
/**
 * getimdb.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @version 0.2.4
 * 
 * This is the content for the page proposing a match on imdb for a movie.
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

  $movie = new Movie($id_movie);

  $conn = db_ensure_connected();

  echo '<h2>' . $movie->getTitle() . " - lier à une fiche IMDb</h2>\n";

  $xml = new DomDocument();
  $imdb_id = '';
  if (isset($_GET['imdb_id']) && $_GET['imdb_id'] != '') {
    $imdb_id = $_GET['imdb_id'];
    $xml->load('http://myapifilms.com/imdb?idIMDB='. $imdb_id .'&format=XML');
    $item = $xml->getElementsByTagName('movie')->item(0);    
  }
  else {
    $xml->load('http://myapifilms.com/title?title='. $movie->getTitle() .'&format=XML&limit=1');
    $item = $xml->getElementsByTagName('movie')->item(0);
  }
  
  //print_r($xml);

  //debug$xml->saveXML());

  if ($item != null) {
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
    $genres = $genres->getElementsByTagName('genre');
    foreach ($genres as $genre) {
      echo $genre->nodeValue . ' ';
    }
?>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <form action="dolinkimdb.php" method="POST">
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
  }
  else {
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
}
else {
  // Return to home page if no movie is specified
  gotoMainPage();
}

?>
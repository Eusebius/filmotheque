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

// Remember GET parameters
$sortParameters='';
$filterParameters='';

if (isset($_GET['sort'])) {
  if ($_GET['sort'] == 'year') {
    $sortby = 'year';
    $sortParameters='sort=year&';
  }
  else if ($_GET['sort'] == 'rating') {
    $sortby = 'rating';
    $sortParameters='sort=rating&';
  }
  else if ($_GET['sort'] == 'lastseen') {
    $sortby = 'lastseen';
    $sortParameters='sort=lastseen&';
  }
  else {
    $sortby = 'title';
    $sortParameters='sort=title&';
  }
  
  if (isset($_GET['order'])) {
    if ($_GET['order'] == 'desc') {
      $order = 'desc';
      $sortParameters .= 'order=desc&';
    }
    else {
      $order = 'asc';
      $sortParameters .= 'order=asc&';
    }
  }
}
else {
  $sortby='title';
  $order='asc';
}

$conn = db_ensure_connected();

$getAllShortlists = $conn->prepare('select id_shortlist, listname from `shortlists` order by listname asc');
$getAllShortlists->execute();
$listArray = $getAllShortlists->fetchall(PDO::FETCH_ASSOC);

?>

<h2>Liste des films</h2>
<table>
  <tr>
    <td>
      Afficher uniquement les shortlists suivantes&nbsp;:<br />
      <form action="" method="GET">
      <?php 

      /*
      echo '<input type="hidden" name="filterbyshortlist" value="1" />';
      if (isset($_GET['filterbyshortlist']) && ($_GET['filterbyshortlist'] == '1')) {
	$filterParameters = "filterbyshortlist=1&";
      }
      */

      $shortlistFilter=array();

      makeHiddenParameters($sortParameters);
      foreach ($listArray as $list) {
	$id = isIntString($list['id_shortlist']);
	if ($id != false) {
	  $listn = 'list' . $id;
	  echo '<input type="checkbox" name="' . $listn . '" value="1"';
	  if (isset($_GET[$listn]) && $_GET[$listn] == '1') {
	    echo ' checked="checked"';
	    $filterParameters .= $listn . '=1&';
	    array_push($shortlistFilter, $id);
	  }
	  echo ' />&nbsp;';
	  echo $list['listname'] . "<br />\n";
	}
      }
      ?>
      <input type="submit" value="Filtrer"/>
      </form>
    </td>
  </tr>
</table>

<?php

$shortlistJoin='';
$n = count($shortlistFilter);
if ($n > 0) {
  $shortlistJoin = "join `movies-shortlists` on movies.id_movie=`movies-shortlists`.id_movie join shortlists on `movies-shortlists`.id_shortlist=shortlists.id_shortlist where shortlists.id_shortlist = '" . $shortlistFilter[0] . "'";
  for ($i = 1; $i < $n; $i++) {
    $shortlistJoin .= " or shortlists.id_shortlist = '" . $shortlistFilter[$i] . "'";
  }
}

$listMovies = $conn->prepare('select movies.id_movie, title, year, originaltitle, imdb_id, rating, lastseen from movies left outer join experience on movies.id_movie = experience.id_movie ' . $shortlistJoin . ' order by ' . $sortby . ' ' . $order);
$getCategoriesByMovie = $conn->prepare('select id_movie, category from `movies-categories` where id_movie = ?');
$getShortlistsByMovie = $conn->prepare('select id_movie, listname from `movies-shortlists` natural join shortlists where id_movie = ?');

$listMovies->execute();
//echo $listMovies->queryString . '<br>';
//print_r($listMovies->errorInfo());
$movieArray = $listMovies->fetchall(PDO::FETCH_ASSOC);
$nMovies = $listMovies->rowCount();
?>

<p><em><?php echo $nMovies ?>&nbsp;films distincts</em></p>

<p>
<a href="?<?php echo $sortParameters; ?>">Réinitialiser les filtres</a><br />
<a href="?page=addmovie">Ajouter un nouveau film</a>
</p>

<table border="1">
<tr>
<th>Titre&nbsp;<a href="index.php?<?php echo $filterParameters; ?>sort=title&order=asc">⇧</a><a href="index.php?<?php echo $filterParameters; ?>sort=title&order=desc">⇩</a></th>
<th>Année&nbsp;<a href="index.php?<?php echo $filterParameters; ?>sort=year&order=asc">⇧</a><a href="index.php?<?php echo $filterParameters; ?>sort=year&order=desc">⇩</a></th>
<th>Catégories</th>
<th>Note&nbsp;<a href="index.php?<?php echo $filterParameters; ?>sort=rating&order=asc">⇧</a><a href="index.php?<?php echo $filterParameters; ?>sort=rating&order=desc">⇩</a></th>
<th>Shortlists</th>
<th>Vu le&nbsp;<a href="index.php?<?php echo $filterParameters; ?>sort=lastseen&order=asc">⇧</a><a href="index.php?<?php echo $filterParameters; ?>sort=lastseen&order=desc">⇩</a></th>
</tr>

<?php

foreach($movieArray as $movie) {

  echo "<tr>\n";
  echo '<td><a href="?page=moviedetails&id_movie=' . $movie['id_movie'] . '">'
    .  $movie['title']
    . "</a></td>\n";
  echo '<td align="center">'
    .  $movie['year']
    . "</td>\n";
  echo '<td>';
  $getCategoriesByMovie->execute(array($movie['id_movie']));
  $categoryArray = $getCategoriesByMovie->fetchall(PDO::FETCH_ASSOC);
  $ncat = count($categoryArray);
  if ($ncat > 0) {
    echo $categoryArray[0]['category'];
  }
  for ($i = 1; $i < $ncat; $i++) {
    echo ', ' . $categoryArray[$i]['category'];
  }
  echo "</td>\n";
  echo '<td align="center">'
    .  $movie['rating']
    . "</td>\n";
  echo '<td>';
  $getShortlistsByMovie->execute(array($movie['id_movie']));
  $ShortlistArray = $getShortlistsByMovie->fetchall(PDO::FETCH_ASSOC);
  $nsl = count($ShortlistArray);
  if ($nsl > 0) {
    echo $ShortlistArray[0]['listname'];
  }
  for ($i = 1; $i < $nsl; $i++) {
    echo ', ' . $ShortlistArray[$i]['listname'];
  }
  echo "</td>\n";
  echo '<td align="center">';
  if($movie['lastseen'] != '') {
    $date = DateTime::createFromFormat('Y-m-d', $movie['lastseen']);
    echo $date->format('d/m/Y');
  }
  echo "</td>\n";
  if($movie['imdb_id'] == '') {
    echo '<td align="center">';
    echo '<a href="?page=getimdb&id_movie=' . $movie['id_movie'] . '">Lier à une fiche IMDb</a>';
    echo "</td>\n";
  }

  echo "</tr>\n";

}

?>
</table>
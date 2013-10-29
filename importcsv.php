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

$csvfilename = 'films.csv';
/* 
   Imposed column format:
   [0] - Shelfmark
   [1] - Title
   [2] - Category
   [3] - Rating
   [4] - Height
   [5] - Year
   [6] - Maker
   [7] - Actor 1
   [8] - Actor 2
   [9] - VO
   [10] - VF
   [11] - EN subtitles
   [12] - FR subtitles
   [13] - Type
   [14] - Comment
   [15] - Shortlists
   [16] - Lastseen
 */

$pdoconn = db_ensure_connected();

$csvfile = fopen($csvfilename, 'r');
$entrieswithcomments = fopen('entrieswithcomments.csv', 'w');

//Start of the confirmation form
echo '<form action="confirmcsv.php" method="POST">' . "\n";
$id=0 ; //Identifier for the fields of each conflict

//discard first line
fgets($csvfile);

//Insert movies
while ($line = fgets($csvfile)) {
  $columns = explode("\t", $line);
  $entry['shelfmark'] = $columns[0];
  $entry['title'] = $columns[1];
  $entry['category'] = $columns[2];
  $entry['rating'] = $columns[3];
  $entry['height'] = $columns[4];
  $entry['year'] = $columns[5];
  $entry['maker'] = $columns[6];
  $entry['actor1'] = $columns[7];
  $entry['actor2'] = $columns[8];
  $entry['VO'] = $columns[9];
  $entry['VF'] = $columns[10];
  $entry['sten'] = $columns[11];
  $entry['stfr'] = $columns[12];
  $entry['type'] = $columns[13];
  $entry['comment'] = $columns[14];
  $entry['shortlist'] = $columns[15];
  $entry['lastseen'] = $columns[16];

  $getMovieByTitle = $pdoconn->prepare('select * from `movies` where `title`=?');
  $insertMovie = $pdoconn->prepare('insert into `movies` (`title`) values (?)');
  $insertMovieWithYear = $pdoconn->prepare('insert into `movies` (`title`, `year`) values (?, ?)');
  
  if ($entry['comment'] == '') {
    if ($entry['title'] != '') { // ignore lines without titles

      $getMovieByTitle->execute(array($entry['title']));
      if ($getMovieByTitle->rowCount() > 0) {
	$entryArray = $getMovieByTitle->fetchall(PDO::FETCH_ASSOC);
	foreach($entryArray as $movieEntry) {
	  //print_r($entryArray);
	  if (isset($movieEntry['year'])) { 
	    if (isset($entry['year']) and $entry['year'] != '') {
	      // if movie exists in the database with the exact same year, do nothing
	      if ($movieEntry['year'] != $entry['year']) {
		//Movie exists, two different years
		echo '<table border="1" borderwidth="1"><tr><th colspan="4">Movie already in the database, with a different year:</th></tr>' . "\n";
		echo '<tr><td><input type="hidden" '
		  . 'name="' . $id . '" '
		  . 'value="' . $movieEntry['id_movie'] . '"/>' 
		  . $entry['title'] . '</td>'
		  . '<td><input type="radio" name="' . $id . '-movieconflict" value="db"/>Merge to date in database (' 
		  . $movieEntry['year'] .')</td>'
		  . '<td><input type="radio" name="' . $id . '-movieconflict" value="csv"/>Merge to date in file (' 
		  . $entry['year'] .')</td>'
		  . '<td><input type="radio" name="' . $id . '-movieconflict" value="create"/>Create distinct entry</td><tr>';
		echo '</table><br />' . "\n";
		$id++;
	      }
	    } else {
	      //Movie exists with a year, year not specified in CSV
		echo '<table border="1" borderwidth="1"><tr><th colspan="4">Movie already in the database, year is specified in the database but not in the file:</th></tr>' . "\n";
		echo '<tr><td><input type="hidden" '
		  . 'name="' . $id . '" '
		  . 'value="' . $movieEntry['id_movie'] . '"/>' 
		  . $entry['title'] . '</td>'
		  . '<td><input type="radio" name="' . $id . '-movieconflict" value="db"/>Merge to date in database (' 
		  . $movieEntry['year'] .')</td>'
		  . '<td><input type="radio" name="' . $id . '-movieconflict" value="create"/>Create distinct entry</td><tr>';
		echo '</table><br />' . "\n";
		$id++;
	    }
	  } else {
	    //echo 'Movie exists without a registered year ('.$entry['title'].', '.$entry['year'].').<br />' . "\n"; 
	    if (isset($entry['year']) and $entry['year'] != '') {
	      //TODO Movie exists without a year, year specified in CSV
		echo '<table border="1" borderwidth="1"><tr><th colspan="4">Movie already in the database, year specified in the file but not in the database:</th></tr>' . "\n";
		echo '<tr><td><input type="hidden" '
		  . 'name="' . $id . '" '
		  . 'value="' . $movieEntry['id_movie'] . '"/>' 
		  . $entry['title'] . '</td>'
		  . '<td><input type="radio" name="' . $id . '-movieconflict" value="csv"/>Merge to date in file (' 
		  . $entry['year'] .')</td>'
		  . '<td><input type="radio" name="' . $id . '-movieconflict" value="create"/>Create distinct entry</td><tr>';
		echo '</table><br />' . "\n";
		$id++;
	    } else {
	      //TODO Movie exists without a year, year not specified in CSV
		echo '<table border="1" borderwidth="1"><tr><th colspan="4">Movie already in the database, year specified neither in the file nor in the database:</th></tr>' . "\n";
		echo '<tr><td><input type="hidden" '
		  . 'name="' . $id . '" '
		  . 'value="' . $movieEntry['id_movie'] . '"/>' 
		  . $entry['title'] . '</td>'
		  . '<td><input type="radio" name="' . $id . '-movieconflict" value="csv"/>Merge to the database entry</td>'
		  . '<td><input type="radio" name="' . $id . '-movieconflict" value="create"/>Create distinct entry</td><tr>';
		echo '</table><br />' . "\n";
		$id++;
	    }
	  }
	}
      } else {
	if ($entry['year'] != '') {
	  $insertMovieWithYear->execute(array($entry['title'],$entry['year']));
	} else {
	  $insertMovie->execute(array($entry['title']));
	}
	echo 'Movie added ('.$entry['title'].').<br />' . "\n";
      }
    }
  }
  else { // store entries with comments for later
    fwrite($entrieswithcomments,$line);
  }
  
  //echo $line;
  //echo '<br />';
  
}

// End of confirmation form
echo '<input type="submit" value="Confirm" /></form>' . "\n";

// ...

fclose($entrieswithcomments);
fclose($csvfile);

?>
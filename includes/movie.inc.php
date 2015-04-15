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

class Movie {

  private $id_movie;
  private $title;
  private $year;
  private $makers;
  private $makersID;
  private $actors;
  private $actorsID;
  private $categories;
  private $shortlists;
  private $shortlistsID;
  private $rating;
  private $lastseen;
  private $originaltitle;
  private $imdb_id;

  // Array of references to medium objects
  private $media;

  public function Movie($id_movie, $withPeople=true, $withCategories=true, $withShortlists=true) {
    $this->imdb_id = '';
    $this->originaltitle = '';
    if ($id_movie == null) {
      $this->id_movie = null;
    }
    else {
      $this->id_movie = $id_movie;
      $this->updateAll($withPeople, $withCategories, $withShortlists);
    }
  }

  // Deletes the medium and all associated information from the base
  public function delete() {
    if ($this->id_movie != null) {
      $conn = db_ensure_connected();
      $delMovie=$conn->prepare('delete from `movies` where `id_movie`=?');
      if (!$delMovie->execute(array($this->id_movie))) {
	td($delMovie->errorInfo());
	if ($_SESSION['debug']) {
	  die();
	}
      }
    }
  }

  public function dump() {
    if ($_SESSION['debug']) {
	echo "<pre>\n";
	print_r($this);
	echo "\n</pre>\n";
      }
  }

  public function setFieldAndWait($field, $value) {
    if ($field == 'imdb_id') {
      $this->imdb_id = $value;
    }
    else if ($field == 'year') {
      $this->year = $value;
    }
    else if ($field == 'originaltitle') {
      $this->originaltitle = $value;
    }
  }

  public function setValues($title, $year, $makers, $actors, $categories, $shortlists, $rating, $lastseen) {
    $this->title = ($title != null ? $title : '');
    $this->year = ($year != null ? $year : '');
    $this->makersID = ($makers != null ? $makers : array());
    $this->actorsID = ($actors != null ? $actors : array());
    $this->categories = ($categories != null ? $categories : array());
    $this->shortlistsID = ($shortlists != null ? $shortlists : array());
    $this->rating = ($rating != null ? $rating : '');
    $this->lastseen = ($lastseen != null ? $this->unformatDate($lastseen) : null);

    $this->writeAll();
    $this->updateMakers();
    $this->updateActors();
    $this->updateShortlists();
  }

  public function setLastSeen($lastseen) {
      $this->lastseen = ($lastseen != null ? $this->unformatDate($lastseen) : null);

      $conn = db_ensure_connected();
      $conn->beginTransaction();
      
      if ($this->rating != null && $this->rating != '') {
          $setLastSeen = $conn->prepare('update experience set lastseen=? where id_movie=?');
          $setLastSeen->execute(array($this->lastseen, $this->id_movie));
      } else {
          $setLastSeen = $conn->prepare('insert into experience (lastseen, id_movie) values(?, ?)');
          $setLastSeen->execute(array($this->lastseen, $this->id_movie));
      }

      $conn->commit();
      
  }

  public function writeAll() {
    $conn = db_ensure_connected();

    $conn->beginTransaction();

    // Ensure that the tuple exists in movies, create or update it
    $checkMovie = $conn->prepare('select id_movie from movies where id_movie = ?');
    $checkMovie->execute(array($this->id_movie));
    if ($checkMovie->rowCount() == 0) {
      $insertMovie = $conn->prepare('insert into movies (title, year, imdb_id, originaltitle) values (?, ?, ?, ?)');
      $insertMovie->execute(array($this->title, ($this->year != ''? $this->year : null), ($this->imdb_id != ''? $this->imdb_id : null), ($this->originaltitle != ''? $this->originaltitle : null)));
      $this->id_movie = $conn->lastInsertId();
    }
    else {
      $updateMovies = $conn->prepare('update movies set title=?, year=?, imdb_id=?, originaltitle=? where id_movie=?');
      $updateMovies->execute(array($this->title, ($this->year != ''? $this->year : null), ($this->imdb_id != ''? $this->imdb_id : null), ($this->originaltitle != ''? $this->originaltitle : null), $this->id_movie));
    }

    $deleteMakers = $conn->prepare('delete from `movies-makers` where id_movie = ?');
    $deleteMakers->execute(array($this->id_movie));
    foreach ($this->makersID as $makerID) {
      $insertMakers = $conn->prepare('insert into `movies-makers` (id_movie, id_person) values (?, ?)');
      $insertMakers->execute(array($this->id_movie, $makerID));
    }

    $deleteActors = $conn->prepare('delete from `movies-actors` where id_movie = ?');
    $deleteActors->execute(array($this->id_movie));
    foreach ($this->actorsID as $actorID) {
      $insertActors = $conn->prepare('insert into `movies-actors` (id_movie, id_person) values (?, ?)');
      $insertActors->execute(array($this->id_movie, $actorID));
    }

    $deleteCategories = $conn->prepare('delete from `movies-categories` where id_movie = ?');
    $deleteCategories->execute(array($this->id_movie));
    foreach ($this->categories as $category) {
      $insertCategories = $conn->prepare('insert into `movies-categories` (id_movie, category) values (?, ?)');
      $insertCategories->execute(array($this->id_movie, $category));
    }

    $deleteShortlists = $conn->prepare('delete from `movies-shortlists` where id_movie = ?');
    $deleteShortlists->execute(array($this->id_movie));
    foreach ($this->shortlistsID as $id_shortlist) {
      $insertShortlists = $conn->prepare('insert into `movies-shortlists` (id_movie, id_shortlist) values (?, ?)');
      $insertShortlists->execute(array($this->id_movie, $id_shortlist));
    }

    $deleteExperience = $conn->prepare('delete from experience where id_movie = ?');
    $deleteExperience->execute(array($this->id_movie));
    $updateExperience = $conn->prepare('insert into experience (rating, lastseen, id_movie) values (?, ?, ?)');
    $updateExperience->execute(array(($this->rating != ''? $this->rating : null), ($this->lastseen != ''? $this->lastseen : null), $this->id_movie));

    $conn->commit();
  }

  public function getID() {
    return $this->id_movie;
  }

  public function getIMDbID() {
    return $this->imdb_id;
  }

  public function getTitle() {
    return $this->title;
  }

  public function getOriginalTitle() {
    return $this->originaltitle;
  }

  public function getYear() {
    return $this->year;
  }

  public function getMakers() {
    $r = array();
    foreach ($this->makers as $maker) {
      array_push($r, $maker);
    }
    return $r;
  }

  public function getActors() {
    $r = array();
    foreach ($this->actors as $actor) {
      array_push($r, $actor);
    }
    return $r;
  }

  public function getCategories() {
    $r = array();
    foreach ($this->categories as $cat) {
      array_push($r, $cat);
    }
    return $r;
  }

  public function getShortlists() {
    $r = array();
    foreach ($this->shortlists as $sl) {
      array_push($r, $sl);
    }
    return $r;
  }

  public function getRating() {
    return $this->rating;
  }

  public function getCoverFileName() {
    return 'covers/' . $this->id_movie . '.jpg';
  }

  public function getLastseen() {
    return $this->lastseen;
  }

  public function getFormattedLastseen($format='d/m/Y') {
    if($this->lastseen != '') {
      $date = DateTime::createFromFormat('Y-m-d', $this->lastseen);
      return $date->format('d/m/Y');
    }
    else {
      return '';
    }
  }

  public function unformatDate($date) {
    $date2 = DateTime::createFromFormat('d/m/Y', $date);
    return $date2->format('Y-m-d');
  }

  public function updateMakers() {
    $this->makers = array();
    $this->makersID = array();
    $conn = db_ensure_connected();
    $getMakers = $conn->prepare('select id_person, name from `movies-makers` natural join persons where id_movie = ?');
    $getMakers->execute(array($this->id_movie));
    $makerArray = $getMakers->fetchall(PDO::FETCH_ASSOC);
    foreach ($makerArray as $maker) {
      array_push($this->makers, $maker['name']);
      array_push($this->makersID, $maker['id_person']);
    }
  }

  public function updateActors() {
    $this->actors = array();
    $this->actorsID = array();
    $conn = db_ensure_connected();
    $getActors = $conn->prepare('select id_person, name from `movies-actors` natural join persons where id_movie = ?');
    $getActors->execute(array($this->id_movie));
    $actorArray = $getActors->fetchall(PDO::FETCH_ASSOC);
    foreach ($actorArray as $actor) {
      array_push($this->actors, $actor['name']);
      array_push($this->actorsID, $actor['id_person']);
    }
  }

  public function updateCategories() {
    $this->categories = array();
    $conn = db_ensure_connected();
    $getCategories = $conn->prepare('select category from `movies-categories` where id_movie = ?');
    $getCategories->execute(array($this->id_movie));
    $categoryArray = $getCategories->fetchall(PDO::FETCH_ASSOC);
    foreach ($categoryArray as $category) {
      array_push($this->categories, $category['category']);
    }
  }

  public function updateShortlists() {
    $this->shortlists = array();
    $this->shortlistsID = array();
    $conn = db_ensure_connected();
    $getShortlists = $conn->prepare('select id_shortlist, listname from `movies-shortlists` natural join shortlists where id_movie = ?');
    $getShortlists->execute(array($this->id_movie));
    $shortlistArray = $getShortlists->fetchall(PDO::FETCH_ASSOC);
    foreach ($shortlistArray as $shortlist) {
      array_push($this->shortlists, $shortlist['listname']);
      array_push($this->shortlistsID, $shortlist['id_shortlist']);
    }
  }

  public function updateAll($withPeople=true, $withCategories=true, $withShortlists=true) {
    $conn = db_ensure_connected();
    $getMovie=$conn->prepare('select movies.id_movie id_movie, title, year, imdb_id, originaltitle, rating, lastseen from movies left outer join experience on movies.id_movie = experience.id_movie where movies.id_movie = ?');

    $getMovie->execute(array($this->id_movie));
    $nMovies = $getMovie->rowCount();
    if ($nMovies == 0) {
      fatal(
	  "Erreur inattendue : aucun film ne correspond à l'ID $id_movie.<br /><br />"
	  . '<a href=".">Retour à la page principale</a>'
	  );
    }
    if ($nMovies > 1) {
      fatal(
	    "Erreur inattendue : plusieurs films correspondent à l'ID"
	    . "$id_movie.<br /><br />"
	    . '<a href=".">Retour à la page principale</a>'
	    );
    }

    $movieArray = $getMovie->fetchall(PDO::FETCH_ASSOC);
    $this->title = $movieArray[0]['title'];
    $this->year = $movieArray[0]['year'];
    $this->imdb_id = $movieArray[0]['imdb_id'];
    $this->originaltitle = $movieArray[0]['originaltitle'];
    $this->rating = $movieArray[0]['rating'];
    $this->lastseen = $movieArray[0]['lastseen'];

    if ($withPeople) {
      $this->updateMakers();
      $this->updateActors();
    }
    if ($withCategories) {
      $this->updateCategories();
    }
    if ($withShortlists) {
      $this->updateShortlists();
    }
  }

  public function retrieveMedia() {
    $this->media=Array();

    $conn = db_ensure_connected();
    $getMedia=$conn->prepare('select distinct id_medium from media where id_movie = ?');

    $getMedia->execute(array($this->id_movie));
    $mediaArray = $getMedia->fetchall(PDO::FETCH_ASSOC);
    foreach ($mediaArray as $mediumArray) {
      $id_medium = $mediumArray['id_medium'];
      $medium = new Medium($id_medium);
      array_push($this->media, $medium);
    }
  }

  public function getMedia() {
    $media = $this->media;
    return $media;
  }
}

class Medium {

  // Reference to the movie object
  private $movie;

  private $id_medium;
  private $id_movie;
  private $type;
  private $height;
  private $width;
  private $comment;
  private $shelfmark;
  private $quality;

  // Language arrays
  private $audio;
  private $subs;

  //TODO borrowers

  public function Medium($id_medium, $movie=null) {
    if ($id_medium == null) {
      $this->id_medium == null;
    }
    else {
      $this->id_medium = $id_medium;
      $this->updateAll();
      $this->movie = null;
      if ($movie != null) {
	$this->setMovieIfCorrect($movie, true);
      }
    }
  }

  public function getID() {
    return $this->id_medium;
  }

  public function getMovieID() {
    return $this->id_movie;
  }

  public function getType() {
    return $this->type;
  }

  public function getHeight() {
    return $this->height;
  }

  public function getWidth() {
    return $this->width;
  }

  public function getQuality() {
    return $this->quality;
  }

  public function getComment() {
    return $this->comment;
  }

  public function getShelfmark() {
    return $this->shelfmark;
  }

  public function getAudio() {
    $audio = $this->audio;
    return $audio;
  }

  public function getSubs() {
    $subs = $this->subs;
    return $subs;
  }

  // Deletes the medium and all associated information from the base
  public function delete() {
    $conn = db_ensure_connected();
    $delMedium=$conn->prepare('delete from `media` where `id_medium`=?');
    if (!$delMedium->execute(array($this->id_medium))) {
      td($delMedium->errorInfo());
      if ($_SESSION['debug']) {
	die();
      }
    }
  }
  
  // Update all fields but does not sets a movie object
  public function updateAll() {
    $conn = db_ensure_connected();
    $getMedium=$conn->prepare('select media.id_medium, id_movie, type, height, width, comment, shelfmark, quality from media, `media-quality` where media.id_medium = ? and media.id_medium=`media-quality`.id_medium');

    $getMedium->execute(array($this->id_medium));
    $nMedia = $getMedium->rowCount();
    if ($nMedia == 0) {
      fatal(
	  'Erreur inattendue : aucun support ne correspond à l\'ID '.$this->id_medium.'.<br /><br />'
	  . '<a href=".">Retour à la page principale</a>'
	  );
    }
    if ($nMedia > 1) {
      fatal(
	    "Erreur inattendue : plusieurs supports correspondent à l'ID"
	    . "$id_medium.<br /><br />"
	    . '<a href=".">Retour à la page principale</a>'
	    );
    }

    $mediumArray = $getMedium->fetchall(PDO::FETCH_ASSOC);
    $this->id_movie = $mediumArray[0]['id_movie'];
    $this->type = $mediumArray[0]['type'];
    $this->height = $mediumArray[0]['height'];
    $this->width = $mediumArray[0]['width'];
    $this->comment = $mediumArray[0]['comment'];
    $this->shelfmark = $mediumArray[0]['shelfmark'];
    $this->quality = $mediumArray[0]['quality'];

    $this->updateAudio();
    $this->updateSubs();
    
  }

  public function updateAudio() {
    $this->audio = array();
    $conn = db_ensure_connected();
    $getAudio = $conn->prepare('select language from `media-audio` where id_medium = ?');
    $getAudio->execute(array($this->id_medium));
    $audioArray = $getAudio->fetchall(PDO::FETCH_ASSOC);
    foreach ($audioArray as $audio) {
      array_push($this->audio, $audio['language']);
    }
  }

  public function updateSubs() {
    $this->subs = array();
    $conn = db_ensure_connected();
    $getSubs = $conn->prepare('select language from `media-subs` where id_medium = ?');
    $getSubs->execute(array($this->id_medium));
    $subArray = $getSubs->fetchall(PDO::FETCH_ASSOC);
    foreach ($subArray as $sub) {
      array_push($this->subs, $sub['language']);
    }
  }

  public function retrieveMovie() {
    if ($this->movie == null) {
      $this->movie = new Movie($this->id_movie);
    }
  }

  public function getMovie() {
    return $this->movie;
  }

  //Sets the movie, only if its id_movie is the right one
  public function setMovieIfCorrect($movie, $otherwiseRetrieveIt=false) {
    if ($movie->getID() == $this->getMovieID()) {
      $this->movie = $movie;
    }
    else if ($otherwiseRetrieveIt) {
      $this->retrieveMovie();
    }
  }

  public function setValues($type, $height, $width, $comment, $shelfmark, $audio, $subs, $id_movie=null) {
    if ($id_movie != null) {
      $this->id_movie = $id_movie;
    }
    $this->type = ($type != null ? $type : '');
    $this->height = ($height != null ? $height : '');
    $this->width = ($width != null ? $width : '');
    $this->comment = ($comment != null ? $comment : '');
    $this->shelfmark = ($shelfmark != null ? $shelfmark : '');

    $this->audio = ($audio != null ? $audio : array());
    $this->subs = ($subs != null ? $subs : array());

    $this->writeAll();
  }

  public function writeAll() {
    $conn = db_ensure_connected();

    $conn->beginTransaction();

    $checkMedium = $conn->prepare('select id_medium from media where id_medium = ?');
    $checkMedium->execute(array($this->id_medium));
    if ($checkMedium->rowCount() == 0) {
      $insertMedium = $conn->prepare('insert into media (id_movie, type) values (?, ?)');
      $insertMedium->execute(array($this->id_movie, $this->type));
      $this->id_medium = $conn->lastInsertId();
    }
    $updateMovies = $conn->prepare('update media set type=?, height=?, width=?, comment=?, shelfmark=? where id_medium=?');
    $updateMovies->execute(array($this->type, ($this->height != '' ? $this->height : null), ($this->width != '' ? $this->width : null) , $this->comment, ($this->shelfmark != '' ? $this->shelfmark : null), $this->id_medium));

    $deleteAudio = $conn->prepare('delete from `media-audio` where id_medium = ?');
    $deleteAudio->execute(array($this->id_medium));
    foreach ($this->audio as $lang) {
      $insertLang = $conn->prepare('insert into `media-audio` (id_medium, language) values (?, ?)');
      $insertLang->execute(array($this->id_medium, $lang));
    }

    $deleteSubs = $conn->prepare('delete from `media-subs` where id_medium = ?');
    $deleteSubs->execute(array($this->id_medium));
    foreach ($this->subs as $lang) {
      $insertLang = $conn->prepare('insert into `media-subs` (id_medium, language) values (?, ?)');
      $insertLang->execute(array($this->id_medium, $lang));
    }

    $conn->commit();
  }

  public function dump() {
    if ($_SESSION['debug']) {
	echo "<pre>\n";
	print_r($this);
	echo "</pre>\n";
      }
  }
  
}

?>
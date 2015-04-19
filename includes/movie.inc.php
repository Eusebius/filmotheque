<?php

/**
 * includes/movie.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the file defining the Movie and Medium classes.
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

/**
 * Class representing a given movie in the application, and managing its 
 * persistency in the database.
 * 
 * Not every piece of information is populated and up-to-date at a given time.
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
class Movie {

    /**
     * @var \int The unique identifier of the movie in the application.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $id_movie;
    
    /**
     * @var \string The title of the movie (in French).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $title;
    
    /**
     * @var \int The release year of the movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $year;
    
    /**
     * @var \string[] The makers of the movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $makers;
    
    /**
     * @var \int[] The unique identifiers of the makers of the movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $makersID;
    
    /**
     * @var \string[] The actors of the movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $actors;
    
    /**
     * @var \int[] The unique identifiers of the actors of the movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $actorsID;
    
    /**
     * @var \string[] The categories of the movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $categories;
    
    /**
     * @var \string[] The name of the shortlists in which the movie appears.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $shortlists;
    
    /**
     * @var \int[] The unique identifiers of the shortlists in which the movie 
     * appears.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $shortlistsID;
    
    /**
     * @var \int The rating of the movie, from 0 to 5.
     * appears.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $rating;
    
    /**
     * @var \string The date of the last time the movie has been seen (in a 
     * 'yyyy-mm-dd' format).
     * appears.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $lastseen;
    
    /**
     * @var \string The original title of the movie (actually the title in 
     * English).
     * appears.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $originaltitle;
    
    /**
     * @var \string The IMDB identifier to which the movie is linked.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $imdb_id;
    
    /**
     * @var \Medium[] The media attached to this movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $media;

    /**
     * Constructor method for the Movie class. It retrieves all information from
     * the database, on the basis of the provided identifier.
     * 
     * @param \int $id_movie The unique identifier of the movie in the 
     * application.
     * @param \boolean $withPeople Also retrieve the makers and actors attached
     * to the movie.
     * @param \boolean $withCategories Also retrieve the categories attached to
     * the movie.
     * @param \boolean $withShortlists Also retrieve the shortlists in which the
     * movie appears.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function Movie($id_movie, $withPeople = true, $withCategories = true, $withShortlists = true) {
        $this->imdb_id = '';
        $this->originaltitle = '';
        if ($id_movie == null) {
            $this->id_movie = null;
        } else {
            $this->id_movie = $id_movie;
            $this->updateAll($withPeople, $withCategories, $withShortlists);
        }
    }

    /**
     * Delete the movie and all associated information from the database.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function delete() {
        if ($this->id_movie != null) {
            $conn = db_ensure_connected();
            $delMovie = $conn->prepare('delete from `movies` where `id_movie`=?');
            if (!$delMovie->execute(array($this->id_movie))) {
                //What on Earth is this td function?
                td($delMovie->errorInfo());
                if ($_SESSION['debug']) {
                    die();
                }
            }
        }
    }

    /**
     * If in debug mode, print a dump of the Movie object.
     * Otherwise, do nothing.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function dump() {
        if ($_SESSION['debug']) {
            echo "<pre>\n";
            print_r($this);
            echo "\n</pre>\n";
        }
    }

    /**
     * Set a field of the movie object, but do not push it to the database.
     * Implemented only for {@link imdb_id}, {@link year} and 
     * {@link originaltitle}.
     * 
     * @param \string $field The name of the field.
     * @param type $value The value to be used.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function setFieldAndWait($field, $value) {
        if ($field == 'imdb_id') {
            $this->imdb_id = $value;
        } else if ($field == 'year') {
            $this->year = $value;
        } else if ($field == 'originaltitle') {
            $this->originaltitle = $value;
        }
    }

    /**
     * Set the fields of the movie object, and update the database to mirror the
     * changes.
     * 
     * @param \string $title The value for {@link title}
     * @param \int $year The value for {@link year}
     * @param \int[] $makersID The value for {@link makersID}
     * @param \int[] $actorsID The value for {@link actorsID}
     * @param \string[] $categories The value for {@link categories}
     * @param \int[] $shortlistsID The value for {@link shortlistsID}
     * @param \int $rating The value for {@link rating}
     * @param \string $lastseen The value for {@link lastseen}
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function setValues($title, $year, $makersID, $actorsID, $categories, $shortlistsID, $rating, $lastseen) {
        $this->title = ($title != null ? $title : '');
        $this->year = ($year != null ? $year : '');
        $this->makersID = ($makersID != null ? $makersID : array());
        $this->actorsID = ($actorsID != null ? $actorsID : array());
        $this->categories = ($categories != null ? $categories : array());
        $this->shortlistsID = ($shortlistsID != null ? $shortlistsID : array());
        $this->rating = ($rating != null ? $rating : '');
        $this->lastseen = ($lastseen != null ? $this->unformatDate($lastseen) : null);

        /*
         * There is a need to update lists after the write, because only the 
         * IDs are updated initially, and we want the user-friendly names.
         */
        $this->writeAll();
        $this->updateMakers();
        $this->updateActors();
        $this->updateShortlists();
    }

    /**
     * Set the date of the last time the movie was seen ({@link lastseen}), and
     * update the database accordingly. If this is the first viewing, the rating
     * is set to 0.
     * 
     * @param \string $lastseen The date, in the format 'dd/mm/yyyy'.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
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
    
    /**
     * Write all current information about the movie in the database. It
     * includes information contained in secondary tables, such as related 
     * persons, categories, and so on.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function writeAll() {
        $conn = db_ensure_connected();

        $conn->beginTransaction();

        // Ensure that the tuple exists in movies, create or update it
        $checkMovie = $conn->prepare('select id_movie from movies where id_movie = ?');
        $checkMovie->execute(array($this->id_movie));
        if ($checkMovie->rowCount() == 0) {
            $insertMovie = $conn->prepare('insert into movies (title, year, imdb_id, originaltitle) values (?, ?, ?, ?)');
            $insertMovie->execute(array($this->title, ($this->year != '' ? $this->year : null), ($this->imdb_id != '' ? $this->imdb_id : null), ($this->originaltitle != '' ? $this->originaltitle : null)));
            $this->id_movie = $conn->lastInsertId();
        } else {
            $updateMovies = $conn->prepare('update movies set title=?, year=?, imdb_id=?, originaltitle=? where id_movie=?');
            $result = $updateMovies->execute(array($this->title, ($this->year != '' ? $this->year : null), ($this->imdb_id != '' ? $this->imdb_id : null), ($this->originaltitle != '' ? $this->originaltitle : null), $this->id_movie));
            if (!$result) {
                fatal($updateMovies->errorInfo());
            }
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
        $updateExperience->execute(array(($this->rating != '' ? $this->rating : null), ($this->lastseen != '' ? $this->lastseen : null), $this->id_movie));

        $conn->commit();
    }

    /**
     * Get the unique identifier of the movie in the application 
     * ({@link id_movie}).
     * @return \int The unique identifier of the movie in the application.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getID() {
        return $this->id_movie;
    }

    /**
     * Get the IMDB identifier attached to the movie
     * ({@link imdb_id}).
     * @return \string The IMDB identifier attached to the movie.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getIMDbID() {
        return $this->imdb_id;
    }

    /**
     * Get the title (in French) of the movie
     * ({@link title}).
     * @return \string The title (in French) of the movie.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Get the original title (actually the English one) of the movie
     * ({@link originaltitle}).
     * @return \string The original title (actually the English one) of the movie.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getOriginalTitle() {
        return $this->originaltitle;
    }

    /**
     * Get the release year of the movie
     * ({@link year}).
     * @return \int The release year of the movie.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getYear() {
        return $this->year;
    }

    /**
     * Get the makers of the movie
     * ({@link makers}).
     * @return \string[] The makers of the movie.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getMakers() {
        $r = array();
        foreach ($this->makers as $maker) {
            array_push($r, $maker);
        }
        return $r;
    }

    /**
     * Get the actors of the movie
     * ({@link actors}).
     * @return \string[] The actors of the movie.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getActors() {
        $r = array();
        foreach ($this->actors as $actor) {
            array_push($r, $actor);
        }
        return $r;
    }

    /**
     * Get the categories of the movie
     * ({@link categories}).
     * @return \string[] The categories of the movie.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getCategories() {
        $r = array();
        foreach ($this->categories as $cat) {
            array_push($r, $cat);
        }
        return $r;
    }

    /**
     * Get the shortlists in which the movie appears
     * ({@link shortlists}).
     * @return \string[] The shortlists in which the movie appears.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getShortlists() {
        $r = array();
        foreach ($this->shortlists as $sl) {
            array_push($r, $sl);
        }
        return $r;
    }

    /**
     * Get the rating of the movie
     * ({@link rating}).
     * @return \int The rating of the movie
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getRating() {
        return $this->rating;
    }

    /**
     * Get the relative URI of the film cover on the server (even if the file 
     * does not exist).
     * @return \string The relative URI of the film cover on the server.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getCoverFileName() {
        return 'covers/' . $this->id_movie . '.jpg';
    }

    /**
     * Get the date at which the movie was last seen
     * ({@link lastseen}).
     * @return \string The date at which the movie was last seen, in a 
     * 'yyyy-mm-dd' format.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getLastseen() {
        return $this->lastseen;
    }

    /**
     * Get the date at which the movie was last seen
     * ({@link lastseen}), in a 'dd/mm/yyyy' format.
     * @return \string The date at which the movie was last seen, in a 
     * 'dd/mm/yyyy' format.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getFormattedLastseen($format = 'd/m/Y') {
        if ($this->lastseen != '') {
            $date = DateTime::createFromFormat('Y-m-d', $this->lastseen);
            return $date->format('d/m/Y');
        } else {
            return '';
        }
    }

    /**
     * Convert a date from a 'dd/mm/yyyy' format to a 'yyyy-mm-dd' format.
     * @param \string The date in a 'dd/mm/yyyy' format.
     * @return \string The date in a 'yyyy-mm-dd' format.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function unformatDate($date) {
        $date2 = DateTime::createFromFormat('d/m/Y', $date);
        return $date2->format('Y-m-d');
    }

    /**
     * Retrieve the names ({@link makers}) and IDs ({@link makersID}) of the 
     * movie makers from the database.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
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

    /**
     * Retrieve the names ({@link actors}) and IDs ({@link actorsID}) of the 
     * movie actors from the database.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
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

    /**
     * Retrieve the categories ({@link categories}) of the movie from the 
     * database.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
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

    /**
     * Retrieve from the database the shortlists in which the movie appears 
     * ({@link shortlists}), as well as their IDs (@link shortlistsID).
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
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

    /**
     * Retrieve information about the movie from the database, provided 
     * {@link id_movie} is properly set.
     * 
     * @param \boolean $withPeople Also retrieve actors and makers.
     * @param \boolean $withCategories Also retrieve categories.
     * @param \boolean $withShortlists Also retrieve shortlists.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function updateAll($withPeople = true, $withCategories = true, $withShortlists = true) {
        $conn = db_ensure_connected();
        $getMovie = $conn->prepare('select movies.id_movie id_movie, title, year, imdb_id, originaltitle, rating, lastseen from movies left outer join experience on movies.id_movie = experience.id_movie where movies.id_movie = ?');

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

    /**
     * Retrieve from the database the information about the media associated to
     * the movie, and stores it in {@link media}.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function retrieveMedia() {
        $this->media = Array();

        $conn = db_ensure_connected();
        $getMedia = $conn->prepare('select distinct id_medium from media where id_movie = ?');

        $getMedia->execute(array($this->id_movie));
        $mediaArray = $getMedia->fetchall(PDO::FETCH_ASSOC);
        foreach ($mediaArray as $mediumArray) {
            $id_medium = $mediumArray['id_medium'];
            $medium = new Medium($id_medium);
            array_push($this->media, $medium);
        }
    }

    /**
     * Get the media associated with the movie.
     * 
     * @return \Medium[] All the media associated with the movie.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getMedia() {
        $r = array();
        foreach ($this->media as $medium) {
            array_push($r, $medium);
        }
        return $r;
    }

}
/**
 * Class representing a given medium in the application, and managing its 
 * persistency in the database.
 * 
 * Not every piece of information is populated and up-to-date at a given time.
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
class Medium {

    /**
     * @var \Movie Reference to the corresponding movie object.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $movie;

    /**
     * @var \int Unique identifier of the medium in the application.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $id_medium;

    /**
     * @var \int Unique identifier of the corresponding movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $id_movie;

    /**
     * @var \string Type of medium (e.g. 'DVD', 'mkv'...).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $type;

    /**
     * @var \int Screen height in pixels.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $height;

    /**
     * @var \int Screen width in pixels.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $width;

    /**
     * @var \string User comment regarding the medium.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $comment;

    /**
     * @var \int Shelfmark of the medium in the user's classification system.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $shelfmark;

    /**
     * @var \string Calculated quality, corresponding to an entry in the 
     * `quality` table of the database.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $quality;

    /**
     * @var \string[] Languages available (as two-character codes) in this 
     * medium's audio streams.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $audio;

    /**
     * @var \string[] Languages available (as two-character codes) in this 
     * medium's subtitles.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $subs;

    /**
     * Constructor method for the Medium class. May either initialize a blank 
     * medium or retrieve from the database the data about an existing one.
     * @param \int $id_medium Unique identifier of the medium to retrieve, or
     * `null` to create a blank one.
     * @param \Movie $movie optional reference to an existing movie object. 
     * Ignored if first parameter is null or if the movie doesn't match the 
     * medium.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function Medium($id_medium, $movie = null) {
        if ($id_medium == null) {
            $this->id_medium == null;
        } else {
            $this->id_medium = $id_medium;
            $this->updateAll();
            $this->movie = null;
            if ($movie != null) {
                $this->setMovieIfCorrect($movie, true);
            }
        }
    }

    /**
     * Get the unique identifier of the medium.
     * @return \item The unique identifier of the medium.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getID() {
        return $this->id_medium;
    }

    /**
     * Get the unique identifier of the medium's movie.
     * @return \item The unique identifier of the medium's movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getMovieID() {
        return $this->id_movie;
    }

    /**
     * Get the type of the medium.
     * @return \string The type of the medium.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Get the medium's screen height, in pixels.
     * @return \int The medium's screen height, in pixels.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * Get the medium's screen width, in pixels.
     * @return \int The medium's screen width, in pixels.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * Get the medium's quality.
     * @return \string The medium's quality.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getQuality() {
        return $this->quality;
    }

    /**
     * Get the user comment about the medium.
     * @return \string The user comment about the medium.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * Get the shelfmark of the medium.
     * @return \int The shelfmark of the medium.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getShelfmark() {
        return $this->shelfmark;
    }

    /**
     * Get the languages available in the medium's audio streams.
     * @return \string[] The available languages, as two-character codes.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getAudio() {
        $r = array();
        foreach ($this->audio as $lang) {
            array_push($r, $lang);
        }
        return $r;
    }

    /**
     * Get the languages available in the medium's subtitles.
     * @return \string[] The available languages, as two-character codes.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getSubs() {
        $r = array();
        foreach ($this->subs as $lang) {
            array_push($r, $lang);
        }
        return $r;
    }

    /**
     * Delete the medium and all associated information from the database.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function delete() {
        $conn = db_ensure_connected();
        $delMedium = $conn->prepare('delete from `media` where `id_medium`=?');
        if (!$delMedium->execute(array($this->id_medium))) {
            // What on Earth is this td function, 
            // and why die silently in debug mode?
            td($delMedium->errorInfo());
            if ($_SESSION['debug']) {
                die();
            }
        }
    }

    /**
     * Retrieve all fields of the medium object from the database,
     * but do not set a movie object ({@link movie}).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function updateAll() {
        $conn = db_ensure_connected();
        $getMedium = $conn->prepare('select media.id_medium, id_movie, type, height, width, comment, shelfmark, quality from media, `media-quality` where media.id_medium = ? and media.id_medium=`media-quality`.id_medium');

        $getMedium->execute(array($this->id_medium));
        $nMedia = $getMedium->rowCount();
        if ($nMedia == 0) {
            fatal(
                    'Erreur inattendue : aucun support ne correspond à l\'ID ' . $this->id_medium . '.<br /><br />'
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

    /**
     * Retrieve information about available audio languages from the database.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
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

    /**
     * Retrieve information about available subtitile languages from the 
     * database.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
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

    /**
     * Set the associated movie object ({@link movie}).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function retrieveMovie() {
        if ($this->movie == null) {
            $this->movie = new Movie($this->id_movie);
        }
    }

    /**
     * Get the associated movie object ({@link movie}).
     * @return \Movie the associated movie object.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getMovie() {
        return $this->movie;
    }

    /**
     * Attach a provided movie object as the associated movie ({@link movie}),
     * but only if it is the right one (i.e. if {@link id_movie} matches).
     * @param \Movie $movie The movie object to be attached.
     * @param \boolean $otherwiseRetrieveIt Determines whether the right movie 
     * is retrieved from the database, in case of an inconsistency.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function setMovieIfCorrect($movie, $otherwiseRetrieveIt = false) {
        if ($movie->getID() == $this->getMovieID()) {
            $this->movie = $movie;
        } else if ($otherwiseRetrieveIt) {
            $this->retrieveMovie();
        }
    }

    /**
     * Set the fields of the medium object, and update the database to mirror 
     * the changes.
     * @param \string $type The type of the medium ({@link type}).
     * @param \int $height The screen height of the medium, in pixels 
     * ({@link height}).
     * @param \int $width The screen width of the medium, in pixels 
     * ({@link width}).
     * @param \string $comment The user comment about the medium 
     * ({@link comment}).
     * @param \int $shelfmark The shelfmark of the medium ({@link shelfmark}).
     * @param \string[] $audio The available audio languages of the medium 
     * ({@link audio}).
     * @param \string[] $subs The available subtitle languages of the medium 
     * ({@link subs}).
     * @param \int $id_movie The unique identifier of the movie associated to 
     * the medium ({@link id_movie}).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function setValues($type, $height, $width, $comment, $shelfmark, 
            $audio, $subs, $id_movie = null) {
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

    /**
     * Write all current information about the medium in the database. It 
     * includes information contained in secondary tables, such as available
     * languages.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
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
        $updateMovies->execute(array($this->type, ($this->height != '' ? $this->height : null), ($this->width != '' ? $this->width : null), $this->comment, ($this->shelfmark != '' ? $this->shelfmark : null), $this->id_medium));

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

    /**
     * If in debug mode, print a dump of the Medium object. Otherwise, do 
     * nothing.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function dump() {
        if ($_SESSION['debug']) {
            echo "<pre>\n";
            print_r($this);
            echo "</pre>\n";
        }
    }

}

?>
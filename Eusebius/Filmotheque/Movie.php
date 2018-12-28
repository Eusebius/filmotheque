<?php

/**
 * includes/Movie.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the file defining the Movie class.
 * This file is not to be included directly, use declarations.inc.php instead.
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

namespace Eusebius\Filmotheque;

use PDO,
    PDOException;
use DateTime;

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
    private $movieID;

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
    private $imdbID;

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
    public function __construct($id_movie, $withPeople = true, $withCategories = true, $withShortlists = true) {
        $this->imdbID = '';
        $this->originaltitle = '';
        if ($id_movie == null) {
            $this->movieID = null;
        } else {
            $this->movieID = $id_movie;
            $this->updateAll($withPeople, $withCategories, $withShortlists);
        }
    }

    /**
     * Delete the movie and all associated information from the database.
     * 
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function delete() {
        if ($this->movieID != null) {
            $conn = Util::getDbConnection();
            try {
                $delMovie = $conn->prepare('delete from `movies` where `id_movie`=?');
                if (!$delMovie->execute(array($this->movieID))) {
                    Util::fatal('Error while deleting movie ' . $this->movieID . ': ' . $delMovie->errorInfo());
                }
                $cover = $_SESSION['basepath'] . 'covers/' . $this->getID() . '.jpg';
                if (file_exists($cover)) {
                    unlink($cover);
                }
            } catch (PDOException $e) {
                Util::fatal('Error while deleting movie ' . $this->movieID . ': ' . $e->getMessage());
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
            $this->imdbID = $value;
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
        $this->lastseen = ($lastseen != null ? Util::unformatDate($lastseen) : null);

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
        $this->lastseen = ($lastseen != null ? Util::unformatDate($lastseen) : null);

        $conn = Util::getDbConnection();
        try {
            $conn->beginTransaction();

            //if ($this->rating != null && $this->rating != '') {
                $setLastSeen = $conn->prepare('update experience set lastseen=? where id_movie=?');
                $setLastSeen->execute(array($this->lastseen, $this->movieID));
                /*
            } else {
                $setLastSeen = $conn->prepare('insert into experience (lastseen, id_movie) values(?, ?)');
                $setLastSeen->execute(array($this->lastseen, $this->movieID));
            }*/

            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            Util::fatal('Error while writing experience data for movie ' . $this->movieID . ': ' . $e->getMessage());
        }
    }

    /**
     * Write all current information about the movie in the database. It
     * includes information contained in secondary tables, such as related 
     * persons, categories, and so on.
     * 
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function writeAll() {
        $conn = Util::getDbConnection();

        try {
            $conn->beginTransaction();

            // Ensure that the tuple exists in movies, create or update it
            $checkMovie = $conn->prepare('select id_movie from movies where id_movie = ?');
            $checkMovie->execute(array($this->movieID));
            if ($checkMovie->rowCount() == 0) {
                $insertMovie = $conn->prepare('insert into movies (title, year, imdb_id, originaltitle) values (?, ?, ?, ?)');
                $insertMovie->execute(array($this->title, ($this->year != '' ? $this->year : null), ($this->imdbID != '' ? $this->imdbID : null), ($this->originaltitle != '' ? $this->originaltitle : null)));
                $this->movieID = $conn->lastInsertId();
            } else {
                $updateMovies = $conn->prepare('update movies set title=?, year=?, imdb_id=?, originaltitle=? where id_movie=?');
                $result = $updateMovies->execute(array($this->title, ($this->year != '' ? $this->year : null), ($this->imdbID != '' ? $this->imdbID : null), ($this->originaltitle != '' ? $this->originaltitle : null), $this->movieID));
                if (!$result) {
                    Util::fatal('Error while updating data for movie ' . $this->movieID . ': ' . $updateMovies->errorInfo());
                }
            }

            $deleteMakers = $conn->prepare('delete from `movies-makers` where id_movie = ?');
            $deleteMakers->execute(array($this->movieID));
            foreach ($this->makersID as $makerID) {
                $insertMakers = $conn->prepare('insert into `movies-makers` (id_movie, id_person) values (?, ?)');
                $insertMakers->execute(array($this->movieID, $makerID));
            }

            $deleteActors = $conn->prepare('delete from `movies-actors` where id_movie = ?');
            $deleteActors->execute(array($this->movieID));
            foreach ($this->actorsID as $actorID) {
                $insertActors = $conn->prepare('insert into `movies-actors` (id_movie, id_person) values (?, ?)');
                $insertActors->execute(array($this->movieID, $actorID));
            }

            $deleteCategories = $conn->prepare('delete from `movies-categories` where id_movie = ?');
            $deleteCategories->execute(array($this->movieID));
            foreach ($this->categories as $category) {
                $insertCategories = $conn->prepare('insert into `movies-categories` (id_movie, category) values (?, ?)');
                $insertCategories->execute(array($this->movieID, $category));
            }

            $deleteShortlists = $conn->prepare('delete from `movies-shortlists` where id_movie = ?');
            $deleteShortlists->execute(array($this->movieID));
            foreach ($this->shortlistsID as $id_shortlist) {
                $insertShortlists = $conn->prepare('insert into `movies-shortlists` (id_movie, id_shortlist) values (?, ?)');
                $insertShortlists->execute(array($this->movieID, $id_shortlist));
            }

            //NB This creates a completely empty entry if neither field is provided.
            $deleteExperience = $conn->prepare('delete from experience where id_movie = ?');
            $deleteExperience->execute(array($this->movieID));
            $updateExperience = $conn->prepare('insert into experience (rating, lastseen, id_movie) values (?, ?, ?)');
            $updateExperience->execute(array(($this->rating != '' ? $this->rating : null), ($this->lastseen != '' ? $this->lastseen : null), $this->movieID));

            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            Util::fatal('Error while writing all data for movie ' . $this->movieID . ': ' . $e->getMessage());
        }
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
        return $this->movieID;
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
        return $this->imdbID;
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
        $movieMakers = array();
        foreach ($this->makers as $maker) {
            array_push($movieMakers, $maker);
        }
        return $movieMakers;
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
        return 'covers/' . $this->movieID . '.jpg';
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
            $result = $date->format($format);
        } else {
            $result = '';
        }
        return $result;
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
        $conn = Util::getDbConnection();
        try {
            $getMakers = $conn->prepare('select id_person, name from `movies-makers` natural join persons where id_movie = ?');
            $getMakers->execute(array($this->movieID));
            $makerArray = $getMakers->fetchall(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal('Error while retrieving maker data for movie ' . $this->movieID . ': ' . $e->getMessage());
        }
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
        $conn = Util::getDbConnection();
        try {
            $getActors = $conn->prepare('select id_person, name from `movies-actors` natural join persons where id_movie = ?');
            $getActors->execute(array($this->movieID));
            $actorArray = $getActors->fetchall(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal('Error while retrieving actor data for movie ' . $this->movieID . ': ' . $e->getMessage());
        }
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
        $conn = Util::getDbConnection();
        try {
            $getCategories = $conn->prepare('select category from `movies-categories` where id_movie = ?');
            $getCategories->execute(array($this->movieID));
            $categoryArray = $getCategories->fetchall(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal('Error while retrieving categories for movie ' . $this->movieID . ': ' . $e->getMessage());
        }
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
        $conn = Util::getDbConnection();
        try {
            $getShortlists = $conn->prepare('select id_shortlist, listname from `movies-shortlists` natural join shortlists where id_movie = ?');
            $getShortlists->execute(array($this->movieID));
            $shortlistArray = $getShortlists->fetchall(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal('Error while retrieving shortlists for movie ' . $this->movieID . ': ' . $e->getMessage());
        }
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
        $conn = Util::getDbConnection();
        try {
            $getMovie = $conn->prepare('select movies.id_movie id_movie, title, year, imdb_id, originaltitle, rating, lastseen from movies left outer join experience on movies.id_movie = experience.id_movie where movies.id_movie = ?');

            $getMovie->execute(array($this->movieID));
        } catch (PDOException $e) {
            Util::fatal('Error while retrieving all data for movie ' . $this->movieID . ': ' . $e->getMessage());
        }
        $nMovies = $getMovie->rowCount();
        if ($nMovies == 0) {
            Util::fatal('Error while retrieving all data for movie ' . $this->movieID . ': no corresponding movie in database');
        }
        if ($nMovies > 1) {
            Util::fatal('Error while retrieving all data for movie ' . $this->movieID . ': more than one corresponding movie in database');
        }

        $movieArray = $getMovie->fetchall(PDO::FETCH_ASSOC);
        $this->title = $movieArray[0]['title'];
        $this->year = $movieArray[0]['year'];
        $this->imdbID = $movieArray[0]['imdb_id'];
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

        $conn = Util::getDbConnection();
        try {
            $getMedia = $conn->prepare('select distinct id_medium from media where id_movie = ?');

            $getMedia->execute(array($this->movieID));
            $mediaArray = $getMedia->fetchall(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal('Error while retrieving media for movie ' . $this->movieID . ': ' . $e->getMessage());
        }
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

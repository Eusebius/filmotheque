<?php

/**
 * includes/Medium.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.6
 * 
 * This is the file defining the Medium class.
 * This file is not to be included directly, use declarations.inc.php instead.
 */
/*
  Filmothèque
 * Copyright (C) 2015 Eusebius <eusebius@eusebius.fr>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eusebius\Filmotheque;

use \PDO;

/**
 * Class representing a given medium in the application, and managing its 
 * persistency in the database.
 * 
 * Not every piece of information is populated and up-to-date at a given time.
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.6
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
    private $mediumID;

    /**
     * @var \int Unique identifier of the corresponding movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    private $movieID;

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
    public function __construct($id_medium, $movie = null) {
        if ($id_medium == null) {
            $this->mediumID = null;
        } else {
            $this->mediumID = $id_medium;
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
        return $this->mediumID;
    }

    /**
     * Get the unique identifier of the medium's movie.
     * @return \item The unique identifier of the medium's movie.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function getMovieID() {
        return $this->movieID;
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
        $conn = Util::getDbConnection();
        try {
            $delMedium = $conn->prepare('delete from `media` where `id_medium`=?');
            if (!$delMedium->execute(array($this->mediumID))) {
                // What on Earth is this td function, 
                // and why die silently in debug mode?
                td($delMedium->errorInfo());
                if ($_SESSION['debug']) {
                    die();
                }
            }
        } catch (PDOException $e) {
            Util::fatal($e->getMessage());
        }
    }

    /**
     * Retrieve all fields of the medium object from the database,
     * but do not set a movie object ({@link movie}).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    public function updateAll() {
        $conn = Util::getDbConnection();
        try {
            $getMedium = $conn->prepare('select media.id_medium, id_movie, type, '
                    . 'height, width, comment, shelfmark, quality from media, '
                    . '`media-quality` where media.id_medium = ? and '
                    . 'media.id_medium=`media-quality`.id_medium');

            $getMedium->execute(array($this->mediumID));
        } catch (PDOException $e) {
            Util::fatal($e->getMessage());
        }
        $nMedia = $getMedium->rowCount();
        if ($nMedia == 0) {
            Util::fatal('<br />' . $getMedium->queryString . '<br />' .
                    'Erreur inattendue : aucun support ne correspond à l\'ID '
                    . $this->mediumID . '.<br /><br />'
                    . '<a href=".">Retour à la page principale</a>'
            );
        }
        if ($nMedia > 1) {
            Util::fatal(
                    "Erreur inattendue : plusieurs supports correspondent à l'ID"
                    . "{$this->mediumID}.<br /><br />"
                    . '<a href=".">Retour à la page principale</a>'
            );
        }

        $mediumArray = $getMedium->fetchall(PDO::FETCH_ASSOC);
        $this->movieID = $mediumArray[0]['id_movie'];
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
        $conn = Util::getDbConnection();
        try {
            $getAudio = $conn->prepare('select language from `media-audio` where id_medium = ?');
            $getAudio->execute(array($this->mediumID));
            $audioArray = $getAudio->fetchall(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal($e->getMessage());
        }
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
        $conn = Util::getDbConnection();
        try {
            $getSubs = $conn->prepare('select language from `media-subs` where '
                    . 'id_medium = ?');
            $getSubs->execute(array($this->mediumID));
            $subArray = $getSubs->fetchall(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal($e->getMessage());
        }
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
            $this->movie = new Movie($this->movieID);
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
    public function setValues($type, $height, $width, $comment, $shelfmark, $audio, $subs, $id_movie = null) {
        if ($id_movie != null) {
            $this->movieID = $id_movie;
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
        $conn = Util::getDbConnection();
        try {
            $conn->beginTransaction();

            $checkMedium = $conn->prepare('select id_medium from media where '
                    . 'id_medium = ?');
            $checkMedium->execute(array($this->mediumID));
            if ($checkMedium->rowCount() == 0) {
                $insertMedium = $conn->prepare('insert into media (id_movie, type) '
                        . 'values (?, ?)');
                $insertMedium->execute(array($this->movieID, $this->type));
                $this->mediumID = $conn->lastInsertId();
            }
            $updateMovies = $conn->prepare('update media set type=?, height=?, '
                    . 'width=?, comment=?, shelfmark=? where id_medium=?');
            $updateMovies->execute(array($this->type,
                ($this->height != '' ? $this->height : null),
                ($this->width != '' ? $this->width : null), $this->comment,
                ($this->shelfmark != '' ? $this->shelfmark : null),
                $this->mediumID));

            $deleteAudio = $conn->prepare('delete from `media-audio` where id_medium = ?');
            $deleteAudio->execute(array($this->mediumID));
            foreach ($this->audio as $lang) {
                $insertLang = $conn->prepare('insert into `media-audio` (id_medium, language) values (?, ?)');
                $insertLang->execute(array($this->mediumID, $lang));
            }

            $deleteSubs = $conn->prepare('delete from `media-subs` where id_medium = ?');
            $deleteSubs->execute(array($this->mediumID));
            foreach ($this->subs as $lang) {
                $insertLang = $conn->prepare('insert into `media-subs` (id_medium, language) values (?, ?)');
                $insertLang->execute(array($this->mediumID, $lang));
            }

            $conn->commit();
        } catch (PDOException $e) {
            Util::fatal($e->getMessage());
        }
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

<?php

/**
 * scripts/dolinkimdb.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the script taking care of the linking to an imdb movie entry.
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

if (__FILE__ !== $_SERVER["SCRIPT_FILENAME"]) {
    header('Location: ../');
    die();
}

require_once('../includes/declarations.inc.php');
require_once('../includes/initialization.inc.php');

use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\Movie;
use Eusebius\Filmotheque\Util;

Auth::ensurePermission('write');

$id_movie_string = Util::getPOSTValueOrNull('id_movie', Util::POST_CHECK_INT);
$imdb_id = Util::getPOSTValueOrNull('imdb_id', Util::POST_CHECK_STRING);

if ($id_movie_string !== NULL && $id_movie_string !== '' && $imdb_id !== NULL && $imdb_id !== '') {

    if ((string) (int) $id_movie_string == $id_movie_string) {
        $id_movie = (int) $id_movie_string;
    } else {
        // Return to home page if movie ID is not a number
        Util::gotoMainPage();
    }

    $movie = new Movie($id_movie);

    if (!isset($_SESSION['imdbdata'][$id_movie])) {

        $xml = new DomDocument();
        $xml->load('http://myapifilms.com/imdb?idIMDB=' . $imdb_id . '&format=XML');
    } else {
        $xml = new DomDocument();
        $xml->loadXML($_SESSION['imdbdata'][$id_movie]);
    }
    $movies = $xml->getElementsByTagName('movie');
    foreach ($movies as $item) {
        if ($imdb_id === $item->getElementsByTagName('idIMDB')->item(0)->nodeValue) {
            $originaltitle = $item->getElementsByTagName('title')->item(0)->nodeValue;
            $year = $item->getElementsByTagName('year')->item(0)->nodeValue;

            $movie->setFieldAndWait("imdb_id", $imdb_id);
            $movie->setFieldAndWait("originaltitle", $originaltitle);
            $movie->setFieldAndWait("year", $year);
            $movie->writeAll();

            Util::resetMovieInSession();

            $cover = $item->getElementsByTagName('urlPoster')->item(0);
            if ($cover != null) {
                $cover = $cover->nodeValue;
                $coverdest = $_SESSION['basepath'] . 'covers/' . $movie->getID() . '.jpg';
                if (file_exists($coverdest)) {
                    unlink($coverdest);
                }
                copy($cover, $coverdest);
            }
            unset($_SESSION['imdbdata']);
            header('Location:../?page=moviedetails&id_movie=' . $id_movie);
            die();
        }
    }
} else {
    // Return to home page if either movie ID or imdb ID is not provided
    Util::gotoMainPage();
}
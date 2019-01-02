<?php

/**
 * scripts/doremoveimdb.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.3
 * 
 * This is the script removing the link between an movie and an IMDb entry.
 */
/*
  Filmothèque
  Copyright (C) 2012-2019 Eusebius (eusebius@eusebius.fr)

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

$id_movie_string = filter_input(INPUT_GET, 'id_movie', FILTER_SANITIZE_NUMBER_INT);

if ($id_movie_string !== NULL && $id_movie_string !== '') {

    if ((string) (int) $id_movie_string == $id_movie_string) {
        $id_movie = (int) $id_movie_string;
    } else {
        // Return to home page if movie ID is not a number
        Util::gotoMainPage();
    }

    $movie = new Movie($id_movie);
    $movie->unsetIMDb();
    unset($_SESSION['movie']);
    header('Location:../?page=moviedetails&id_movie=' . $id_movie);
    die();
    
} else {
    // Return to home page if movie ID is not provided
    Util::gotoMainPage();
}
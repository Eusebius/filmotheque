<?php

/**
 * doabandonimdb.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.5
 * 
 * This is the script acknowledging that a movie cannot be linked to an IMDb 
 * file.
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

require_once('includes/declarations.inc.php');
require_once('includes/initialization.inc.php');

if (isset($_GET['id_movie']) && $_GET['id_movie'] != '') {

    if ((string) (int) $_GET['id_movie'] == $_GET['id_movie']) {
        $id_movie = (int) $_GET['id_movie'];
    } else {
        // Return to home page if movie ID is not a number
        Util::gotoMainPage();
    }

    $movie = Util::getMovieInSession($id_movie);

    if ($movie->getIMDbID() == null) {
        $movie->setFieldAndWait('imdb_id', 'nil');
        $movie->writeAll();
    }

    header('Location:./?page=moviedetails&id_movie=' . $id_movie);
} else {
    // Return to home page if no movie ID is provided
    Util::gotoMainPage();
}
<?php

/**
 * docreatemovie.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the script taking care of the creation of a movie.
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

require_once('../includes/declarations.inc.php');
require_once('../includes/initialization.inc.php');
ensurePermission('w');

if (isset($_POST['title']) && $_POST['title'] != '') {

    $movie = new Movie(null);
    $movie->setValues(Util::getPOSTValueOrNull('title'), 
            Util::getPOSTValueOrNull('year'), 
            Util::getPOSTValueOrNull('makers'), 
            Util::getPOSTValueOrNull('actors'), 
            Util::getPOSTValueOrNull('categories'), 
            Util::getPOSTValueOrNull('shortlists'), 
            Util::getPOSTValueOrNull('rating'), 
            Util::getPOSTValueOrNull('lastseen'));

    //$movie->dump();die();
    $_SESSION['movie'] = $movie;
    header('Location:../?page=moviedetails&id_movie=' . $movie->getID());
} else {
    // Return to home page if no movie title is provided
    Util::gotoMainPage();
}
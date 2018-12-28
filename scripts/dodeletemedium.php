<?php

/**
 * scripts/dodeletemedium.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the script taking care of the deletion of a medium.
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
use Eusebius\Filmotheque\Medium;
use Eusebius\Filmotheque\Util;
Auth::ensurePermission('write');

$id_medium_string = filter_input(INPUT_GET, 'id_medium', FILTER_SANITIZE_NUMBER_INT);

if ($id_medium_string !== false && $id_medium_string !== NULL && $id_medium_string !== '') {

    if ((string) (int) $id_medium_string == $id_medium_string) {
        $id_medium = (int) $id_medium_string;
    } else {
        // Return to home page if medium ID is not a number
        Util::gotoMainPage();
    }

    if (!isset($_SESSION['medium']) || $_SESSION['medium']->getID() != $id_medium) {
        $_SESSION['medium'] = new Medium($id_medium);
    }
    $_SESSION['medium']->delete();

    $movieID = $_SESSION['medium']->getMovieID();

    header('Location:../?page=moviedetails&id_movie=' . $movieID);
} else {
    // Return to home page if no medium ID is provided
    Util::gotoMainPage();
}
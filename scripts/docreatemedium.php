<?php

/**
 * scripts/docreatemedium.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the script taking care of the creation of a medium.
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
use Eusebius\Filmotheque\Medium;
use Eusebius\Filmotheque\Util;
Auth::ensurePermission('write');

//TODO check all instances of == and !=

$id_movie_string = Util::getPOSTValueOrNull('id_movie', Util::POST_CHECK_INT);

if ($id_movie_string !== NULL && $id_movie_string !== '') {

    if ((string) (int) $id_movie_string == $id_movie_string) { //== is intentional here
        $id_movie = (int) $id_movie_string;
    } else {
        // Return to home page if movie ID is not a number
        Util::gotoMainPage();
    }

    $medium = new Medium(null);
    $medium->setValues(Util::getPOSTValueOrNull('container', Util::POST_CHECK_STRING), 
            Util::getPOSTValueOrNull('height', Util::POST_CHECK_INT), 
            Util::getPOSTValueOrNull('width', Util::POST_CHECK_INT), 
            Util::getPOSTValueOrNull('comment', Util::POST_CHECK_STRING), 
            Util::getPOSTValueOrNull('shelfmark', Util::POST_CHECK_INT), 
            Util::getPOSTValueOrNull('audio', Util::POST_CHECK_STRING_ARRAY), 
            Util::getPOSTValueOrNull('subs', Util::POST_CHECK_STRING_ARRAY), 
            $id_movie);

    //$medium->dump();
    header('Location:../?page=moviedetails&id_movie=' . $medium->getMovieID());
} else {
    // Return to home page if no movie ID is provided
    Util::gotoMainPage();
}
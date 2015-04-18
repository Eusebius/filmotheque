<?php
/**
 * docreatemovie.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @version 0.2.4
 * 
 * This is the script taking care of the creation of a movie.
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

require_once('includes/required.inc.php');


if (isset($_POST['title']) && $_POST['title'] != '') {

  $movie = new Movie(null);
  $movie->setValues(POSTValueOrNull('title'), POSTValueOrNull('year'), POSTValueOrNull('makers'), POSTValueOrNull('actors'), POSTValueOrNull('categories'), POSTValueOrNull('shortlists'), POSTValueOrNull('rating'), POSTValueOrNull('lastseen'));

  //$movie->dump();die();
  $_SESSION['movie'] = $movie;
  header('Location:./?page=moviedetails&id_movie=' . $movie->getID());
}

else {
  // Return to home page if no movie title is provided
    gotoMainPage();
}

function POSTValueOrNull($POSTindex) {
  if (isset($_POST[$POSTindex]) && $_POST[$POSTindex] != '') {
    return $_POST[$POSTindex];
  }
  else return null;
}

?>
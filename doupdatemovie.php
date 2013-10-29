<?php
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


if (isset($_POST['id_movie']) && $_POST['id_movie'] != '') {

  if((string)(int)$_POST['id_movie'] == $_POST['id_movie']) {
    $id_movie = (int)$_POST['id_movie'];
  }
  else {
  // Return to home page if movie ID is not a number
    gotoMainPage();
  }

  if (!isset($_SESSION['movie'])) {
    $_SESSION['movie'] = new Movie($id_movie);
  }
  $movie = $_SESSION['movie'];

  $movie->setValues(POSTValueOrNull('title'), POSTValueOrNull('year'), POSTValueOrNull('makers'), POSTValueOrNull('actors'), POSTValueOrNull('categories'), POSTValueOrNull('shortlists'), POSTValueOrNull('rating'), POSTValueOrNull('lastseen'));

  header('Location:./?page=moviedetails&id_movie=' . $id_movie);
}

else {
  // Return to home page if no movie ID is provided
    gotoMainPage();
}

function POSTValueOrNull($POSTindex) {
  if (isset($_POST[$POSTindex]) && $_POST[$POSTindex] != '') {
    return $_POST[$POSTindex];
  }
  else return null;
}

?>
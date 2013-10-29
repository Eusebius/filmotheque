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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

  <head>
    <title>Filmothèque</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/movies.css"/>
  </head>
  
  <body>
<?php

  if (isset($_GET['page']) && $_GET['page'] == 'moviedetails') {
    include('moviedetails.inc.php');
  }
  else if (isset($_GET['page']) && $_GET['page'] == 'updatemovie') {
    include('updatemovie.inc.php');
  }
  else if (isset($_GET['page']) && $_GET['page'] == 'updatemedium') {
    include('updatemedium.inc.php');
  }
  else if (isset($_GET['page']) && $_GET['page'] == 'addmedium') {
    include('addmedium.inc.php');
  }
  else if (isset($_GET['page']) && $_GET['page'] == 'addmovie') {
    include('addmovie.inc.php');
  }
  else if (isset($_GET['page']) && $_GET['page'] == 'getimdb') {
    include('getimdb.inc.php');
  }
  else {
    include('listmovies.inc.php');
  }

?>

    <br />
    <br />
    <hr />
    Version <?php echo $_SESSION['config']['version']; ?>
  </body>
</html>
<?php
/**
 * index.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the main file to be served by the web server. It contains the
 * skeleton of any HTML rendering, and loads PHP content based on a GET 
 * parameter.
 */
/*
  Filmothèque
  Copyright (C) 2012-2016 Eusebius (eusebius@eusebius.fr)

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

use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\Util;

Auth::ensureAuthenticated();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <title>Filmothèque</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css"/>
    </head>

    <body>
        <div id="aside">
            <div id="corner">
            </div>
            <div id="sidemenu">
                <?php
                include('pages/sidemenu.inc.php');
                ?>
            </div>
        </div>

        <div id="header">
            <h1>Filmothèque</h1>
            <?php
            Util::checkChmod();
            if ($_SESSION['debug']) {
                echo "<hr /><center><em><strong>DEBUG MODE</strong></em></center><hr /><br />\n";
            }
            ?>
        </div>
        <div class="container">
            <?php
            //TODO provide a specific input filter for page names
            $getPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

            if ($getPage === 'moviedetails') {
                include('pages/moviedetails.inc.php');
            } else if ($getPage === 'updatemovie') {
                include('pages/updatemovie.inc.php');
            } else if ($getPage === 'updatemedium') {
                include('pages/updatemedium.inc.php');
            } else if ($getPage === 'addmedium') {
                include('pages/addmedium.inc.php');
            } else if ($getPage === 'addmovie') {
                include('pages/addmovie.inc.php');
            } else if ($getPage === 'getimdb') {
                include('pages/getimdb.inc.php');
            } else if($getPage === 'admin/manageusers.inc.php' && Auth::hasPermission('admin')) {
                include('pages/admin/manageusers.inc.php');
            } else {
                include('pages/listmovies.inc.php');
            }
            ?>
        </div>
        <!-- </div> -->
    </body>
</html>
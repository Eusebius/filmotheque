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
        <link rel="stylesheet" type="text/css" href="bootstrap/cyborg.min.css" />
        <link rel="stylesheet" type="text/css" href="custom.css" />
        <!-- Scrollbar Custom CSS -->
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css" /> -->

        <!-- Legacy Filmotheque CSS -->
        <!-- <link rel="stylesheet" type="text/css" href="style.css"/> -->
    </head>

    <body>
        <div class="wrapper">
            <!-- Sidebar -->
            <nav id="sidebar">

                <?php
                include('pages/sidemenu.inc.php');
                ?>

            </nav>


            <!-- Page Content -->
            <div id="content">

                <!-- <div id="header"> -->
                <h2><center>Filmothèque</center></h2>
                <?php
                Util::checkChmod();
                Util::checkAdminPwd();
                if ($_SESSION['debug']) {
                    ?>

                    <div class="bs-component">
                        <div class="alert alert-info">
                            <!-- <h4 class="alert-heading"><center><em><strong>DEBUG MODE</strong></em></center></h4> -->
                            <center><em><strong>DEBUG MODE</strong></em></center>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <!-- </div> -->
                <div class="container">
                    <?php
                    //TODO provide a specific input filter for page names
                    $getPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

                    if (!Auth::hasPermission('read')) {
                        include('pages/noaccess.inc.php');
                    } else if ($getPage === 'moviedetails') {
                        include('pages/moviedetails.inc.php');
                    } else if ($getPage === 'updatemovie' && Auth::hasPermission('write')) {
                        include('pages/updatemovie.inc.php');
                    } else if ($getPage === 'updatemedium' && Auth::hasPermission('write')) {
                        include('pages/updatemedium.inc.php');
                    } else if ($getPage === 'addmedium' && Auth::hasPermission('write')) {
                        include('pages/addmedium.inc.php');
                    } else if ($getPage === 'addmovie' && Auth::hasPermission('write')) {
                        include('pages/addmovie.inc.php');
                    } else if ($getPage === 'getimdb' && Auth::hasPermission('write')) {
                        include('pages/getimdb.inc.php');
                    } else if ($getPage === 'admin/manageusers.inc.php' && Auth::hasPermission('admin')) {
                        include('pages/admin/manageusers.inc.php');
                    } else {
                        include('pages/listmovies.inc.php');
                        include('pages/listmovies.inc.php');
                        include('pages/listmovies.inc.php');
                        include('pages/listmovies.inc.php');
                        include('pages/listmovies.inc.php');
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- bootstrap JS files -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
        <!-- jQuery Custom Scroller CDN -->
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>-->


    </body>
</html>
<?php

/**
 * includes/config.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the main configuration file of the application. All customization for
 * a particular installation should go here.
 * This file is not to be included directly, use declarations.inc.php instead.
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

/**
 * Configuration array
 * *DON'T TOUCH THIS*
 */
$_SESSION['config'] = array();

/**
 * Are we in debug mode?
 */
$_SESSION['debug'] = false;

/**
 * Database connection configuration
 * *UPDATE THIS* to suit your needs
 */
$_SESSION['config']['db_type'] = 'mysql';
$_SESSION['config']['db_server'] = 'localhost';
$_SESSION['config']['db_db'] = 'films';
$_SESSION['config']['db_user'] = 'films';
$_SESSION['config']['db_password'] = 'films';
$_SESSION['config']['db_prefix'] = '';

/**
 * Hard-coded authentication source.
 * Usable permissions are: r (read basic info), w (write everything), shortlists
 * (see them), rating (see them), lastseen (see this info).
 * *UPDATE THIS* to design your user base.
 */
// TODO keeping this in a file and in session sucks, of course. It should be 
// moved to the database ASAP, with proper password hashing.
// TODO permission for seeing borrowed items
$_SESSION['users'] = array(
    array('login' => 'admin', 'password' => 'admin', 'roles' => array('admin', 'rw', 'ro')),
    array('login' => 'rw', 'password' => 'rw', 'roles' => array('rw', 'ro')),
    array('login' => 'ro', 'password' => 'ro', 'roles' => array('ro'))
);
$_SESSION['roles'] = array(
    'admin' => array('r', 'w', 'shortlists', 'rating', 'lastseen'),
    'rw' => array('r', 'w', 'shortlists', 'rating', 'lastseen'),
    'ro' => array('r')
);

/* /**
 * Current software version
 * *DON'T TOUCH THIS*
 */
$_SESSION['config']['version'] = "0.2.7";

/**
 *  Assigned background colours for each medium quality
 * Modify the colours as you wish, but the array keys should match the entries
 * of the `quality` table in the database. It is not advised to modify them.
 */
$colour['Full HD'] = "#64FF64";
$colour['Blu-Ray'] = "#55D955";
$colour['DVD'] = "#B7D2FF";
$colour['DivX moyen'] = "#FFFF64";
$colour['DivX médiocre'] = "#FFC264";
$colour['undefined'] = "#FFFFFF";
$colour['0'] = "#DDDDDD";

/**
 * Ranking of the medium qualities.
 * Again, it is not advised to modify this.
 */
$rank['Full HD'] = 5;
$rank['Blu-Ray'] = 4;
$rank['DVD'] = 3;
$rank['DivX moyen'] = 2;
$rank['DivX médiocre'] = 1;
$rank['undefined'] = 0;

/**
 * This is just the developers' garbage. Ignore it.
 */
/*
  print_r($_SESSION['config']);
  echo '<br />';
*/

/**
 * MyAPIFilms token used by the application
 */
$apiToken = '05ecaa22-39b5-4b69-8975-b75d82485ac2';
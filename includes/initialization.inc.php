<?php

/**
 * includes/initialization.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.6
 * 
 * This is a file included in all pages and scripts of the application. It 
 * initializes the session environment and sets all needed global variables.
 */
/*
 * Copyright (C) 2015 Eusebius <eusebius@eusebius.fr>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

use Eusebius\Filmotheque\Util;

//Check whether we are in HTTPS or not
$secure = Util::isHTTPS();

//Set cookie parameters accordingly
$currentCookieParams = session_get_cookie_params(); 
session_set_cookie_params(
        $currentCookieParams['lifetime'], 
        $currentCookieParams['path'], 
        $currentCookieParams['domain'], 
        $secure, 
        true);

session_start();
require_once('config.inc.php');

if (!isset($_SESSION['http'])) {
    if ($secure) {
        // SSL connection
        $_SESSION['http'] = 'https';
    } else {
        $_SESSION['http'] = 'http';
    }
}

if (!isset($_SESSION['basepath'])) {
    $currentDirName = dirname(__FILE__);
    $basepath = Util::stripPathFromDirs($currentDirName);
    ini_set('include_path', $basepath . ':' . ini_get('include_path'));
    $_SESSION['basepath'] = $basepath;
}

unset($_SESSION['baseuri']);
if (!isset($_SESSION['baseuri'])) {
    $completeURI = Util::getHttpHost() . Util::getRequestURI();
    $_SESSION['baseuri'] = Util::stripPathFromDirs($completeURI);
}

unset($_SESSION['loginURL']);
$_SESSION['loginURL'] = $_SESSION['http'] . '://' . $_SESSION['baseuri'] . 'login.php';

unset($_SESSION['homeURL']);
$_SESSION['homeURL'] = $_SESSION['http'] . '://' . $_SESSION['baseuri'] . 'index.php';

if (!$_SESSION['debug']) {
    ini_set('display_errors', 'Off'); //It should be the webmaster's responsibility, though - errors may arise above this line
} else {
    ini_set('display_errors', 'On');
    ini_set('error_reporting', E_ALL);
}
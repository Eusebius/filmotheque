<?php

/**
 * includes/required.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is a file included in all pages and scripts of the application. It 
 * positions session info, globals, utility functions and various configuration
 * data.
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

$currentDirName = dirname(__FILE__);
$basepath = substr($currentDirName, 0, (strpos($currentDirName, '/includes') ? strpos($currentDirName, '/includes') : strpos($currentDirName, '\includes')));
ini_set('include_path', $basepath . ':' . ini_get('include_path'));

require_once('Movie.php');
require_once('Medium.php');

session_start();

require_once('config.inc.php');
require_once('db.inc.php');

$_SESSION['basepath'] = $basepath;

$_SESSION['debug'] = true;

if (!$_SESSION['debug']) {
    ini_set('display_errors', 'Off'); //It should be the webmaster's responsibility, though - errors may arise above this line
} else {
    ini_set('display_errors', 'On');
    ini_set('error_reporting', E_ALL);
}

/**
 * Redirects the visitor to the main page of the application.
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
function gotoMainPage() {
    header('Location:.');
    die();
}

/**
 * If in debug mode, make a debug print of the variable. Otherwise, do nothing.
 * @param $array
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
function debug($array) {
    if ($_SESSION['debug']) {
        echo "<pre>\n";
        print_r($array);
        echo "\n</pre>\n";
    }
}

/*
  function debug($string) {
  if ($_SESSION['debug']) {
  echo $string;
  }
  }
 */

/**
 * Halts the application, with an error message if in debug mode, or silently
 * otherwise.
 * @param $message The message to display (can be a string or an array).
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
function fatal($message) {
    if ($_SESSION['debug']) {
        print_r($message);
        die();
    } else {
        gotoMainPage();
    }
}

/**
 * Check that the `covers` directory is writeable by the application. Otherwise,
 * print an error message informing the user (even if not in debug mode).
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
function check_chmod() {
    if (!is_writable($_SESSION['basepath'] . '/covers')) {
        echo '<center><strong><font color="red">Erreur de configuration&nbsp;: le répertoire "covers" doit être accessible en écriture.</font></strong></center>';
    }
}

/**
 * Check whether the parameter is a string corresponding to an int.
 * @param \string $string The string to check.
 * @return The corresponding integer, or `false`.
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
function isIntString($string) {
    if ((string) (int) $string == $string) { //TODO rewrite that, as "0123abc" coerces to an int.
        $result = (int) $string;
    } else {
        $result = false;
    }
    return $result;
}

/**
 * Print hidden input fields based on a GET-like parameter string.
 * @param \string $paramString A GET-like parameter string, in the form 
 * `key1=value1&key2=value2`.
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
function makeHiddenParameters($paramString) {
    $couples = explode('&', $paramString);
    foreach ($couples as $couple) {
        $couple = explode('=', $couple);
        if (count($couple) == 2) {
            echo '<input type="hidden" name="' . $couple[0]
            . '" value="' . $couple[1] . '" />' . "\n";
        }
    }
}

/**
 * For a given index, returns the corresponding POST parameter if it is valid,
 * or `null` otherwise.
 * @param \string $POSTindex The index of the parameter.
 * @return Either the value of the parameter, or `null`.
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
function POSTValueOrNull($POSTindex) {
    if (isset($_POST[$POSTindex]) && $_POST[$POSTindex] != '') {
        $result = $_POST[$POSTindex];
    } else {
        $result = null;
    }
    return $result;
}

/**
 * Check that the proper movie is present as an object in session (either reuse
 * the existing one or load the proper one). Does not refresh the movie object
 * if it refers to the right movie.
 * @param \int $id_movie The unique identifier of the movie.
 * @return \Movie The movie object.
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.6
 */
function getMovieInSession($id_movie) {
    if (!isset($_SESSION['movie']) || $_SESSION['movie']->getID() != $id_movie) {
        $_SESSION['movie'] = new Movie($id_movie);
    }
    return $_SESSION['movie'];
}

/**
 * Check that the proper medium is present as an object in session (either reuse
 * the existing one or load the proper one). Does not refresh the medium object
 * if it refers to the right medium.
 * @param \int $id_medium The unique identifier of the medium.
 * @return \Medium The medium object.
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.6
 */
function getMediumInSession($id_medium) {
    if (!isset($_SESSION['medium']) || $_SESSION['medium']->getID() != $id_medium) {
        $_SESSION['medium'] = new Medium($id_medium);
    }
    return $_SESSION['medium'];
}
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

$basepath = dirname(__FILE__);
$basepath = substr($basepath, 0, 
		   (strpos($basepath, '/includes') ? strpos($basepath, '/includes') 
		    : strpos($basepath, '\includes')));
ini_set('include_path', $basepath . ':' . ini_get('include_path'));

require_once('movie.inc.php');

session_start();

require_once('config.inc.php');
require_once('db.inc.php');

$_SESSION['basepath'] = $basepath;

$_SESSION['debug'] = true;

if (!$_SESSION['debug']) {
  ini_set('display_errors', 'Off'); //It should be the webmaster's responsibility, though - errors may arise above this line
}
else {
  ini_set('display_errors', 'On');
  ini_set('error_reporting', E_ALL);
}

function gotoMainPage() {
  header('Location:.');
  die();
}

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
function fatal($string) {
  if ($_SESSION['debug']) {
    die($string);
  }
  else {
    die();
  }
}

function check_chmod() {
  if (!is_writable($_SESSION['basepath'] . '/covers')) {
    echo '<center><strong><font color="red">Erreur de configuration&nbsp;: le répertoire "covers" doit être accessible en écriture.</font></strong></center>';
  }
}

// Checks whether the parameter is a string corresponding to an int.
// Returns either the int, or false
function isIntString($string) {
  if((string)(int)$string == $string) {
    return (int)$string;
  }
  else {
    return false;
  }
}

// The parameter must not have a starting "?"
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

?>
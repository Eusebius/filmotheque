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
$basepath = substr($basepath, 0, strpos($basepath, '/includes'));
ini_set('include_path', $basepath . ':' . ini_get('include_path'));

require_once('movie.inc.php');

session_start();

require_once('config.inc.php');
require_once('db.inc.php');

$_SESSION['basepath'] = $basepath;


$_SESSION['debug'] = true;

function gotoMainPage() {
  header('Location:.');
  die();
}

function td($array) {
  if ($_SESSION['debug']) {
    echo "<pre>\n";
    print_r($array);
    echo "\n</pre>\n";
  }
}

function debug($string) {
  if ($_SESSION['debug']) {
    echo $string;
  }
}

function fatal($string) {
  if ($_SESSION['debug']) {
    die($string);
  }
  else {
    die();
  }
}

function check_chmod() {
  $f = fopen($_SESSION['basepath'] . '/covers/test', 'w');
  if (!$f) {
    echo '<center><strong><font color="red">Erreur de configuration&nbsp;: le répertoire "covers" doit être accessible en écriture.</font></strong></center>';
  }
  else {
    fclose($f);
  }
}

?>
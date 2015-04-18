<?php
/**
 * includes/db.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is a library of database management functions.
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

require_once('includes/config.inc.php');

/**
 * Provides a valid connection to the database, either by retrieving an existing
 * one in session or by opening a new one (and registering it in session for 
 * future use).
 * 
 * In case of errors, dies with an error message (unspecific if not in debug
 * mode).
 * 
 * @return \PDO A valid PDO connection object.
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
function db_ensure_connected() {
  if ((!isset($_SESSION['dbconn'])) or ($_SESSION['dbconn'] == null)) {
    try {
      $pdoconn = new PDO(
				    $_SESSION['config']['db_type'] 
				    . ':host=' . $_SESSION['config']['db_server']
				    . ';dbname=' . $_SESSION['config']['db_db'],
				    $_SESSION['config']['db_user'],
				    $_SESSION['config']['db_password'],
				    array(PDO::ATTR_PERSISTENT => true));
    } 
    catch (PDOException $e) {
      if ($_SESSION['debug']) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
	    die();
      }
      else {
	die('<center><font color="red"><strong>Une erreur de connexion à la base de données est survenue.</strong></font></center>');
      }
    }
  }
  $pdoconn->query("SET NAMES utf8");   
  return $pdoconn;
}

/**
 * Closes the connection to the database, provided all resultsets are nullified
 * beforehand.
 * 
 * @param \PDO $pdoconn The connection to close
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 */
function db_disconnect($pdoconn) {
  $pdoconn = null;
}

?>

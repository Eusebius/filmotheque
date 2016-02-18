<?php
/**
 * logon.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.7
 * 
 * This is the authentication script of the application.
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
require_once('../includes/declarations.inc.php');
require_once('../includes/initialization.inc.php');

// If credentials are incomplete, return to login page
if (!isset($_POST['login']) || ($_POST['login'] === '')
    || !isset($_POST['login']) || ($_POST['password'] === '')) {
    Util::gotoLoginPage();
} else {
    $login = $_POST['login'];
    $password = $_POST['password'];
    foreach($_SESSION['users'] as $user) {
        if ($user['login'] === $login && $user['password'] === $password) {
            $_SESSION['auth'] = $user;
            if (isset($_SESSION['nextPage'])) {
                header('Location: ' . $_SESSION['nextPage']);
                exit();
            } else {
                Util::gotoMainPage();
            }
        }
    }
    //Authentication has failed
    //TODO include an error message
    Util::gotoLoginPage();
}

?>
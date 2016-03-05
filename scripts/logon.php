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

use Eusebius\Filmotheque\Util;
use Eusebius\Filmotheque\Auth;

$login = Util::getPOSTValueOrNull('login', Util::POST_CHECK_STRING);
$password= Util::getPOSTValueOrNull('password', Util::POST_CHECK_RAW);

// If credentials are incomplete, return to login page
if ($login === NULL || $login === '' || $password === NULL || ($password === '')) {
    Util::gotoLoginPage();
} else {
    
    if (Auth::authenticateUser($login, $password)) {
        //Authentication is successful
        $_SESSION['auth'] = $login;
        session_regenerate_id(false);
        if (isset($_SESSION['nextPage'])) {
            header('Location: ' . $_SESSION['nextPage']);
            exit();
        } else {
            Util::gotoMainPage();
        }
    }
    
    //Authentication has failed
    //TODO include an error message
    Util::gotoLoginPage();
}
<?php

/**
 * scripts/doupdateuser.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 * 
 * This script updates the profile of a user.
 */
/*
  Filmothèque
  Copyright (C) 2012-2018 Eusebius (eusebius@eusebius.fr)

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

if (__FILE__ !== $_SERVER["SCRIPT_FILENAME"]) {
    header('Location: ../');
    die();
}

require_once('../includes/declarations.inc.php');
require_once('../includes/initialization.inc.php');

use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\User;
use Eusebius\Filmotheque\Util;
use Eusebius\Exceptions\UserNotFoundException;

Auth::ensureAuthenticated();

$stdRegexp = '/^[a-z_\-0-9]*$/i';

$login = filter_input(INPUT_POST, 'login', FILTER_VALIDATE_REGEXP, array('options' => array("regexp" => $stdRegexp)));
if ($login === false || $login === '') {
    Util::fatal('Invalid login provided: ' . filter_input(INPUT_POST, 'login'));
}

if (!Auth::hasPermission('admin') && !$_SESSION !== $login) {
    // The logged user is neither an admin nor the user to be updated,
    // He is not authorized to perform this action.
    Util::gotoMainPage();
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if ($email === false) {
    Util::fatal('Invalid e-mail provided: ' . filter_input(INPUT_POST, 'email'));
}

// One cannot change one's own roles
if ($_SESSION['auth'] === $login) {
    $roles = false;
} else {
    $roles = filter_input(INPUT_POST, 'roles', FILTER_VALIDATE_REGEXP, array('options' => array("regexp" => $stdRegexp), 'flags' => FILTER_REQUIRE_ARRAY));
//$roles may be false or null

    if (!is_null($roles) && $roles !== false) {
        $validRoles = Auth::getAllRoles();
        foreach ($roles as $role) {
            if (!in_array($role, $validRoles)) {
                Util::fatal('Invalid role provided: ' . $role);
            }
        }
    } else {
        $roles = false;
    }
}

try {
    $user = new User($login);
} catch (UserNotFoundException $e) {
    $_SESSION['error'] = "L'utilisateur que vous souhaitez modifier n'existe pas.";
}
if ($email !== $user->getEmail()) {
    try {
        $user->updateEmail($email);
    } catch (UnauthorizedException $e) {
        $_SESSION['error'] = 'Une erreur d\'autorisation inattendue est '
                . 'survenue, vous ne pouvez pas mettre à jour cet utilisateur';
    }
}
if ($roles === false) {
    $roles = array();
}
//TODO maybe check before if there is a diff, but beware, 
//order might be different and === is probably not adapted.
//It has to be a set equality.
if (Auth::hasPermission('admin') && $_SESSION['auth'] !== $login) {
    try {
        $user->updateRoles($roles);
    } catch (UnauthorizedException $e) {
        $_SESSION['error'] = 'Une erreur d\'autorisation inattendue est '
                . 'survenue, vous ne pouvez pas mettre à jour cet utilisateur';
    }
}

header('Location:../?page=admin/manageusers.inc.php');

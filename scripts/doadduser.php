<?php

/**
 * scripts/doadduser.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 * 
 * This script creates a new user in the system.
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

use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\User;
use Eusebius\Filmotheque\Util;
use Eusebius\Exceptions\UserExistsException;

Auth::ensurePermission('admin');

$stdRegexp = '/^[a-z_\-0-9]*$/i';

$login = filter_input(INPUT_POST, 'login', FILTER_VALIDATE_REGEXP, array('options' => array("regexp" => $stdRegexp)));
if ($login === false || $login === '') {
    Util::fatal('Invalid login provided: ' . filter_input(INPUT_POST, 'login'));
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if ($email === false) {
    Util::fatal('Invalid e-mail provided: ' . filter_input(INPUT_POST, 'email'));
}

$password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT, FILTER_REQUIRE_SCALAR);

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

$user = new User();
$user->setLogin($login);
$user->setEmail($email);
$user->setPassword($password);
if ($roles !== false) {
    $user->setRoles($roles);
}

try {
    $user->createInDB();
} catch (UserExistsException $e) {
    $_SESSION['error'] = 'Un utilisateur existe déjà avec ce login.';
}
header('Location:../?page=admin/manageusers.inc.php');

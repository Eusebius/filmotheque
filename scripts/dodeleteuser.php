<?php

/**
 * scripts/dodeleteuser.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 * 
 * This script deletes a user from the system.
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
use Eusebius\Filmotheque\Util,
    Eusebius\Exceptions\UserNotFoundException;

Auth::ensurePermission('admin');
$stdRegexp = '/^[a-z_\-0-9]*$/i';

$login = filter_input(INPUT_GET, 'login', FILTER_VALIDATE_REGEXP, array('options' => array("regexp" => $stdRegexp)));
if ($login === false || $login === '') {
    $message = 'Invalid login provided for deletion: ' . filter_input(INPUT_POST, 'login');
    Util::log('fatal', 'dodeleteuser', $message);
    Util::fatal($message);
}

if ($login === $_SESSION['auth']) {
    $message = 'You cannot delete your own account.';
    Util::log('fatal', 'dodeleteuser', $message);
    Util::fatal($message);
}

try {
    $user = new User($login);
} catch (UserNotFoundException $e) {
    $_SESSION['error'] = 'Cet utilisateur n\'existe pas';
    header('Location:../?page=admin/manageusers.inc.php');
    die();
}
$user->deleteFromDB();

header('Location:../?page=admin/manageusers.inc.php');

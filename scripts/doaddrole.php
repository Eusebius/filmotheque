<?php

/**
 * scripts/doaddrole.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 * 
 * This script adds a role in the system.
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

if (__FILE__ !== $_SERVER["SCRIPT_FILENAME"]) {
    header('Location: ../');
    die();
}

require_once('../includes/declarations.inc.php');
require_once('../includes/initialization.inc.php');

use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\Util;

// Only admins can do that
Auth::ensurePermission('admin');

$stdRegexp = '/^[a-z_\-0-9]*$/i';

$role = filter_input(INPUT_POST, 'role', FILTER_VALIDATE_REGEXP, array('options' => array("regexp" => $stdRegexp)));
if ($role === false || $role === '') {
    Util::fatal('Error while creating role: invalid role name provided (' . filter_input(INPUT_POST, 'role') . ')');
}
if (in_array($role, Auth::getAllRoles())) {
    Util::log('error', __FILE__, __LINE__, 'Error while creating role: role ' . $role . ' already exists');
    $_SESSION['error'] = 'Ce rôle existe déjà&nbsp;: ' . $role;
    header('Location:../?page=admin/manageusers.inc.php');
    die();
}

$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_AMP));
if ($description === false) {
    //Role description may be null
    $description = null;
}

$pdo = Util::getDbConnection();
try {
    $pdo->beginTransaction();

    $addRole = $pdo->prepare('insert into roles (role, description) values(?, ?)');
    $addRole->execute(array($role, $description));

    $pdo->commit();
    Util::log('info', __FILE__, __LINE__, 'Role ' . $role . ' created');
} catch (PDOException $e) {
    $pdo->rollBack();
    Util::fatal('Error while creating role ' . $role . ': ' . $e->getMessage());
}

header('Location:../?page=admin/manageusers.inc.php');

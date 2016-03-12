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

require_once('../includes/declarations.inc.php');
require_once('../includes/initialization.inc.php');

use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\Util;

// Only admins can do that
Auth::ensurePermission('admin');

$stdRegexp = '/^[a-z_\-0-9]*$/i';

$role = filter_input(INPUT_POST, 'role', FILTER_VALIDATE_REGEXP, array('options' => array("regexp" => $stdRegexp)));
if ($role === false || $role === '') {
    Util::fatal('Invalid role name provided: ' . filter_input(INPUT_POST, 'role'));
}
if (in_array($role, Auth::getAllRoles())) {
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
} catch (PDOException $e) {
    $pdo->rollBack();
    Util::fatal('Impossible to create role ' . $role . ': ' . $e);
}

header('Location:../?page=admin/manageusers.inc.php');

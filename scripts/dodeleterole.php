<?php

/**
 * scripts/dodeleterole.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 * 
 * This script deletes a given role, if unused.
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

$role = filter_input(INPUT_GET, 'role', FILTER_VALIDATE_REGEXP, array('options' => array("regexp" => $stdRegexp)));
if ($role === false || $role === '') {
    Util::fatal('Invalid role name provided: ' . filter_input(INPUT_POST, 'role'));
}
if (!in_array($role, Auth::getAllRoles())) {
    Util::fatal('Invalid role provided: ' . $role);
}
if (!Auth::isRoleUnused($role)) {
    Util::fatal('Role ' . $role . ' is in use and cannot be deleted.');
}
if ($role === 'admin') {
    Util::fatal('The admin role cannot be deleted.');
}

$pdo = Util::getDbConnection();
try {
    $pdo->beginTransaction();
    
    $deletePermissions = $pdo->prepare('delete from `roles-permissions` where role=?');
    $deletePermissions->execute(array($role));
    
    $deleteRole = $pdo->prepare('delete from roles where role=?');
    $deleteRole->execute(array($role));
    
    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    Util::fatal('Impossible to delete role ' . $role . ': ' . $e);
}

header('Location:../?page=admin/manageusers.inc.php');

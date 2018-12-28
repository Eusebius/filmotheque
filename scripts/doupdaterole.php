<?php

/**
 * scripts/doupdaterole.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 * 
 * This script updates the description and permissions of a given role.
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
    Util::fatal('Invalid role name provided: ' . filter_input(INPUT_POST, 'role'));
}
if (!in_array($role, Auth::getAllRoles())) {
    Util::fatal('Invalid role name provided: ' . $role);
}
if ($role === 'admin') {
    Util::fatal('The admin role cannot be updated.');
}

$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_AMP));
//If $description is false, do nothing, do not update it
if ($description === '') {
    $description = null;
}
if ($description === Auth::getDescriptionOfRole($role)) {
    $description = false; // Description is unchanged, do nothing
}

$permissions = filter_input(INPUT_POST, 'permissions', FILTER_VALIDATE_REGEXP, array('options' => array("regexp" => $stdRegexp), 'flags' => FILTER_REQUIRE_ARRAY));
//$roles may be false or null

if (!is_null($permissions) && $permissions !== false) {
    $validPermissions = Auth::getAllPermissions();
    foreach ($permissions as $permission) {
        if (!in_array($permission, $validPermissions)) {
            Util::fatal('Invalid permission provided: ' . $permission);
        }
    }
} else {
    $permissions = false;
}

if ($permissions === false) {
    $permissions = array();
}
//TODO maybe check before if there is a diff, but beware, 
//order might be different and === is probably not adapted.
//It has to be a set equality.
$pdo = Util::getDbConnection();
try {
    $pdo->beginTransaction();
    
    if ($description !== false) {
        $updateDescription = $pdo->prepare('update roles set description=? where role=?');
        $updateDescription->execute(array($description, $role));
    }
    
    $deletePermissions = $pdo->prepare('delete from `roles-permissions` where role=?');
    $deletePermissions->execute(array($role));
    
    foreach($permissions as $permission) {
        $addPermission = $pdo->prepare('insert into `roles-permissions` (role, permission) values(?, ?)');
        $addPermission->execute(array($role, $permission));
    }
    
    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    Util::fatal('Impossible to update permissions for role ' . $role . ': ' . $e->getMessage());
}

header('Location:../?page=admin/manageusers.inc.php');

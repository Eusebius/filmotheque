<?php

/**
 * auth.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.7
 * 
 * This is the authentication library of the application, to be included in 
 * every page through initialization.inc.php.
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

/**
 * Class providing static authentication and access control functions.
 *
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.7
 */
class Auth {

    /**
     * Authenticate a user with his login and password.
     * @param type $login The login provided by the user.
     * @param type $password The password provided by the user.
     * @return bool true if authentication is successful, false otherwise.
     * @since 0.2.8
     * //TODO wipe passwords from memory
     * //TODO manage hashed passwords
     */
    static function authenticateUser($login, $password) {
        $result = false;
        $pdo = Util::getDbConnection();

        $getUser = $pdo->prepare('select login, password from users where login=?');
        $getUser->execute(array($login));
        
        $nbUsers = $getUser->rowCount();
        if ($nbUsers === 1) {
            $userArray = $getUser->fetchall(PDO::FETCH_ASSOC);
            $dbpassword = $userArray[0]['password'];
            if ($password === $dbpassword) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Make sure a user is authenticated, otherwise redirect to login form.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.7
     */
    static function ensureAuthenticated() {
        if (!self::isAuthenticated()) {
            $_SESSION['nextPage'] = $_SERVER['SCRIPT_NAME'];
            Util::gotoLoginPage();
        }
    }

    /**
     * If the authenticated user (if any) doesn't have a given role, redirect her
     * to the application home page.
     * @param \string $role The role to check.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.7
     */
    static function ensureRole($role) {
        if (!self::hasRole($role)) {
            Util::gotoMainPage();
        }
    }

    /**
     * If the authenticated user (if any) doesn't have a given permission, 
     * redirect her to the application home page.
     * @param \string $perm The permission to check.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.7
     */
    static function ensurePermission($perm) {
        if (!self::hasPermission($perm)) {
            Util::gotoMainPage();
        }
    }

    /**
     * Check whether the authenticated user, if it exists, has a given role.
     * @param \string $role The role to check.
     * @return boolean True if the user has the role, false otherwise.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.7
     */
    static function hasRole($role) {
        $result = false;
        if (isset($_SESSION['auth']) && $_SESSION['auth'] !== '') {
            $roles = Auth::getRoles($_SESSION['auth']);
            if (in_array($role, $roles, true)) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Lists the current roles of a given user.
     * Roles are fetched directly from the database, not from a cache.
     * @param string $login The login of the user.
     * @return array An array of the user's roles.
     * @since 0.2.8
     */
    static function getRoles($login) {
        $pdo = Util::getDbConnection();
        $getRoles = $pdo->prepare('select role from `users-roles` where login=?');
        $getRoles->execute(array($login));
        $rolesResult = $getRoles->fetchAll(PDO::FETCH_ASSOC);
        $roles = array();
        foreach ($rolesResult as $roleRecord) {
            $roles[] = $roleRecord['role'];
        }
        return $roles;
    }

    /**
     * Check whether the authenticated user, if it exists, has a given permission.
     * @param \string $perm The permission to check.
     * @return boolean True if the user has the permission, false otherwise.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.7
     */
    static function hasPermission($perm) {
        $result = false;
        if (isset($_SESSION['auth']) && ($_SESSION['auth'] !== '')) {
            $permissions = Auth::getPermissions($_SESSION['auth']);
            if (in_array($perm, $permissions, true)) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Lists the current permissions of a given user.
     * Permissions are fetched directly from the database, not from a cache.
     * @param string $login The login of the user.
     * @return array An array of the user's permissions.
     * @since 0.2.8
     */
    static function getPermissions($login) {
        $pdo = Util::getDbConnection();
        $getPermissions = $pdo->prepare('select permission from `users-roles`, `roles-permissions` '
                . 'where login=? and `users-roles`.role = `roles-permissions`.role');
        $getPermissions->execute(array($login));
        $permissionsResult = $getPermissions->fetchAll(PDO::FETCH_ASSOC);
        $permissions = array();
        foreach ($permissionsResult as $permissionRecord) {
            $permissions[] = $permissionRecord['permission'];
        }
        return $permissions;
    }

    /**
     * Tells whether a user is currently authenticated or not.
     * @return boolean True if a user is authenticated, false otherwise.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.7
     */
    static function isAuthenticated() {
        $result = false;
        if (isset($_SESSION['auth']) && $_SESSION['auth'] !== '') {
            $result = true;
        }
        return $result;
    }

    /**
     * Disconnects any authenticated user from the application and return to the 
     * login form.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.7
     */
    static function disconnect() {
        if (self::isAuthenticated()) {
            unset($_SESSION['auth']);
        }
        Util::gotoLoginPage();
    }
}
?>
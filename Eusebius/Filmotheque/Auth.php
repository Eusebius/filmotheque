<?php

/**
 * Eusebius/Filmotheque/Auth.php
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

namespace Eusebius\Filmotheque;

use PDO,
    PDOException;

/**
 * Class providing static authentication and access control functions.
 *
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.7
 */
class Auth {

    /**
     * Prepares a password for storage in the database.
     * Hash it with SHA256, encode it in base64, and then hash/salt the result 
     * with the default options of password_hash.
     * @param string $password The cleartext password.
     * @return string The hash to be stored in the database.
     * @since 0.2.8
     */
    static function encryptPassword($password) {
        //TODO add an encryption layer in the end, with a key known only to PHP.
        //Avoid truncating the password to 72 chars (by hashing it a first time), 
        //or on 0x00 (by encoding it in base64)
        $preparedPassword = base64_encode(hash('sha256', $password, true));
        $encPassword = password_hash($preparedPassword, PASSWORD_DEFAULT);
        return $encPassword;
    }

    /**
     * Checks that a cleartext password matches with a given hash (as stored in 
     * the database).
     * This function is to be used jointly with encryptPassword, and uses the 
     * same process (SHA256 hashing and base64 encoding of the cleartext 
     * password before passing it to the standard PHP method).
     * @param string $clearTextPassword The cleartext password, as provided by the user.
     * @param string $encPassword The reference hash, as stored in the database.
     * @return boolean True if the password matches, false otherwise.
     * @since 0.2.8
     */
    static function checkPassword($clearTextPassword, $encPassword) {
        $preparedPassword = base64_encode(hash('sha256', $clearTextPassword, true));
        return password_verify($preparedPassword, $encPassword);
    }

    /**
     * Authenticate a user with his login and password.
     * @param string $login The login provided by the user.
     * @param string $password The password provided by the user.
     * @return bool true if authentication is successful, false otherwise.
     * @since 0.2.8
     * //TODO wipe passwords from memory
     */
    static function authenticateUser($login, $password) {
        $result = false;
        $pdo = Util::getDbConnection();

        try {
            $getUser = $pdo->prepare('select login, password from users where login=?');
            $getUser->execute(array($login));
        } catch (PDOException $e) {
            Util::fatal('Error while authenticating user "' . $login . '" from ' . $remoteIP . ': ' . $e->getMessage());
        }

        $nbUsers = $getUser->rowCount();
        if ($nbUsers === 1) {
            $userArray = $getUser->fetchall(PDO::FETCH_ASSOC);
            $dbpassword = $userArray[0]['password'];
            if (Auth::checkPassword($password, $dbpassword)) {
                $result = true;
            }
        }

        $remoteIP = (is_string($_SERVER['REMOTE_ADDR']) ?
                $_SERVER['REMOTE_ADDR'] : '[unknown IP]');
        if ($result === true) {
            Util::log('info', 'auth', 'Successful authentication of user "'
                    . $login . '" from ' . $remoteIP . '.');
        } else {
            Util::log('warning', 'auth', 'Failed authentication of user "'
                    . $login . '" from ' . $remoteIP . '.');
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
            $_SESSION['nextPage'] = Util::getRequestURI();
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
     * Checks whether a role is unused in the application, i.e. no user has this role.
     * @param string $role The role to check.
     * @return bool True if the role is valid and currently unused, false otherwise.
     * @since 0.3.2
     */
    static function isRoleUnused($role) {
        $result = false;
        if (in_array($role, Auth::getAllRoles())) {
            $pdo = Util::getDbConnection();
            try {
                $countUsers = $pdo->prepare('select count(login) as count from `users-roles` where role=?');
                $countUsers->execute(array($role));
                if ($countUsers->rowCount() !== 1) {
                    Util::fatal('Unexpected database error with query: ' . $countUsers->queryString);
                } else {
                    $count = $countUsers->fetchAll(PDO::FETCH_ASSOC)[0]['count'];
                    if ($count === '0') {
                        $result = true;
                    }
                }
            } catch (PDOException $e) {
                Util::fatal('Error while testing role ' . $role . ': ' . $e->getMessage());
            }
        }
        return $result;
    }

    /**
     * Lists all the roles of the application.
     * @return array The list of existing roles, as an array of strings.
     * @since 0.3.2
     */
    static function getAllRoles() {
        $result = array();
        $pdo = Util::getDbConnection();
        try {
            $listRoles = $pdo->prepare('select role from roles');
            $listRoles->execute();
        } catch (PDOException $e) {
            Util::fatal('Error while listing roles: ' . $e->getMessage());
        }
        $roleArray = $listRoles->fetchAll(PDO::FETCH_ASSOC);
        foreach ($roleArray as $roleEntry) {
            $result[] = $roleEntry['role'];
        }
        return $result;
    }

    /**
     * Lists all the permissions of the application.
     * @return array The list of existing permissions, as an array of strings.
     * @since 0.3.2
     */
    static function getAllPermissions() {
        $result = array();
        $pdo = Util::getDbConnection();
        try {
            $listPermissions = $pdo->prepare('select permission from permissions');
            $listPermissions->execute();
        } catch (PDOException $e) {
            Util::fatal('Error while listing permissions: ' . $e->getMessage());
        }
        $permissionArray = $listPermissions->fetchAll(PDO::FETCH_ASSOC);
        foreach ($permissionArray as $permissionEntry) {
            $result[] = $permissionEntry['permission'];
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
        try {
            $getRoles = $pdo->prepare('select role from `users-roles` where login=?');
            $getRoles->execute(array($login));
        } catch (PDOException $e) {
            Util::fatal('Error while listing roles of user ' . $login . ': ' . $e->getMessage());
        }
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
        try {
            //TODO I think we need a left outer join here
            $getPermissions = $pdo->prepare('select permission from `users-roles`, `roles-permissions` '
                    . 'where login=? and `users-roles`.role = `roles-permissions`.role');
            $getPermissions->execute(array($login));
            $permissionsResult = $getPermissions->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal('Error while listing permissions of user ' . $login . ': ' . $e->getMessage());
        }
        $permissions = array();
        foreach ($permissionsResult as $permissionRecord) {
            $permissions[] = $permissionRecord['permission'];
        }
        return $permissions;
    }

    /**
     * Lists the current permissions of a given role.
     * Permissions are fetched directly from the database, not from a cache.
     * @param string $role The considered role.
     * @return array The permissions associated to the role, as an array of strings.
     * @since 0.3.2
     */
    static function getPermissionsOfRole($role) {
        $pdo = Util::getDbConnection();
        try {
            $getPermissions = $pdo->prepare('select permission from `roles-permissions` '
                    . 'where role=?');
            $getPermissions->execute(array($role));
            $permissionsResult = $getPermissions->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal('Error while listing permissions of role ' . $role . ': ' . $e->getMessage());
        }
        $permissions = array();
        foreach ($permissionsResult as $permissionRecord) {
            $permissions[] = $permissionRecord['permission'];
        }
        return $permissions;
    }

    /**
     * Gets the description of a given role.
     * @param string $role The considered role.
     * @return string The description of the role.
     * @since 0.3.2
     */
    static function getDescriptionOfRole($role) {
        $description = null;
        $pdo = Util::getDbConnection();
        try {
            $getDescription = $pdo->prepare('select description from roles '
                    . 'where role=?');
            $getDescription->execute(array($role));
            if ($getDescription->rowCount() === 1) {
                $description = $getDescription->fetchAll(PDO::FETCH_ASSOC)[0]['description'];
            }
        } catch (PDOException $e) {
            Util::fatal('Error while getting description of role ' . $role . ': ' . $e->getMessage());
        }
        return $description;
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
            session_unset();
            session_destroy();
            session_regenerate_id(true);
        }
        Util::gotoLoginPage();
    }

}

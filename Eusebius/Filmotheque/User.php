<?php

/**
 * Eusebius/Filmotheque/User.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
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
    PDOException,
    Eusebius\Exceptions\UserNotFoundException;

//TODO enforce authorizations on this class's methods

/**
 * Class representing a user in the system.
 *
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 */
class User {

    private $login;
    private $email;
    private $roles;
    private $permissions;

    /**
     * Create either a new user object.
     * The new user object is either empty, or populated by fetching data from the database based on the user's login.
     * An authenticated can only fetch his own user object, unless he is an administrator.
     * In this case, he can create an emtpy user object or fetch any user object.
     * 
     * @param string $login The login for the user, or null to create an empty user object.
     * @since 0.3.2
     */
    public function __construct($login = NULL) {
        if (is_null($login)) {
            //One has to be an administrator to create a new user
            Auth::ensurePermission('admin');
            //Create a new empty user
            $this->roles = array();
            $this->permissions = array();
        } else if (!is_string($login)) {
            Util::fatal("Login provided for user creation is not a string - " . var_dump($login));
        } else {
            //An authenticated user can only fetch his own record, unless he is an administrator
            if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== $login) {
                Auth::ensurePermission('admin');
            }
            //Fetch a user from the database based on his login
            $this->login = $login;
            $this->roles = array();
            $this->permissions = array();
            $pdo = Util::getDbConnection();
            try {
                $getUser = $pdo->prepare('select `users`.login, `users`.email, '
                        . '`users-roles`.role, `roles-permissions`.permission '
                        . 'from users left outer join `users-roles` on users.login = `users-roles`.login '
                        . 'left outer join `roles-permissions` on `users-roles`.role = `roles-permissions`.role '
                        . 'where `users`.login=?');
                $getUser->execute(array($login));
            } catch (PDOException $e) {
                Util::fatal($e->getMessage());
            }

            $nbUsers = $getUser->rowCount();
            if ($nbUsers === 0) {
                throw new UserNotFoundException("User $login could not be found in the database.");
            } else {
                $userArray = $getUser->fetchall(PDO::FETCH_ASSOC);
                $this->email = $userArray[0]['email'];
                foreach ($userArray as $userEntry) {
                    if (!in_array($userEntry['role'], $this->roles)) {
                        $this->roles[] = $userEntry['role'];
                    }
                    if (!in_array($userEntry['permission'], $this->permissions)) {
                        $this->permissions[] = $userEntry['permission'];
                    }
                }
            }
        }
    }

    /**
     * Fetch all existing users in the database.
     * Only authenticated administrators can execute this method.
     * @return array An array of User objects.
     * @since 0.3.2
     */
    public static function fetchAllUsers() {
        Auth::ensurePermission('admin');
        $userArray = array();
        $pdo = Util::getDbConnection();
        try {
            $fetchUserLogins = $pdo->prepare('select login from users');
            $fetchUserLogins->execute();
        } catch (PDOException $e) {
            Util::fatal($e->getMessage());
        }
        $loginArray = $fetchUserLogins->fetchall(PDO::FETCH_ASSOC);
        foreach ($loginArray as $loginEntry) {
            $userArray[] = new User($loginEntry['login']);
        }
        return $userArray;
    }

    /**
     * Get the user's login.
     * @return string The user's login.
     * @since 0.3.2
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * Get the user's e-mail.
     * @return string the user's e-mail.
     * @since 0.3.2
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Get the user's roles.
     * @return array The user's roles, as an array of strings.
     * @since 0.3.2
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * Get the user's permissions.
     * @return array The user's permissions, as an array of strings.
     * @since 0.3.2
     */
    public function getPermissions() {
        return $this->permissions;
    }

}

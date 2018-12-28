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
    Eusebius\Exceptions\UserNotFoundException,
    Eusebius\Exceptions\UserExistsException,
    Eusebius\Exceptions\UnauthorizedException;

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
    private $password; //Not always instantiated

    /**
     * Create either a new user object.
     * The new user object is either empty, or populated by fetching data from the database based on the user's login.
     * An authenticated can only fetch his own user object, unless he is an administrator.
     * In this case, he can create an emtpy user object or fetch any user object.
     *
     * @param string $login The login for the user, or null to create an empty user object.
     * @throws UserNotFoundException If a login has been provided but the user does not exist.
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
            Util::fatal('Error while building user object: Login provided for user creation is not a string - ' . var_dump($login));
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
                Util::fatal('Error while retrieving data to build ' . 'user object for user ' . $login . ': '. $e->getMessage());
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
            Util::fatal('Error while retrieving user list: ' . $e->getMessage());
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
     * Set a user's login.
     * Nothing is pushed to the database at this step.
     * @param string $login The new login.
     * @since 0.3.2
     */
    public function setLogin($login) {
        Auth::ensurePermission('admin');
        //TOOD validate parameter type
        $this->login = $login;
    }

    /**
     * Set a user's e-mail.
     * Nothing is pushed to the database at this step.
     * @param string $email The new e-mail.
     * @since 0.3.2
     */
    public function setEmail($email) {
        //TOOD validate parameter type
        $this->email = $email;
    }

    /**
     * Set a user's password.
     * Nothing is pushed to the database at this step.
     * @param string $password The new password.
     * @since 0.3.2
     */
    public function setPassword($password) {
        //TODO ensure that this variable is cleaned in memory
        //TOOD validate parameter type
        $this->password = $password;
    }

    /**
     * Set a user's roles.
     * Nothing is pushed to the database at this step.
     * @param array $roles The roles of the user, as an array of strings.
     */
    public function setRoles(array $roles) {
        //TODO validate parameter type
        //TODO check that the roles are valid
        $this->roles = $roles;
    }

    /**
     * Get the user's e-mail.
     * @return string the user's e-mail.
     * @since 0.3.2
     */
    public function getEmail() {
        //TODO validate e-mail again?
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

    /**
     * Create the current user in the database.
     * Triggers a fatal error if the user is incomplete. //TODO use an exception
     * @throws UserExistsException If the login already exists.
     */
    public function createInDB() {
        if (is_null($this->login) || is_null($this->email) || is_null($this->password) || is_null($this->roles)) {
            Util::fatal('Error while trying to create an incomplete user object in database (' . var_dump($this->login) . ', ' . var_dump($this->email) . ', ' . var_dump($this->roles) . ')');
        }
        $pdo = Util::getDbConnection();
        $pdo->beginTransaction();
        $encPassword = Auth::encryptPassword($this->password);
        $insertUser = $pdo->prepare('insert into users (login, email, password) values(?, ?, ?)');
        try {
            $insertUser->execute(array($this->login, $this->email, $encPassword));
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($insertUser->errorCode() === '23000') {
                throw new UserExistsException('User already exists: ' . $this->login);
            } else {
                Util::debug($insertUser->errorInfo());
                Util::fatal('Error while creating user ' . $this->login . ': ' . $e->getMessage());
            }
        }
        $pdo->commit();
        $this->writeRolesInDB();
        Util::log('info', 'admin', 'User ' . $this->login
                . ' created with roles: '
                . implode(', ', $this->roles) . '.');
    }

    /**
     * Delete a user from the database.
     * Triggers a fatal error if the login is not provided (or in other unusual cases).
     * @since 0.3.2
     */
    public function deleteFromDB() {
        if (is_null($this->login)) {
            Util::fatal('Error while trying to delete an incomplete user (no login)');
        }

        $pdo = Util::getDbConnection();
        $pdo->beginTransaction();

        try {
            $deleteRoles = $pdo->prepare('delete from `users-roles` where login=?');
            $deleteRoles->execute(array($this->login));
        } catch (PDOException $e) {
            $pdo->rollBack();
            Util::fatal('Error while deleting roles of user ' . $this->login . ': ' . $e->getMessage());
        }
        try {
            $deleteUser = $pdo->prepare('delete from users where login=?');
            $deleteUser->execute(array($this->login));
        } catch (PDOException $e) {
            $pdo->rollBack();
            Util::fatal('Error while deleting user ' . $this->login . ': ' . $e->getMessage());
        }
        $pdo->commit();
        Util::log('info', 'admin', 'User ' . $this->login
                . ' deleted');
    }

    /**
     * Sets a new e-mail in the (already valid) object, and updates the value in the database.
     * @param string $email The new e-mail of the user.
     * @throws UnauthorizedException If the authenticated user is neither an admin nor the corresponding user.
     * @since 0.3.2
     */
    public function updateEmail($email) {
        if (is_null($this->login)) {
            Util::fatal('Error while setting the e-mail of an ' . 'incomplete user (no login).');
        } else if (!Auth::hasPermission('admin') && $_SESSION['auth'] !== $this->login) {
            //The authenticated user has no right to update the user object
            throw new UnauthorizedException('The authenticated user ('
            . $_SESSION['auth'] . ') is neither an admin nor the user to be updated ('
            . $this->login . ').');
        } else if ($this->email !== $email) {
            $this->setEmail($email);
            $pdo = Util::getDbConnection();
            try {
                $updateEmail = $pdo->prepare('update users set email=? where login=?');
                $updateEmail->execute(array($email, $this->login));
                Util::log('info', 'admin', 'E-mail of user ' . $this->login  . ' updated to ' . $this->email);
            } catch (PDOException $e) {
                Util::fatal('Error while updating e-mail of user ' . $this->login . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Sets the roles in the (already valid) object, and updates the database accordingly.
     * @param array $roles The new roles for the user, as an array of strings.
     * @throws UnauthorizedException If the authenticated user is not an admin, or he is the user to be updated.
     * @since 0.3.2
     */
    public function updateRoles($roles) {
        if (is_null($this->login)) {
            Util::fatal('Error while trying to update the roles ' . 'of an incomplete user (no login)');
        } else if (!Auth::hasPermission('admin') || $_SESSION['auth'] === $this->login) {
            //The authenticated user has no right to update the user object
            throw new UnauthorizedException('The authenticated user ('
            . $_SESSION['auth'] . ') is not an admin authorized to update the roles of user '
            . $this->login . ')');
        } else {
            $this->setRoles($roles);
            $this->writeRolesInDB();
            Util::log('info', 'admin', 'Roles of user ' . $this->login
                    . ' updated to (' . implode(', ', $this->roles) . ')');
        }
    }

    /**
     * Updates the database for the object's user so that it reflects the roles currently set in the object.
     * Does nothing if either the user's login or his roles are not set in the object.
     * This method starts a new transaction, so any pending transaction should be taken care of before, or it will be committed automatically (for MySQL at least).
     */
    private function writeRolesInDB() {
        if (!is_null($this->login) && !is_null($this->roles)) {
            $pdo = Util::getDbConnection();
            $pdo->beginTransaction();
            try {
                $deleteRoles = $pdo->prepare('delete from `users-roles` where login=?');
                $deleteRoles->execute(array($this->login));
            } catch (PDOException $e) {
                $pdo->rollBack();
                Util::fatal('Error while deleting/updating the roles ' . 'of user ' . $this->login . ': ' . $e->getMessage());
            }
            foreach ($this->roles as $role) {
                try {
                    $addRole = $pdo->prepare('insert into `users-roles` (login, role) values(?, ?)');
                    $addRole->execute(array($this->login, $role));
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    Util::fatal('Error while adding role '  . $role. ' to user ' . $this->login . ': ' . $e->getMessage());
                }
            }
            $pdo->commit();
        }
    }

}

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
 * Make sure a user is authenticated, otherwise redirect to login form.
 */
function ensureAuthenticated() {
    if (!isAuthenticated()) {
        $_SESSION['nextPage'] = $_SERVER['SCRIPT_NAME'];
        header('Location: ' . $_SESSION['loginURL']);
    }
}

/**
 * If the authenticated user (if any) doesn't have a given role, redirect her
 * to the application home page.
 * @param \string $role The role to check.
 */
function ensureRole($role) {
    if (!hasRole($role)) {
        header('Location: ' . $_SESSION['homeURL']);
        exit();
    }
}

/**
 * Check whether the authenticated user, if it exists, has a given role.
 * @param \string $role The role to check.
 * @return boolean True if the user has the role, false otherwise.
 */
function hasRole($role) {
    if (isset($_SESSION['auth']['roles'])) {
        $roles = $_SESSION['auth']['roles'];
        if (in_array($role, $roles, true)) {
            return true;
        }
    }
    return false;
}

/**
 * Tells whether a user is currently authenticated or not.
 * @return boolean True if a user is authenticated, false otherwise.
 */
function isAuthenticated() {
    if (isset($_SESSION['auth']) 
            && isset($_SESSION['auth']['login']) 
            && $_SESSION['auth']['login'] !== '') {
        return true;
    } else {
        return false;
    }
}

/**
 * Disconnects any authenticated user from the application and return to the 
 * login form.
 */
function disconnect() {
    if (isAuthenticated()) {
        unset($_SESSION['auth']);
    }
    header('Location: http://' . $_SESSION['baseuri'] . '/login.php');
}

?>
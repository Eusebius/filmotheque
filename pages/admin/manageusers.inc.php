<?php
/**
 * pages/admin/manageusers.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 * 
 * User management administration page
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
require_once('includes/declarations.inc.php');
require_once('includes/initialization.inc.php');

use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\User;

Auth::ensurePermission('admin');

?>

<h2>Liste des utilisateurs existants</h2>
<table border="1">
    <tr>
        <th>Login</th>
        <th>E-mail</th>
        <th>Rôles</th>
        <th>Permissions</th>
        <th>&nbsp;</th>
    </tr>
    <?php
        $users = User::fetchAllUsers();
        foreach ($users as $user) {
            echo '<tr><td>' . $user->getLogin() . '</td><td>' 
                    . $user->getEmail() . '</td><td>'
                    . implode(', ', $user->getRoles()) . '</td><td>'
                    . implode(', ', $user->getPermissions()) . '</td><td>'
                    . '</td></tr>';
        }
    ?>
</table>
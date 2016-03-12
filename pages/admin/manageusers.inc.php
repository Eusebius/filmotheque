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

if (isset($_SESSION['error'])) {
    ?>
    <div id="error"><?php echo $_SESSION['error']; ?></div>
    <?php
    unset($_SESSION['error']);
}
?>

<table>
    <tr>
        <td>
            <h2>Ajouter un nouvel utilisateur</h2>

            <form action="scripts/doadduser.php" method="POST">
                <table>
                    <tr><td>Login&nbsp;:</td><td><input type="text" name="login" /></td></tr>
                    <tr><td>E-mail&nbsp;:</td><td><input type="text" name="email" /></td></tr>
                    <tr><td>Mot de passe (temporaire)&nbsp;:</td><td><input type="password" name="password" /></td></tr>
                    <tr>
                        <td>Rôles&nbsp;:</td>
                        <td>
                            <select name="roles[]" multiple>
                                <?php
                                $roles = Auth::getAllRoles();
                                foreach ($roles as $role) {
                                    echo '<option value="' . $role . '">' . $role . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan="2"><input type="submit" value="Créer l'utilisateur" /></td></tr>
                </table>
            </form>
        </td>
        <td>
            <h2>Ajouter un nouveau rôle</h2>
            <form action="scripts/doaddrole.php" method="post">
                <table>
                    <tr><td>Nom du rôle&nbsp;:</td><td><input type="text" name="role" /></td></tr>
                    <tr><td>Description&nbsp;:</td><td><input type="text" name="description" /></td></tr>
                    <tr><td colspan="2"><input type="submit" value="Créer le rôle" /></td></tr>
                </table>
            </form>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <h2>Liste des utilisateurs existants</h2>
            <table border="1">
                <tr>
                    <th rowspan="2">Login</th>
                    <th rowspan="2">E-mail</th>
                    <?php echo '<th colspan="' . count($roles) . '">Rôles</th>'; ?>
                    <!-- <th rowspan="2">Permissions</th> -->
                    <th rowspan="2">&nbsp;</th>
                    <th rowspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <?php
                    foreach ($roles as $role) {
                        echo '<th>' . $role . '</th>';
                    }
                    ?>
                </tr>
                <?php
                $users = User::fetchAllUsers();
                foreach ($users as $user) {
                    echo '<form action="scripts/doupdateuser.php" method="POST">';
                    echo '<input type="hidden" name="login" value="' . $user->getLogin() . '" />';
                    echo '<tr><td>' . $user->getLogin() . '</td><td><input type="text" name="email" value="'
                    . $user->getEmail() . '" /></td>' . "\n";
                    foreach ($roles as $role) {
                        echo '<td align="center"><input type="checkbox" name="roles[]" value="';
                        echo $role . '" ';
                        if (in_array($role, $user->getRoles())) {
                            echo 'checked ';
                        }
                        if ($user->getLogin() === $_SESSION['auth']) {
                            echo 'disabled ';
                        }
                        echo '/></td>' . "\n";
                    }
                    //echo '<td>' . implode(', ', $user->getPermissions()) . '</td>';
                    echo '<td><input type="submit" value="Mettre à jour" /></form></td>' . "\n";
                    echo '<td>';
                    if ($user->getLogin() !== $_SESSION['auth']) {
                        echo '<a href="scripts/dodeleteuser.php?login=' . $user->getLogin()
                        . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer l\\\'utilisateur '
                        . $user->getLogin() . ' ?\')">Supprimer l\'utilisateur</a>';
                    }
                    echo '</td></tr>' . "\n";
                }
                ?>
            </table>

        </td>
    </tr>
    <tr>
        <td colspan="2">

            <h2>Liste des rôles disponibles</h2>
            <table border="1">
                <tr>
                    <th rowspan="2">Rôle</th>
                    <th rowspan="2">Description</th>
                    <?php
                    $permissions = Auth::getAllPermissions();
                    echo '<th colspan="' . count($permissions) . '">Permissions</th>';
                    ?>
                    <!-- <th rowspan="2">Permissions</th> -->
                    <th rowspan="2">&nbsp;</th>
                    <th rowspan="2">&nbsp;</th>
                </tr>
                <tr>
                    <?php
                    foreach ($permissions as $permission) {
                        echo '<th>' . $permission . '</th>';
                    }
                    ?>
                </tr>
                <?php
                foreach ($roles as $role) {
                    $rolePermissions = Auth::getPermissionsOfRole($role);
                    $roleDescription = Auth::getDescriptionOfRole($role);
                    echo '<tr>';
                    if ($role !== 'admin') {
                        echo '<form action="scripts/doupdaterole.php" method="POST">';
                    }
                    echo '<input type="hidden" name="role" value="' . $role . '" />' . "\n";
                    echo '<td>' . $role . '</td>' . "\n";
                    echo '<td>';
                    if ($role !== 'admin') {
                        echo '<input type="text" name="description" size="50" value="' . $roleDescription . '" />';
                    } else {
                        echo  $roleDescription;
                    }
                    echo  '</td>' . "\n";
                    foreach ($permissions as $permission) {
                        echo '<td align="center"><input type="checkbox" name="permissions[]" value="';
                        echo $permission . '" ';
                        if (in_array($permission, $rolePermissions)) {
                            echo 'checked ';
                        }
                        if ($role === 'admin') {
                            echo 'disabled ';
                        }
                        echo '/></td>' . "\n";
                    }
                    if ($role !== 'admin') {
                        echo '<td><input type="submit" value="Mettre à jour" /></td>';
                    }
                    echo '<td>';
                    if ($role !== 'admin' && Auth::isRoleUnused($role)) {
                        echo '<a href="scripts/dodeleterole.php?role=' . $role . '">Supprimer le rôle</a>';
                    }
                    echo '</td></form>';
                    echo '</tr>';
                }
                ?>

        </td>
    </tr>
</table>
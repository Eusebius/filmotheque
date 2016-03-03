<?php
/**
 * testing/testpwd.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.8
 * 
 * This is a script for testing password encryption and verification mechanisms.
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

$testHash = Auth::password_encrypt("test");
echo "Haché/salé de \"test\"&nbsp: $testHash<br />";

if (isset($_POST['password']) && ($_POST['password'] !== '')) {
    $password = $_POST['password'];
    $encPassword = Auth::password_encrypt($password);
    echo "Mot de passe tapé&nbsp;: $password<br />";
    echo "Mot de passe haché/salé&nbsp: $encPassword<br />";
    
    if (isset($_POST['hash']) && ($_POST['hash'] !== '')) {
        $hash = $_POST['hash'];
        if (Auth::password_check($password, $hash)) {
            echo "Le mot de passe et le haché fourni correspondent.<br />";
        } else {
            echo "Le mot de passe et le haché fourni ne correspondent pas.<br />";
        }
    } else {
        if (Auth::password_check($password, $testHash)) {
            echo "Le mot de passe correspond avec le haché/salé de \"test\".<br />";
        } else {
            echo "Le mot de passe ne correspond pas avec le haché/salé de \"test\".<br />";
        }
    }
}
?>

<form method="POST">
    Mot de passe&nbsp;: <input type="password" name="password" />
    <br />
    Hash à vérifier&nbsp;: <input type="text" name="hash" />
    <br />
    <input type="submit">
</form>
<?php
/**
 * login.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.7
 * 
 * This is the authentication page of the application.
 */
/*
  Filmothèque
  Copyright (C) 2012-2019 Eusebius (eusebius@eusebius.fr)

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <title>Filmothèque</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="style.css"/>
    </head>

    <body>
        <div class="login">
            <h2>Authentification</h2>
            <form action="scripts/logon.php" method="POST">
                <table id="login">
                    <tr><td>Login&nbsp;:</td><td><input type="text" name="login" /></td></tr>
                    <tr><td>Mot de passe&nbsp;:</td><td><input type="password" name="password" /></td></tr>
                    <tr><td colspan="2"><input type="submit" value="Connexion" /></td></tr>
                </table>
            </form>
        </div>
    </body>
</html>
<?php

/**
 * noaccess.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.3
 * 
 * This is the content shown to authenticated users with no access.
 */
/*
  Filmothèque
  Copyright (C) 2012-2018 Eusebius (eusebius@eusebius.fr)

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

if (__FILE__ === $_SERVER["SCRIPT_FILENAME"]) {
    header('Location: ../');
    die();
}

require_once('includes/declarations.inc.php');
require_once('includes/initialization.inc.php');

use Eusebius\Filmotheque\Auth,
    Eusebius\Filmotheque\Util;

Auth::ensureAuthenticated();

if (Auth::hasPermission('read')) {
    Util::gotoMainPage();
} else {
?>

<p>You have an account all right, but you don't even have the permission to read anything on this website. The administrators here really seem to hate you for some reason.</p>

<?php
}
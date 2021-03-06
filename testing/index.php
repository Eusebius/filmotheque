<?php
/**
 * testing/index.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is a test script intended to demonstrate AJAX auto-completion capabilities
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
require_once('../includes/declarations.inc.php');
require_once('../includes/initialization.inc.php');
use Eusebius\Filmotheque\Auth;
Auth::ensurePermission('admin');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script type="text/javascript" src="test.js"></script>
        <script type="text/javascript">
            window.onload = function () {
                initAutoComplete(document.getElementById('form-test'),
                        document.getElementById('champ-texte'), document.getElementById('bouton-submit'))
            };
        </script>
        <title>AJAX Autocompletion test</title>
    </head>
    <body>
        <form name="form-test" id="form-test"
              action="javascript:alert('soumission de ' + document.getElementById('champ-texte').value)"
              style="margin-left: 50px; margin-top:20px">
            <input type="text" name="champ-texte" id="champ-texte" size="20" />
            <input type="submit" id="bouton-submit">
        </form>

        <p><?php echo (int) "0123abc"; ?></p>
        <p><?php echo 3 + "0123abc"; ?></p>
    </body>
</html>

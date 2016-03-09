<?php
/**
 * sidemenu.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.0
 * 
 * This is the side menu for most application pages.
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

Auth::ensureAuthenticated();
?>

<hr />
<ul>
    <li><a href="?page=listmovies">Liste des films</a></li>
    <?php
    if (Auth::hasPermission('w')) {
        ?>
        <li><a href="?page=addmovie">Ajouter un nouveau film</a></li>
        <?php
    }
    ?>
</ul>
<ul>
    <li><a href="scripts/disconnect.php">Se déconnecter</a></li>
</ul>

<br />
<br />
<hr />
<?php
if (Auth::hasRole('admin')) {
    ?>
    <h3>[Administration]</h3>
    <ul>
        <li><a href="?page=admin/manageusers.inc.php">Gestion des utilisateurs</a></li>
    </ul>
    <hr />
    <br />
    <?php
}
?>
<p>
    Filmothèque by Eusebius<br />
    version <?php echo $_SESSION['config']['version']; ?>
</p>
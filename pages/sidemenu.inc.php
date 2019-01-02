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

if (__FILE__ === $_SERVER["SCRIPT_FILENAME"]) {
    header('Location: ../');
    die();
}

require_once('includes/declarations.inc.php');
require_once('includes/initialization.inc.php');

use Eusebius\Filmotheque\Auth;

Auth::ensureAuthenticated();
?>



<div class="bs-component">
    <div class="btn-group-vertical" style="width: 100%;">
        <?php
        if (Auth::hasPermission('read')) {
            ?>
            <a class="btn btn-primary" href="?page=listmovies" role="button">Liste des films</a>
            <?php
        }
        if (Auth::hasPermission('write')) {
            ?>
            <a class="btn btn-primary" href="?page=addmovie" role="button">Ajouter un nouveau film</a>
            <?php
        }
        ?>
    </div>
</div>

<br />

<div class="card text-white bg-secondary mb-3" style="max-width: 20rem;">
  <div class="card-header text-center">Qualité des supports</div>
  <div class="card-body">
    <table class="table table-hover">
       <tbody>
<?php
            foreach ($colour as $quality => $col) {
                echo '<tr class="table-dark"><td style="background-color:' . $col . ';">';
                echo $quality;
                echo '</td></tr>';
            }
?>
       </tbody>
    </table>
  </div>
</div>

<?php
if (Auth::hasRole('admin')) {
    ?>
    <div class="bs-component">
        <div class="btn-group-vertical" style="width: 100%;">
            <button class="btn btn-info" disabled>Administration</button>
            <a class="btn btn-info" href="?page=admin/manageusers.inc.php" role="button">Gestion des utilisateurs</a>
        </div>
    </div>

    <br />
    <?php
}
?>

<div class="bs-component">
    <div class="btn-group-vertical" style="width: 100%;">
        <a class="btn btn-warning" href="scripts/disconnect.php" role="button">Se déconnecter</a>
    </div>
</div>

<br />

<div class="card text-light bg-secondary mb-3" style="max-width: 20rem;">
  <div class="card-body text-center">
      <a href="https://github.com/Eusebius/filmotheque">Filmothèque by Eusebius</a>
      <br />
      version <?php echo $_SESSION['config']['version'];?>
  </div>
</div>
<?php
/*
    Filmothèque
    Copyright (C) 2012-2013 Eusebius (eusebius@eusebius.fr)

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

$_SESSION['config']=array();

$_SESSION['config']['db_type'] = 'mysql';
$_SESSION['config']['db_server'] = 'localhost';
$_SESSION['config']['db_db'] = 'films';
$_SESSION['config']['db_user'] = 'films';
$_SESSION['config']['db_password'] = 'films';
$_SESSION['config']['db_prefix'] = '';

$_SESSION['config']['version'] = "0.2";

$colour['Full HD'] = "#64FF64";
$colour['Blu-Ray'] = "#55D955";
$colour['DVD'] = "#B7D2FF";
$colour['DivX moyen'] = "#FFFF64";
$colour['DivX médiocre'] = "#FFC264";
$colour['undefined'] = "#FFFFFF";

$rank['Full HD'] = 5;
$rank['Blu-Ray'] = 4;
$rank['DVD'] = 3;
$rank['DivX moyen'] = 2;
$rank['DivX médiocre'] = 1;
$rank['undefined'] = 0;


/*
  print_r($_SESSION['config']);
  echo '<br />';
*/
?>
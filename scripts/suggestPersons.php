<?php
/**
 * includes/suggestPersons.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is a test script aiming to provide suggestions for people to an AJAX
 * script.
 */
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

require_once('../includes/required.inc.php');

//header('Content-Type: text/plain;charset=utf-8');

header('Content-Type: text/xml;charset=utf-8');
echo(utf8_encode("<?xml version='1.0' encoding='UTF-8' ?><options>"));

if (isset($_GET['prefix'])) {
    $prefix = utf8_decode($_GET['prefix']);
} else {
    $prefix = "";
}
$prefix = strtolower($prefix);
generateOptions($prefix);

function generateOptions($prefix) {
  $length = strlen($prefix);
  $MAX_RETURN = 10;
  $i = 0;
  $conn = db_ensure_connected();
  //$getPersons = $conn->prepare('select id_person, name from persons where left(lower(name), ?) = ? or locate(?, lower(name)) <> 0 order by name limit ' . $MAX_RETURN);
  $getPersons = $conn->prepare('(SELECT id_person, name, 1 AS query FROM persons WHERE left( lower( name ) , ? ) = ? ORDER BY name LIMIT 10)'
			       . ' UNION '
			       . '(SELECT id_person, name, 2 FROM persons WHERE locate(?, lower( name ) ) <> 0 AND id_person NOT IN '
			       . '(SELECT id_person FROM persons WHERE left( lower( name ) , ? ) = ?)'
			       . 'ORDER BY name LIMIT 10)'
			       . 'ORDER BY query LIMIT 10');
  $getPersons->execute(array($length, $prefix, $prefix, $length, $prefix));
  //$getPersons->execute(array($length, $prefix, $prefix));
  $persons = $getPersons->fetchall(PDO::FETCH_ASSOC);
  foreach ($persons as $person) {
    echo('<option value="' . htmlspecialchars($person['id_person'])
		     . '">' . htmlspecialchars($person['name']) . '</option>');
    echo "\n";
  }
  /*
  $numOptions = $getPersons->rowCount();
  if ($numOptions < $MAX_RETURN) {
    $limit = $MAX_RETURN - $numOptions;
    $morePersons = $conn->prepare('select id_person, name from persons where locate(?, lower(name)) <> 0 order by name limit ' . $limit);
  }
  */
}


echo("</options>");

?>
<?php
/**
 * listmovies.inc.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is the content for the page listing movies present in the database.
 */
/*
  Filmothèque
  Copyright (C) 2012-2015 Eusebius (eusebius@eusebius.fr)

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
Auth::ensurePermission('read');

// Remember GET parameters
$sortParameters = '';
$listFilterParameters = '';
$catFilterParameters = '';

if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'year') {
        $sortby = 'year';
        $sortParameters = 'sort=year&';
    } else if ($_GET['sort'] == 'rating' && Auth::hasPermission('rating')) {
        $sortby = 'rating';
        $sortParameters = 'sort=rating&';
    } else if ($_GET['sort'] == 'lastseen' && Auth::hasPermission('lastseen')) {
        $sortby = 'lastseen';
        $sortParameters = 'sort=lastseen&';
    } else {
        $sortby = 'title';
        $sortParameters = 'sort=title&';
    }

    if (isset($_GET['order'])) {
        if ($_GET['order'] == 'desc') {
            $order = 'desc';
            $sortParameters .= 'order=desc&';
        } else {
            $order = 'asc';
            $sortParameters .= 'order=asc&';
        }
    }
} else {
    $sortby = 'title';
    $order = 'asc';
}

$conn = Util::getDbConnection();

$getAllShortlists = $conn->prepare('select id_shortlist, listname from `shortlists` order by listname asc');
$getAllShortlists->execute();
$listArray = $getAllShortlists->fetchall(PDO::FETCH_ASSOC);

$getAllCats = $conn->prepare('select category from categories order by category asc');
$getAllCats->execute();
$catArray = $getAllCats->fetchall(PDO::FETCH_ASSOC);

// Rebuild all filtering parameters, except for the first kind (shortlists) which is done inline
// Those variables has to be there when we build the first filter form
$catFilter = array();
foreach ($catArray as $catentry) {
    $cat = $catentry['category'];
    $catn = 'cat' . $cat;
    if (isset($_GET[$catn]) && $_GET[$catn] == '1') {
        $catFilterParameters .= $catn . '=1&';
        array_push($catFilter, $cat);
        Util::debug($cat);
    }
}
?>

<h2>Liste des films</h2>
<table>
    <tr>
        <?php if (Auth::hasPermission('shortlists')) { ?>
            <td>
                Afficher uniquement les shortlists suivantes&nbsp;:<br />
                <form action="" method="GET">
                    <?php
                    $shortlistFilter = array();

                    Util::makeHiddenParameters($sortParameters);
                    Util::makeHiddenParameters($catFilterParameters);
                    foreach ($listArray as $list) {
                        $id = Util::isIntString($list['id_shortlist']);
                        if ($id != false) {
                            $listn = 'list' . $id;
                            echo '<input type="checkbox" name="' . $listn . '" value="1"';
                            if (isset($_GET[$listn]) && $_GET[$listn] == '1') {
                                echo ' checked="checked"';
                                $listFilterParameters .= $listn . '=1&';
                                array_push($shortlistFilter, $id);
                            }
                            echo ' />&nbsp;';
                            echo $list['listname'] . "<br />\n";
                        }
                    }
                    ?>
                    <input type="submit" value="Filtrer"/>
                </form>
            </td>
        <?php } ?>
        <td>
            Afficher uniquement les catégories suivantes&nbsp;:<br />
            <form action="" method="GET">
                <?php
                Util::makeHiddenParameters($sortParameters);
                Util::makeHiddenParameters($listFilterParameters);
                foreach ($catArray as $catentry) {
                    $cat = $catentry['category'];
                    $catn = 'cat' . $cat;
                    echo '<input type="checkbox" name="' . $catn . '" value="1"';
                    if (isset($_GET[$catn]) && $_GET[$catn] == '1') {
                        echo ' checked="checked"';
                    }
                    echo ' />&nbsp;';
                    echo $cat . "<br />\n";
                }
                ?>
                <input type="submit" value="Filtrer"/>
            </form>
        </td>
    </tr>
</table>

<?php
$shortlistWhere = '(1=1';
if (isset($shortlistFilter)) {
    $nShortlists = count($shortlistFilter);
} else {
    $nShortlists = 0;
}
if ($nShortlists > 0) {
    $shortlistWhere .= " and (shortlists.id_shortlist = '" . $shortlistFilter[0] . "'";
    for ($i = 1; $i < $nShortlists; $i++) {
        $shortlistWhere .= " or shortlists.id_shortlist = '" . $shortlistFilter[$i] . "'";
    }
    $shortlistWhere .= ')';
}
$shortlistWhere .= ')';

$catWhere = '(1=1';
$nCats = count($catFilter);
if ($nCats > 0) {
    $catWhere .= " and (`movies-categories`.category = '" . $catFilter[0] . "'";
    for ($i = 1; $i < $nCats; $i++) {
        $catWhere .= " or `movies-categories`.category = '" . $catFilter[$i] . "'";
    }
    $catWhere .= ')';
}
$catWhere .= ')';


$listMovies = $conn->prepare('select movies.id_movie, title, year, originaltitle, imdb_id, rating, lastseen from movies left outer join experience on movies.id_movie = experience.id_movie left outer join `movies-shortlists` on movies.id_movie=`movies-shortlists`.id_movie left outer join shortlists on `movies-shortlists`.id_shortlist=shortlists.id_shortlist left outer join `movies-categories` on movies.id_movie=`movies-categories`.id_movie where ' . $shortlistWhere . ' and ' . $catWhere . ' group by movies.id_movie order by ' . $sortby . ' ' . $order);
$getCategoriesByMovie = $conn->prepare('select id_movie, category from `movies-categories` where id_movie = ?');
$getShortlistsByMovie = $conn->prepare('select id_movie, listname from `movies-shortlists` natural join shortlists where id_movie = ?');
$getBestQuality = $conn->prepare('select quality from `media` natural join `media-quality` natural join `quality` where id_movie = ? order by minwidth desc');

$listMovies->execute();
Util::debug($listMovies->queryString);
Util::debug($listMovies->errorInfo());
$movieArray = $listMovies->fetchall(PDO::FETCH_ASSOC);
$nMovies = $listMovies->rowCount();
?>

<p><em><?php echo $nMovies ?>&nbsp;films distincts</em></p>

<p>
    <a href="?<?php echo $sortParameters; ?>">Réinitialiser tous les filtres</a><br />
    <?php if (Auth::hasPermission('write')) { ?>
        <a href="?page=addmovie">Ajouter un nouveau film</a>
    <?php } ?>
</p>

<table border="1">
    <tr>
        <th>Titre&nbsp;<a href="index.php?<?php echo $catFilterParameters . $listFilterParameters; ?>sort=title&order=asc">⇧</a><a href="index.php?<?php echo $catFilterParameters . $listFilterParameters; ?>sort=title&order=desc">⇩</a></th>
        <th>Année&nbsp;<a href="index.php?<?php echo $catFilterParameters . $listFilterParameters; ?>sort=year&order=asc">⇧</a><a href="index.php?<?php echo $catFilterParameters . $listFilterParameters; ?>sort=year&order=desc">⇩</a></th>
        <th>Catégories</th>
        <?php if (Auth::hasPermission('rating')) { ?>
            <th>Note&nbsp;<a href="index.php?<?php echo $catFilterParameters . $listFilterParameters; ?>sort=rating&order=asc">⇧</a><a href="index.php?<?php echo $catFilterParameters . $listFilterParameters; ?>sort=rating&order=desc">⇩</a></th>
        <?php } ?>
        <?php if (Auth::hasPermission('shortlists')) { ?>
            <th>Shortlists</th>
        <?php } ?>
        <?php if (Auth::hasPermission('lastseen')) { ?>
            <th>Vu le&nbsp;<a href="index.php?<?php echo $catFilterParameters . $listFilterParameters; ?>sort=lastseen&order=asc">⇧</a><a href="index.php?<?php echo $catFilterParameters . $listFilterParameters; ?>sort=lastseen&order=desc">⇩</a></th>
        <?php } ?>
    </tr>

    <?php
    foreach ($movieArray as $movie) {

        echo "<tr>\n";

        //TODO get best available quality
        $getBestQuality->execute(array($movie['id_movie']));
        $quality = $getBestQuality->fetch(PDO::FETCH_ASSOC);
        if ($quality) {
            $quality = $quality['quality'];
        }

        echo '<td bgcolor="' . $colour[$quality] . '"><a href="?page=moviedetails&id_movie=' . $movie['id_movie'] . '">'
        . $movie['title']
        . "</a></td>\n";
        echo '<td align="center" bgcolor="' . $colour[$quality] . '">'
        . $movie['year']
        . "</td>\n";
        echo '<td bgcolor="' . $colour[$quality] . '">';
        $getCategoriesByMovie->execute(array($movie['id_movie']));
        $categoryArray = $getCategoriesByMovie->fetchall(PDO::FETCH_ASSOC);
        $ncat = count($categoryArray);
        if ($ncat > 0) {
            echo $categoryArray[0]['category'];
        }
        for ($i = 1; $i < $ncat; $i++) {
            echo ', ' . $categoryArray[$i]['category'];
        }
        echo "</td>\n";
        if (Auth::hasPermission('rating')) {
            echo '<td align="center" bgcolor="' . $colour[$quality] . '">'
            . $movie['rating']
            . "</td>\n";
        }
        if (Auth::hasPermission('shortlists')) {
            echo '<td bgcolor="' . $colour[$quality] . '">';
            $getShortlistsByMovie->execute(array($movie['id_movie']));
            $ShortlistArray = $getShortlistsByMovie->fetchall(PDO::FETCH_ASSOC);
            $nsl = count($ShortlistArray);
            if ($nsl > 0) {
                echo $ShortlistArray[0]['listname'];
            }
            for ($i = 1; $i < $nsl; $i++) {
                echo ', ' . $ShortlistArray[$i]['listname'];
            }
            echo "</td>\n";
        }
        if (Auth::hasPermission('lastseen')) {
            echo '<td align="center" bgcolor="' . $colour[$quality] . '">';
            if ($movie['lastseen'] != '') {
                $date = DateTime::createFromFormat('Y-m-d', $movie['lastseen']);
                echo $date->format('d/m/Y');
            }
            echo "</td>\n";
        }
        if (Auth::hasPermission('write')) {
            if ($movie['imdb_id'] == '') {
                echo '<td align="center" bgcolor="' . $colour[$quality] . '">';
                echo '<a href="?page=getimdb&id_movie=' . $movie['id_movie'] . '">Lier à une fiche IMDb</a>';
                echo "</td>\n";
            }
        }

        echo "</tr>\n";
    }
    ?>
</table>
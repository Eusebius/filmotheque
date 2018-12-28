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

use Eusebius\Filmotheque\Auth;
use Eusebius\Filmotheque\Util;

Auth::ensurePermission('read');

// Remember GET parameters
$sortParameters = '';
$lastseenFilterParameters = '';
$listFilterParameters = '';
$ratingFilterParameters = '';
$catFilterParameters = '';

$sortbyRequest = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING);
if ($sortbyRequest !== false && $sortbyRequest !== NULL && $sortbyRequest !== '') {
    if ($sortbyRequest === 'year') {
        $sortby = 'year';
        $sortParameters = 'sort=year&';
    } else if ($sortbyRequest === 'rating' && Auth::hasPermission('rating')) {
        $sortby = 'rating';
        $sortParameters = 'sort=rating&';
    } else if ($sortbyRequest === 'lastseen' && Auth::hasPermission('lastseen')) {
        $sortby = 'lastseen';
        $sortParameters = 'sort=lastseen&';
    } else {
        $sortby = 'title';
        $sortParameters = 'sort=title&';
    }

    $orderbyRequest = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING);

    if ($orderbyRequest === 'desc') {
        $order = 'desc';
        $sortParameters .= 'order=desc&';
    } else {
        $order = 'asc';
        $sortParameters .= 'order=asc&';
    }
} else {
    $sortby = 'title';
    $order = 'asc';
}

$conn = Util::getDbConnection();

try {
    $getAllShortlists = $conn->prepare('select id_shortlist, listname from `shortlists` order by listname asc');
    $getAllShortlists->execute();
    $listArray = $getAllShortlists->fetchall(PDO::FETCH_ASSOC);

    $getAllCats = $conn->prepare('select category from categories order by category asc');
    $getAllCats->execute();
    $catArray = $getAllCats->fetchall(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    Util::fatal('Error while listing categories and shortlists: ' . $e->getMessage());
}

// Fetch date filter
//TODO design specific date input filter
$dateFilter = filter_input(INPUT_GET, 'lastseen', FILTER_SANITIZE_STRING);
$formattedDateFilter = NULL;
if ($dateFilter !== '0') {
    $formattedDateFilter = Util::unformatDate($dateFilter);
}
if ($dateFilter === '0' || $formattedDateFilter !== NULL) {
    $lastseenFilterParameters = 'lastseen=' . $dateFilter . '&';
}

// Fetch rating filter
$maxRatingFilter = filter_input(INPUT_GET, 'maxrating', FILTER_SANITIZE_NUMBER_INT);
if ($maxRatingFilter === '') {
    $maxRatingFilter = NULL;
}
if ($maxRatingFilter !== NULL) {
    $ratingFilterParameters = 'maxrating=' . $maxRatingFilter . '&';
}
$minRatingFilter = filter_input(INPUT_GET, 'minrating', FILTER_SANITIZE_NUMBER_INT);
if ($minRatingFilter === '') {
    $minRatingFilter = NULL;
}
if ($minRatingFilter !== NULL) {
    $ratingFilterParameters .= 'minrating=' . $minRatingFilter . '&';
}

// Fetch category filter
$catFilter = array();
foreach ($catArray as $catentry) {
    $cat = $catentry['category'];
    $catn = 'cat' . $cat;
    $catnGet = filter_input(INPUT_GET, $catn, FILTER_SANITIZE_NUMBER_INT);
    if ($catnGet === '1') { //This category has been properly selected
        $catFilterParameters .= $catn . '=1&';
        array_push($catFilter, $cat);
        //Util::debug($cat);
    }
}

// Fetch shortlist filter
$shortlistFilter = array();
foreach ($listArray as $list) {
    $id = Util::isIntString($list['id_shortlist']);
    if ($id != false) {
        $listn = 'list' . $id;
        $listnGet = filter_input(INPUT_GET, $listn, FILTER_SANITIZE_NUMBER_INT);
        if ($listnGet === '1') { //This shortlist has been properly selected
            $listFilterParameters .= $listn . '=1&';
        }
    }
}
?>

<h2>Liste des films</h2>
<table>
    <tr>
        <td>
            <?php if (Auth::hasPermission('lastseen')) { ?>
                Afficher uniquement les films non vus après<br />
                (0 pour les films jamais vus)&nbsp;:<br />
                <form action="" method="GET">
                    <?php
                    Util::makeHiddenParameters($sortParameters);
                    Util::makeHiddenParameters($catFilterParameters);
                    Util::makeHiddenParameters($listFilterParameters);
                    Util::makeHiddenParameters($ratingFilterParameters);
                    ?>
                    <input type="text" name="lastseen" value="<?php
                    if ($dateFilter === '0' || $formattedDateFilter !== NULL) {
                        echo $dateFilter;
                    }
                    ?>"/>
                    <input type="submit" value="Filtrer"/>
                </form>
            <?php } ?>
        </td>
        <td rowspan="3">
            Afficher uniquement les catégories suivantes&nbsp;:<br />
            <form action="" method="GET">
                <?php
                Util::makeHiddenParameters($sortParameters);
                Util::makeHiddenParameters($lastseenFilterParameters);
                Util::makeHiddenParameters($listFilterParameters);
                Util::makeHiddenParameters($ratingFilterParameters);
                foreach ($catArray as $catentry) {
                    $cat = $catentry['category'];
                    $catn = 'cat' . $cat;
                    echo '<input type="checkbox" name="' . $catn . '" value="1"';
                    $catnGet = filter_input(INPUT_GET, $catn, FILTER_SANITIZE_NUMBER_INT);
                    if ($catnGet === '1') { // This category has been properly selected
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
    <tr>
        <td>
            <?php if (Auth::hasPermission('rating')) { ?>
                <form action="" method="GET">
                    <?php
                    Util::makeHiddenParameters($sortParameters);
                    Util::makeHiddenParameters($catFilterParameters);
                    Util::makeHiddenParameters($listFilterParameters);
                    Util::makeHiddenParameters($lastseenFilterParameters);
                    ?>
                    Afficher uniquement les films notés<br />
                    entre&nbsp;<select name="minrating">
                        <option <?php if ($minRatingFilter === NULL) echo 'selected'; ?>></option>
                        <option <?php if ($minRatingFilter === '0') echo 'selected'; ?>>0</option>
                        <option <?php if ($minRatingFilter === '1') echo 'selected'; ?>>1</option>
                        <option <?php if ($minRatingFilter === '2') echo 'selected'; ?>>2</option>
                        <option <?php if ($minRatingFilter === '3') echo 'selected'; ?>>3</option>
                        <option <?php if ($minRatingFilter === '4') echo 'selected'; ?>>4</option>
                        <option <?php if ($minRatingFilter === '5') echo 'selected'; ?>>5</option>
                    </select>
                    et&nbsp;<select name="maxrating">
                        <option <?php if ($maxRatingFilter === NULL) echo 'selected'; ?>></option>
                        <option <?php if ($maxRatingFilter === '0') echo 'selected'; ?>>0</option>
                        <option <?php if ($maxRatingFilter === '1') echo 'selected'; ?>>1</option>
                        <option <?php if ($maxRatingFilter === '2') echo 'selected'; ?>>2</option>
                        <option <?php if ($maxRatingFilter === '3') echo 'selected'; ?>>3</option>
                        <option <?php if ($maxRatingFilter === '4') echo 'selected'; ?>>4</option>
                        <option <?php if ($maxRatingFilter === '5') echo 'selected'; ?>>5</option>
                    </select>
                    <input type="submit" value="Filtrer"/>
                </form>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php if (Auth::hasPermission('shortlists')) { ?>
                Afficher uniquement les shortlists suivantes&nbsp;:<br />
                <form action="" method="GET">
                    <?php
                    $shortlistFilter = array();

                    Util::makeHiddenParameters($sortParameters);
                    Util::makeHiddenParameters($lastseenFilterParameters);
                    Util::makeHiddenParameters($catFilterParameters);
                    Util::makeHiddenParameters($ratingFilterParameters);
                    foreach ($listArray as $list) {
                        $id = Util::isIntString($list['id_shortlist']);
                        if ($id != false) {
                            $listn = 'list' . $id;
                            echo '<input type="checkbox" name="' . $listn . '" value="1"';
                            $listnGet = filter_input(INPUT_GET, $listn, FILTER_SANITIZE_NUMBER_INT);
                            if ($listnGet === '1') { //This shortlist has been properly selected
                                echo ' checked="checked"';
                                array_push($shortlistFilter, $id);
                            }
                            echo ' />&nbsp;';
                            echo $list['listname'] . "<br />\n";
                        }
                    }
                    ?>
                    <input type="submit" value="Filtrer"/>
                </form>
            <?php } ?>
        </td>
    </tr>
</table>

<?php
$ratingWhere = '(1=1';
if ($minRatingFilter !== NULL || $maxRatingFilter !== NULL) {

    if ($minRatingFilter !== NULL) {
        $ratingWhere .= " AND `rating` >= $minRatingFilter";
    } else {
        $ratingWhere .= ' AND `rating` is null';
    }

    if ($minRatingFilter === NULL || $maxRatingFilter === NULL) {
        $ratingWhere .= ' OR ';
    } else {
        $ratingWhere .= ' AND ';
    }

    if ($maxRatingFilter !== NULL) {
        $ratingWhere .= "`rating` <= $maxRatingFilter";
    } else {
        $ratingWhere .= '`rating` is null';
    }
}
$ratingWhere .= ')';

$lastseenWhere = '(1=1)';

if ($dateFilter === '0') {
    $lastseenWhere = '(`lastseen` is null)';
} else if ($formattedDateFilter !== NULL) {
    $lastseenWhere = "(`lastseen` is null OR `lastseen` < '$formattedDateFilter')";
}

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

try {
    $listMovies = $conn->prepare('select movies.id_movie, title, year, originaltitle, imdb_id, rating, lastseen from movies left outer join experience on movies.id_movie = experience.id_movie left outer join `movies-shortlists` on movies.id_movie=`movies-shortlists`.id_movie left outer join shortlists on `movies-shortlists`.id_shortlist=shortlists.id_shortlist left outer join `movies-categories` on movies.id_movie=`movies-categories`.id_movie where ' . $shortlistWhere . ' and ' . $lastseenWhere . ' and ' . $catWhere . ' and ' . $ratingWhere . ' group by movies.id_movie order by ' . $sortby . ' ' . $order);
    $getCategoriesByMovie = $conn->prepare('select id_movie, category from `movies-categories` where id_movie = ?');
    $getShortlistsByMovie = $conn->prepare('select id_movie, listname from `movies-shortlists` natural join shortlists where id_movie = ?');
    $getBestQuality = $conn->prepare('select quality from `media` natural join `media-quality` natural join `quality` where id_movie = ? order by minwidth desc');

    $listMovies->execute();
    //Util::debug($listMovies->queryString);
    //Util::debug($listMovies->errorInfo());
    $movieArray = $listMovies->fetchall(PDO::FETCH_ASSOC);
    $nMovies = $listMovies->rowCount();
} catch (PDOException $e) {
    Util::debug($listMovies->queryString);
    Util::fatal('Error while listing movies: ' . $e->getMessage());
}
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
        <th>Titre&nbsp;<a href="index.php?<?php echo $catFilterParameters . $listFilterParameters . $lastseenFilterParameters . $ratingFilterParameters; ?>sort=title&order=asc">⇧</a><a href="index.php?<?php echo $catFilterParameters . $listFilterParameters . $lastseenFilterParameters . $ratingFilterParameters; ?>sort=title&order=desc">⇩</a></th>
        <th>Année&nbsp;<a href="index.php?<?php echo $catFilterParameters . $listFilterParameters . $lastseenFilterParameters . $ratingFilterParameters; ?>sort=year&order=asc">⇧</a><a href="index.php?<?php echo $catFilterParameters . $listFilterParameters . $lastseenFilterParameters . $ratingFilterParameters; ?>sort=year&order=desc">⇩</a></th>
        <th>Catégories</th>
        <?php if (Auth::hasPermission('rating')) { ?>
            <th>Note&nbsp;<a href="index.php?<?php echo $catFilterParameters . $listFilterParameters . $lastseenFilterParameters . $ratingFilterParameters; ?>sort=rating&order=asc">⇧</a><a href="index.php?<?php echo $catFilterParameters . $listFilterParameters . $lastseenFilterParameters . $ratingFilterParameters; ?>sort=rating&order=desc">⇩</a></th>
        <?php } ?>
        <?php if (Auth::hasPermission('shortlists')) { ?>
            <th>Shortlists</th>
        <?php } ?>
        <?php if (Auth::hasPermission('lastseen')) { ?>
            <th>Vu le&nbsp;<a href="index.php?<?php echo $catFilterParameters . $listFilterParameters . $lastseenFilterParameters . $ratingFilterParameters; ?>sort=lastseen&order=asc">⇧</a><a href="index.php?<?php echo $catFilterParameters . $listFilterParameters . $lastseenFilterParameters . $ratingFilterParameters; ?>sort=lastseen&order=desc">⇩</a></th>
        <?php } ?>
    </tr>

    <?php
    foreach ($movieArray as $movie) {

        echo "<tr>\n";

        try {
            $getBestQuality->execute(array($movie['id_movie']));
            $quality = $getBestQuality->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal('Error while getting best quality for movie ' . $movie->getID() . ': ' . $e->getMessage());
        }
        if ($quality) {
            $quality = $quality['quality'];
        } else {
            $quality = 'absent';
        }

        echo '<td bgcolor="' . $colour[$quality] . '"><a href="?page=moviedetails&id_movie=' . $movie['id_movie'] . '">'
        . $movie['title']
        . "</a></td>\n";
        echo '<td align="center" bgcolor="' . $colour[$quality] . '">'
        . $movie['year']
        . "</td>\n";
        echo '<td bgcolor="' . $colour[$quality] . '">';
        try {
            $getCategoriesByMovie->execute(array($movie['id_movie']));
            $categoryArray = $getCategoriesByMovie->fetchall(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Util::fatal('Error while getting categories for movie ' . $movie->getID . ': ' . $e->getMessage());
        }
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
            try {
                $getShortlistsByMovie->execute(array($movie['id_movie']));
                $ShortlistArray = $getShortlistsByMovie->fetchall(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                Util::fatal('Error while getting shortlists for movie ' . $movie->getID . ': ' . $e->getMessage());
            }
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
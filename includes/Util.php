<?php

/**
 * includes/Util.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.6
 * 
 * This is the definition file for the Util class.
 * This file is not to be included directly, use declarations.inc.php instead.
 */
/*
 * Copyright (C) 2015 Eusebius <eusebius@eusebius.fr>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * Class providing static utility functions.
 *
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.6
 */
class Util {

    /**
     * Redirects the visitor to the main page of the application and 
     * stops the current script.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function gotoMainPage() {
        header('Location:http://' . $_SESSION['baseuri']);
        die();
    }

    /**
     * If in debug mode, make a debug print of the variable. Otherwise, do
     * nothing.
     * @param $array The variable to print (typically an array).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function debug($array) {
        if (isset($_SESSION['debug']) && $_SESSION['debug'] === true) {
            echo "<pre>\n";
            print_r($array);
            echo "\n</pre>\n";
        }
    }

    /**
     * Halts the application, with an error message if in debug mode, or 
     * with a generic message otherwise.
     * @param $message The message to display (can be a string or an array).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function fatal($message) {
        if (isset($_SESSION['debug']) && $_SESSION['debug'] === true) {
            print_r($message);
            die();
        } else {
            die('A fatal error has occurred. This application is currently '
                    . 'broken.');
        }
    }

    /**
     * Check that the `covers` directory is writeable by the application. 
     * Otherwise, print an error message informing the user (even if not in
     * debug mode).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function checkChmod() {
        if (!isset($_SESSION['basepath'])) {
            Util::fatal('The application wants to check the rights on the '
                    . '"covers" directory, but the basepath is not recorded in '
                    . 'the session.');
        }
        if (!is_writable($_SESSION['basepath'] . '/covers')) {
            echo '<center><strong><font color="red">Erreur de '
            . 'configuration&nbsp;: le répertoire "covers" doit être accessible'
            . 'en écriture.</font></strong></center>';
        }
    }

    /**
     * Check whether the parameter is a string corresponding to an int.
     * @param \string $string The string to check.
     * @return \int The corresponding integer, or `false`.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function isIntString($string) {
        //TODO rewrite that, as "0123abc" coerces to an int.
        if ((string) (int) $string == $string) {
            $result = (int) $string;
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Print hidden input fields based on a GET-like parameter string.
     * @param \string $paramString A GET-like parameter string, in the form 
     * `key1=value1&key2=value2`.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function makeHiddenParameters($paramString) {
        $couples = explode('&', $paramString);
        foreach ($couples as $couple) {
            $couple = explode('=', $couple);
            if (count($couple) == 2) {
                echo '<input type="hidden" name="' . $couple[0]
                . '" value="' . $couple[1] . '" />' . "\n";
            }
        }
    }

    /**
     * For a given index, returns the corresponding POST parameter if it is 
     * valid, or `null` otherwise.
     * @param \string $POSTindex The index of the parameter.
     * @return \string Either the value of the parameter, or `null`.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function getPOSTValueOrNull($POSTindex) {
        if (isset($_POST[$POSTindex]) && $_POST[$POSTindex] != '') {
            $result = $_POST[$POSTindex];
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Check that the proper movie is present as an object in session (either 
     * reuse the existing one or load the proper one). Does not refresh the 
     * movie object if it refers to the right movie.
     * @param \int $movieID The unique identifier of the movie.
     * @return \Movie The movie object.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.6
     */
    static function getMovieInSession($movieID) {
        if (!isset($_SESSION['movie']) || $_SESSION['movie']->getID() !== $movieID) {
            $_SESSION['movie'] = new Movie($movieID);
        }
        return $_SESSION['movie'];
    }

    /**
     * Check that the proper medium is present as an object in session (either reuse
     * the existing one or load the proper one). Does not refresh the medium object
     * if it refers to the right medium.
     * @param \int $mediumID The unique identifier of the medium.
     * @return \Medium The medium object.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.6
     */
    static function getMediumInSession($mediumID) {
        if (!isset($_SESSION['medium']) || $_SESSION['medium']->getID() !== $mediumID) {
            $_SESSION['medium'] = new Medium($mediumID);
        }
        return $_SESSION['medium'];
    }

    /**
     * Provides a valid connection to the database, either by retrieving an existing
     * one in session or by opening a new one (and registering it in session for 
     * future use).
     * 
     * In case of errors, dies with an error message (unspecific if not in debug
     * mode).
     * 
     * @return \PDO A valid PDO connection object.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function getDbConnection() {
        if (!isset($_SESSION['config']['db_type'])
                || !isset($_SESSION['config']['db_server'])
                || !isset($_SESSION['config']['db_db'])
                || !isset($_SESSION['config']['db_user'])
                || !isset($_SESSION['config']['db_password']))
                {
                    Util::fatal('Database configuration is not properly set up in '
                            . 'session.');
        }
        if ((!isset($_SESSION['dbconn'])) or ( $_SESSION['dbconn'] == null)) {
            try {
                $pdoconn = new PDO(
                        $_SESSION['config']['db_type']
                        . ':host=' . $_SESSION['config']['db_server']
                        . ';dbname=' . $_SESSION['config']['db_db'], 
                        $_SESSION['config']['db_user'], 
                        $_SESSION['config']['db_password'], 
                        array(PDO::ATTR_PERSISTENT => true));
            } catch (PDOException $e) {
                Util::fatal($e->getMessage());
            }
        }
        $pdoconn->query("SET NAMES utf8");
        return $pdoconn;
    }

    /**
     * Closes the connection to the database, provided all resultsets are nullified
     * beforehand.
     * 
     * @param \PDO $pdoconn The connection to close
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function disconnectDB($pdoconn) {
        //TODO do we really disconnect here, or did we modify a copy of the
        //reference?
        $pdoconn = null;
    }

    /**
     * Strip a path (URI or local) from any suffix starting with one of the 
     * folders of the application.
     * 
     * @param \string $path The original path.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.6
     */
    public static function stripPathFromDirs($path) {
        $withoutIncludes = Util::stripPathFromDir($path, '/includes');
        $withoutCovers = Util::stripPathFromDir($withoutIncludes, '/covers');
        $withoutScripts = Util::stripPathFromDir($withoutCovers, '/scripts');
        $withoutPages = Util::stripPathFromDir($withoutScripts, '/pages');
        $withoutParams1 = Util::stripPathFromDir($withoutPages, '?');
        $withoutIndex = Util::stripPathFromDir($withoutParams1, 'index.php');
        if (substr($withoutIndex, -1) !== '/') {
            $withoutIndex .='/';
        }
        return $withoutIndex;
    }
    
    /**
     * Strip an string from a suffix starting with a given prefix.
     * 
     * @param \string $path The original string.
     * @dir \string $dir The prefix to look for.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.6
     */
    private static function stripPathFromDir($path, $dir) {
        return substr($path, 0, 
                (strpos($path, $dir) ? strpos($path, $dir) : strlen($path)));
    }

}

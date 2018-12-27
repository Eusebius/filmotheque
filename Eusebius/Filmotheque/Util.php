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

namespace Eusebius\Filmotheque;

use PDO,
    PDOException;
use DateTime;

/**
 * Class providing static utility functions.
 *
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.6
 */
class Util {

    //TODO propose a check for date strings (cf lastseen)
    //TODO propose custom check for rating (scale 1-5)
    const POST_CHECK_STRING = 0;
    const POST_CHECK_INT = 1;
    const POST_CHECK_STRING_ARRAY = 2;
    const POST_CHECK_INT_ARRAY = 3;
    const POST_CHECK_RAW = 4;

    /**
     * Redirects the visitor to the main page of the application and 
     * stops the current script. Works even in the absence of a working session,
     * but just because the developer has no brains (loading the main page 
     * without a valid session should not be possible).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function gotoMainPage() {
        if (isset($_SESSION['baseuri'])) {
            $baseuri = $_SESSION['baseuri'];
        } else {
            $completeURI = Util::getHttpHost() . Util::getRequestURI();
            $baseuri = Util::stripPathFromDirs($completeURI);
        }
        if (isset($_SESSION['http'])) {
            $http = $_SESSION['http'];
        } else if (Util::isHTTPS()) {
            $http = 'https';
        } else {
            $http = 'http';
        }
        header('Location:' . $http . '://' . $baseuri);
        die();
    }

    /**
     * Determines whether the website is served through HTTP or HTTPS.
     * @return boolean True if HTTPS is used, false otherwise.
     * @since 0.2.8
     */
    static function isHTTPS() {
        $result = false;
        //TODO What are the possible values, how can we filter them properly?
        $https = filter_input(INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_STRING);
        if (($https !== NULL) && ($https !== 'off')) {
            $result = true;
        }
        return $result;
    }

    /**
     * Get and sanitize the request URI from the server environment.
     * Crashes with a fatal error if sanitization fails.
     * @return string The request URI, starting with a slash
     * @since 0.2.8
     */
    static function getRequestURI() {
        //TODO maybe validate against a regexp for a path starting with /
        $sanitizedURI = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING);
        if ($sanitizedURI === false && strpos('.', $sanitizedURI) !== false) {
            Util::fatal('Unable to validate request URI: ' . filter_input(INPUT_SERVER, 'REQUEST_URI'));
        }
        return $sanitizedURI;
    }

    /**
     * Get and sanitize the HTTP host provided by the user agent through the 
     * server environment.
     * Crashes with a fatal error if sanitization fails.
     * @return string the HTTP host sent by the user agent, without trailing 
     * slash
     * @since 0.2.8
     */
    static function getHttpHost() {
        //TODO write custom callback filter?
        $filteredHttpHost = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
        //TODO take precautions on existence of the array and its first element
        $strippedHttpHost = explode('/', $filteredHttpHost)[0];
        if (filter_var('http://' . $strippedHttpHost, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
            return $strippedHttpHost;
        } else {
            Util::fatal("Impossible to validate HTTP_HOST: $filteredHttpHost ($strippedHttpHost).");
            exit();
        }
    }

    /**
     * Redirects the visitor to the login page of the application and 
     * stops the current script. Works even in the absence of a working session
     * (i.e. right after disconnection).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.7
     */
    static function gotoLoginPage() {
        if (isset($_SESSION['baseuri'])) {
            $baseuri = $_SESSION['baseuri'];
        } else {
            $completeURI = Util::getHttpHost() . Util::getRequestURI();
            $baseuri = Util::stripPathFromDirs($completeURI);
        }
        if (isset($_SESSION['http'])) {
            $http = $_SESSION['http'];
        } else if (Util::isHTTPS()) {
            $http = 'https';
        } else {
            $http = 'http';
        }
        header('Location:' . $http . '://' . $baseuri . 'login.php');
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
     * 
     * @SuppressWarnings(PHPMD.ExitExpression)
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
     * Check that the password of the initial `admin` account has been changed. 
     * Otherwise, print an error message informing the user (even if not in
     * debug mode).
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.3.3
     */
    static function checkAdminPwd() {
        $dbconn = Util::getDbConnection();
        
        $pwRes = $dbconn->query('select login from `users` where login=\'admin\' and '
                . 'password=\'$2y$10$XTOHjbXWky4JHVUaanvWLuJfNvV58IRd1bUuGQp3XicPgJQmJSNDe\'');

        if ($pwRes->rowCount() > 0) {
            echo '<center><strong><font color="red">Erreur de '
            . 'configuration&nbsp;: le mot de passe du compte admin n\'a pas '
            . 'été modifié.</font></strong></center>';
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
        //Util::debug($paramString);
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
     * Crashes with a fatal error if the requested validation/sanitization fails.
     * @param \string $POSTindex The index of the parameter.
     * @param \int $validation A constant (among the Util class constants) 
     * specifying the validation/sanitization filter to be used.
     * @return \string Either the value of the parameter, or `null`.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.4
     */
    static function getPOSTValueOrNull($POSTindex, $validation) {
        $options = array();
        switch ($validation) {
            case Util::POST_CHECK_RAW:
                $filter = FILTER_DEFAULT;
                $options = FILTER_REQUIRE_SCALAR;
                break;
            case Util::POST_CHECK_STRING_ARRAY:
                $filter = FILTER_SANITIZE_STRING;
                $options = FILTER_REQUIRE_ARRAY;
                break;
            case Util::POST_CHECK_INT:
                $filter = FILTER_SANITIZE_NUMBER_INT;
                $options = FILTER_REQUIRE_SCALAR;
                break;
            case Util::POST_CHECK_INT_ARRAY:
                $filter = FILTER_SANITIZE_NUMBER_INT;
                $options = FILTER_REQUIRE_ARRAY;
                break;
            case Util::POST_CHECK_STRING:
            default:
                $filter = FILTER_SANITIZE_STRING;
                $options = FILTER_REQUIRE_SCALAR;
        }
        $result = filter_input(INPUT_POST, $POSTindex, $filter, $options);
        if ($result === false) {
            Util::fatal("Unable to validate POST parameter against filter $filter: " . filter_input(INPUT_POST, $POSTindex));
            exit();
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
     * Forget about the movie in session. To be called after an update on the
     * movie object, to ensure that it is fetched from the database again.
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.7
     */
    static function resetMovieInSession() {
        unset($_SESSION['movie']);
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
     * Provides a valid connection to the database, using persistent connections
     * to improve performance.
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
        if (!isset($_SESSION['config']['db_type']) || !isset($_SESSION['config']['db_server']) || !isset($_SESSION['config']['db_db']) || !isset($_SESSION['config']['db_user']) || !isset($_SESSION['config']['db_password'])) {
            Util::fatal('Database configuration is not properly set up in '
                    . 'session.');
        }
        try {
            $pdoconn = new PDO(
                    $_SESSION['config']['db_type']
                    . ':host=' . $_SESSION['config']['db_server']
                    . ';dbname=' . $_SESSION['config']['db_db'], $_SESSION['config']['db_user'], $_SESSION['config']['db_password'], array(PDO::ATTR_PERSISTENT => true)
            );

            $pdoconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdoconn->query("SET NAMES utf8");
        } catch (PDOException $e) {
            Util::fatal($e->getMessage());
        }
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
        $withoutTesting = Util::stripPathFromDir($withoutPages, '/testing');
        $withoutEusebius = Util::stripPathFromDir($withoutTesting, '/Eusebius');
        $withoutParams1 = Util::stripPathFromDir($withoutEusebius, '?');
        $withoutIndex = Util::stripPathFromDir($withoutParams1, 'index.php');
        if (substr($withoutIndex, -1) !== '/') {
            $withoutIndex .= '/';
        }
        return $withoutIndex;
    }

    /**
     * Strip a string from a suffix starting with a given prefix.
     * 
     * @param \string $path The original string.
     * @dir \string $dir The prefix to look for.
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.2.6
     */
    private static function stripPathFromDir($path, $dir) {
        return substr($path, 0, (strpos($path, $dir) ? strpos($path, $dir) : strlen($path)));
    }

    /**
     * Convert a date from a 'dd/mm/yyyy' format to a 'yyyy-mm-dd' format.
     * @param \string The date in a 'dd/mm/yyyy' format.
     * @return \string The date in a 'yyyy-mm-dd' format, or null if the input format was incorrect.
     * 
     * @author Eusebius <eusebius@eusebius.fr>
     * @since 0.3.0
     */
    public static function unformatDate($date) {
        $result = NULL;
        $date2 = DateTime::createFromFormat('d/m/Y', $date);
        if ($date2 !== false) {
            // The provided format is OK
            $result = $date2->format('Y-m-d');
        }
        return $result;
    }

}

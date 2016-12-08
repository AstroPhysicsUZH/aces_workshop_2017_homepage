<?php

/* everything that is printet from db to html should go through this function for security */
function P($var) { return htmlentities($var); }


/* Shorthand to print a bool from the database ("1" -> TRUE, otherwise false) */
function B($var) { return $var==="1"? "yes":"no"; }


function print_menu($active_page)
{
    global $PAGES, $NOT_IMPLEMENTED_PAGES, $HIDDEN_PAGES;

    echo "<nav><ul>";

    foreach ($PAGES as $page_) {
        $page_id    = $page_[0];
        $page_title = $page_[1];

        if ( in_array($page_id, $HIDDEN_PAGES)) { continue; }

        $classes = array(); # collect classes

        if ($page_id == $active_page) {
            array_push($classes, 'active');
            //$page = '&#x25B8; '.$page; #TODO reimplement using css:before
        }

        if (in_array($page_id, $NOT_IMPLEMENTED_PAGES)) {
            array_push($classes, 'not_implemented');
        }

        echo "    <li class='" . implode(',', $classes) . "'>\n";
        echo "        <a href='?page=$page_id'>$page_title</a></li>\n";
    }
    echo <<<HTML
    </ul>
    <p class='menuaddition'>
      contact:<br>
      <a href='mailto:relativityUZH@gmail.com'>relativityUZH@gmail.com</a>
    </p>
</nav>
HTML;
}

/**
    Convert a comma separated file into an associated array.
    The first row should contain the array keys.

    Example:
    @param string $filename  Path to the CSV file
    @param string $delimiter The separator used in the file
    @return array

    @link http://gist.github.com/385876
    @author Jay Williams <http://myd3.com/>
    @copyright Copyright (c) 2010, Jay Williams
    @license http://www.opensource.org/licenses/mit-license.php MIT License
******************************************************************************/
function csv_to_array($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename)) {
        return false;
    }

    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (!$header) {
                $header = $row;
            } else {
                $data[] = array_combine($header, $row);
            }
        }
        fclose($handle);
    }

    return $data;
};

/**
    function to open a database handle (using PDO)
**/
function open_db($dba = NULL) {

    global $db_address;
    if (! $dba){ $dba = $db_address; }
    $db = NULL;

    try {
        // Create (connect to) SQLite database in file
        $db = new PDO($dba);
        // Set errormode to exceptions
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
        echo '<br />';
        var_dump($e->getTraceAsString());

        die(1);
    }
    return $db;
}


/**
 * Check if a table exists in the current database.
 *
 * @param PDO $pdo PDO instance connected to a database.
 * @param string $table Table to search for.
 * @return bool TRUE if table exists, FALSE if no table found.
 **/
function tableExists($pdo, $table) {

    // Try a select statement against the table
    // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
    } catch (Exception $e) {
        // We got an exception == table not found
        return FALSE;
    }

    // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
    return $result !== FALSE;
}




/**
 * endsWith - check if string ends with otherstring
 * http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
 **/
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}



/**
 * Returns the url of the current page
 **/
function get_baseurl() {
    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'];
    $url .= $_SERVER['REQUEST_URI'];
    return $url;
}

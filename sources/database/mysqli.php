<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/*EXTRA FUNCTIONS: mysqli\_.+*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_database_drivers
 */

require_code('database/shared/mysql');

/**
 * Database Driver.
 *
 * @package    core_database_drivers
 */
class Database_Static_mysqli extends Database_super_mysql
{
    public $cache_db = array();
    public $last_select_db = null;
    public $reconnected_once = false;

    /**
     * Get a database connection. This function shouldn't be used by you, as a connection to the database is established automatically.
     *
     * @param  boolean $persistent Whether to create a persistent connection
     * @param  string $db_name The database name
     * @param  string $db_host The database host (the server)
     * @param  string $db_user The database connection username
     * @param  string $db_password The database connection password
     * @param  boolean $fail_ok Whether to on error echo an error and return with a NULL, rather than giving a critical error
     * @return ?array A database connection (note for MySQL, it's actually a pair, containing the database name too: because we need to select the name before each query on the connection) (null: error)
     */
    public function db_get_connection($persistent, $db_name, $db_host, $db_user, $db_password, $fail_ok = false)
    {
        if (!function_exists('mysqli_connect')) {
            $error = 'MySQLi not on server (anymore?). Try using the \'mysql\' database driver. To use it, edit the _config.php config file.';
            if ($fail_ok) {
                echo $error;
                return null;
            }
            critical_error('PASSON', $error);
        }

        // Potential caching
        $x = serialize(array($db_name, $db_host));
        if (array_key_exists($x, $this->cache_db)) {
            if ($this->last_select_db[1] != $db_name) {
                mysqli_select_db($this->cache_db[$x], $db_name);
                $this->last_select_db = array($this->cache_db[$x], $db_name);
            }

            return array($this->cache_db[$x], $db_name);
        }
        $db = @mysqli_connect(($persistent ? 'p:' : '') . $db_host, $db_user, $db_password);

        if ($db === false) {
            $error = 'Could not connect to database-server (when authenticating) (' . mysqli_connect_error() . ')';
            if ($fail_ok) {
                echo $error;
                return null;
            }
            critical_error('PASSON', $error); //warn_exit(do_lang_tempcode('CONNECT_DB_ERROR'));
        }
        if (!mysqli_select_db($db, $db_name)) {
            if ($db_user == 'root') {
                @mysqli_query($db, 'CREATE DATABASE IF NOT EXISTS ' . $db_name);
            }

            if (!mysqli_select_db($db, $db_name)) {
                $error = 'Could not connect to database (' . mysqli_error($db) . ')';
                if ($fail_ok) {
                    echo $error . "\n";
                    return null;
                }
                critical_error('PASSON', $error); //warn_exit(do_lang_tempcode('CONNECT_ERROR'));
            }
        }
        $this->last_select_db = array($db, $db_name);

        $this->cache_db[$x] = $db;

        global $SITE_INFO;
        if (!array_key_exists('database_charset', $SITE_INFO)) {
            $SITE_INFO['database_charset'] = (strtolower(get_charset()) == 'utf-8') ? 'utf8' : 'latin1';
        }
        if (function_exists('mysqli_set_charset')) {
            mysqli_set_charset($db, $SITE_INFO['database_charset']);
        } else {
            @mysqli_query($db, 'SET NAMES "' . addslashes($SITE_INFO['database_charset']) . '"');
        }
        @mysqli_query($db, 'SET WAIT_TIMEOUT=28800');
        @mysqli_query($db, 'SET SQL_BIG_SELECTS=1');
        if ((get_forum_type() == 'cns') && (!$GLOBALS['IN_MINIKERNEL_VERSION'])) {
            @mysqli_query($db, 'SET sql_mode=STRICT_ALL_TABLES');
        }
        // NB: Can add ,ONLY_FULL_GROUP_BY for testing on what other DBs will do, but can_arbitrary_groupby() would need to be made to return false

        return array($db, $db_name);
    }

    /**
     * Find whether full-text-search is present
     *
     * @param  array $db A DB connection
     * @return boolean Whether it is
     */
    public function db_has_full_text($db)
    {
        if ($this->using_innodb()) {
            return false;
        }

        return true;
    }

    /**
     * Find whether subquery support is present
     *
     * @param  array $db A DB connection
     * @return boolean Whether it is
     */
    public function db_has_subqueries($db)
    {
        return true;
    }

    /**
     * Find whether collate support is present
     *
     * @param  array $db A DB connection
     * @return boolean Whether it is
     */
    public function db_has_collate_settings($db)
    {
        return true;
    }

    /**
     * Find whether full-text-boolean-search is present
     *
     * @return boolean Whether it is
     */
    public function db_has_full_text_boolean()
    {
        return true;
    }

    /**
     * Escape a string so it may be inserted into a query. If SQL statements are being built up and passed using db_query then it is essential that this is used for security reasons. Otherwise, the abstraction layer deals with the situation.
     *
     * @param  string $string The string
     * @return string The escaped string
     */
    public function db_escape_string($string)
    {
        if (is_null($this->last_select_db)) {
            return addslashes($string);
        }
        return mysqli_real_escape_string($this->last_select_db[0], $string);
    }

    /**
     * This function is a very basic query executor. It shouldn't usually be used by you, as there are abstracted versions available.
     *
     * @param  string $query The complete SQL query
     * @param  array $db_parts A DB connection
     * @param  ?integer $max The maximum number of rows to affect (null: no limit)
     * @param  ?integer $start The start row to affect (null: no specification)
     * @param  boolean $fail_ok Whether to output an error on failure
     * @param  boolean $get_insert_id Whether to get the autoincrement ID created for an insert query
     * @return ?mixed The results (null: no results), or the insert ID
     */
    public function db_query($query, $db_parts, $max = null, $start = null, $fail_ok = false, $get_insert_id = false)
    {
        list($db, $db_name) = $db_parts;

        if (isset($query[500000])) { // Let's hope we can fail on this, because it's a huge query. We can only allow it if MySQL can.
            $test_result = $this->db_query('SHOW VARIABLES LIKE \'max_allowed_packet\'', $db_parts, null, null, true);

            if (!is_array($test_result)) {
                return null;
            }
            if (intval($test_result[0]['Value']) < intval(strlen($query) * 1.2)) {
                /*@mysql_query('SET session max_allowed_packet='.strval(intval(strlen($query)*1.3)),$db); Does not work well, as MySQL server has gone away error will likely just happen instead */

                if ($get_insert_id) {
                    fatal_exit(do_lang_tempcode('QUERY_FAILED_TOO_BIG', escape_html($query)));
                }
                return null;
            }
        }

        if ($this->last_select_db[1] != $db_name) {
            mysqli_select_db($db, $db_name);
            $this->last_select_db = array($db, $db_name);
        }

        if (($max !== null) && ($start !== null)) {
            $query .= ' LIMIT ' . strval($start) . ',' . strval($max);
        } elseif ($max !== null) {
            $query .= ' LIMIT ' . strval($max);
        } elseif ($start !== null) {
            $query .= ' LIMIT ' . strval($start) . ',30000000';
        }

        $results = @mysqli_query($db, $query);
        if (($results === false) && ((!$fail_ok) || (strpos(mysqli_error($db), 'is marked as crashed and should be repaired') !== false))) {
            $err = mysqli_error($db);

            if ((function_exists('mysqli_ping')) && ($err == 'MySQL server has gone away') && (!$this->reconnected_once)) {
                safe_ini_set('mysqli.reconnect', '1');
                $this->reconnected_once = true;
                mysqli_ping($db);
                $ret = $this->db_query($query, $db_parts, null/*already encoded*/, null/*already encoded*/, $fail_ok, $get_insert_id);
                $this->reconnected_once = false;
                return $ret;
            }

            if (function_exists('ocp_mark_as_escaped')) {
                ocp_mark_as_escaped($err);
            }
            if ((!running_script('upgrader')) && (!get_mass_import_mode()) && (strpos($err, 'Duplicate entry') === false)) {
                $matches = array();
                if (preg_match('#/(\w+)\' is marked as crashed and should be repaired#U', $err, $matches) != 0) {
                    $this->db_query('REPAIR TABLE ' . $matches[1], $db_parts);
                }

                if (!function_exists('do_lang') || is_null(do_lang('QUERY_FAILED', null, null, null, null, false))) {
                    fatal_exit(htmlentities('Query failed: ' . $query . ' : ' . $err));
                }
                fatal_exit(do_lang_tempcode('QUERY_FAILED', escape_html($query), ($err)));
            } else {
                echo htmlentities('Database query failed: ' . $query . ' [') . ($err) . htmlentities(']' . '<br />' . "\n");
                return null;
            }
        }

        $sub = substr(ltrim($query), 0, 7);
        $sub = substr($query, 0, 4);
        if (($results !== true) && (($sub == '(SEL') || ($sub == 'SELE') || ($sub == 'sele') || ($sub == 'EXPL') || ($sub == 'DESC') || ($sub == 'SHOW')) && ($results !== false)) {
            return $this->db_get_query_rows($results);
        }

        if ($get_insert_id) {
            if (strtoupper(substr($query, 0, 7)) == 'UPDATE ') {
                return mysqli_affected_rows($db);
            }
            $ins = mysqli_insert_id($db);
            if ($ins === 0) {
                $table = substr($query, 12, strpos($query, ' ', 12) - 12);
                $rows = $this->db_query('SELECT MAX(id) AS x FROM ' . $table, $db_parts, 1, 0, false, false);
                return $rows[0]['x'];
            }
            return $ins;
        }

        return null;
    }

    /**
     * Get the rows returned from a SELECT query.
     *
     * @param  resource $results The query result pointer
     * @return array A list of row maps
     */
    public function db_get_query_rows($results)
    {
        $num_fields = mysqli_num_fields($results);
        $names = array();
        $types = array();
        for ($x = 0; $x < $num_fields; $x++) {
            $field = mysqli_fetch_field($results);
            $names[$x] = $field->name;
            $types[$x] = $field->type;
        }

        $out = array();
        $newrow = array();
        while (!is_null(($row = mysqli_fetch_row($results)))) {
            $j = 0;
            foreach ($row as $v) {
                $name = $names[$j];
                $type = $types[$j];

                if (($type === 'int') || ($type === 'integer') || ($type === 'real') || ($type === 1) || ($type === 3) || ($type === 8)) {
                    if ((is_null($v)) || ($v === '')) { // Roadsend returns empty string instead of NULL
                        $newrow[$name] = null;
                    } else {
                        $_v = intval($v);
                        if (strval($_v) != $v) {
                            $newrow[$name] = floatval($v);
                        } else {
                            $newrow[$name] = $_v;
                        }
                    }
                } elseif (($type === 16) || ($type === 'bit')) {
                    if ((strlen($v) == 1) && (ord($v[0]) <= 1)) {
                        $newrow[$name] = ord($v); // 0/1 char for BIT field
                    } else {
                        $newrow[$name] = intval($v);
                    }
                } else {
                    $newrow[$name] = $v;
                }

                $j++;
            }

            $out[] = $newrow;
        }
        mysqli_free_result($results);

        return $out;
    }
}

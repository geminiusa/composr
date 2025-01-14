<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_database_drivers
 */

/**
 * Hook class.
 */
class Hook_cron_oracle
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        if (get_db_type() == 'oracle') {
            $oracle_index_cleanup_last_time = intval(get_value('oracle_index_cleanup_last_time', null, true));

            if ($oracle_index_cleanup_last_time < (time() - 60 * 60 * 5)) { // every 5 hours
                set_value('oracle_index_cleanup_last_time', strval(time()), true);

                $indices = $GLOBALS['SITE_DB']->query_select('db_meta_indices', array('i_name'));
                foreach ($indices as $index) {
                    if ($index['i_name'][0] == '#') {
                        $GLOBALS['SITE_DB']->query('EXEC CTX_DDL.SYNC_INDEX(\'' . substr($index['i_name'], 1) . '\')');
                    }
                }
            }
        }
    }
}

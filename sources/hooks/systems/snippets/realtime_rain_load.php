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
 * @package    realtime_rain
 */

/**
 * Hook class.
 */
class Hook_snippet_realtime_rain_load
{
    /**
     * Run function for snippet hooks. Generates XHTML to insert into a page using AJAX.
     *
     * @return tempcode The snippet
     */
    public function run()
    {
        require_lang('realtime_rain');

        $min_time = $GLOBALS['SITE_DB']->query_select_value('stats', 'MIN(date_and_time)');
        return do_template('REALTIME_RAIN_OVERLAY', array('_GUID' => '1b3535932bbefcb9474fbfc2297b4d71', 'MIN_TIME' => strval($min_time)));
    }
}

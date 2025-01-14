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
 * @package    core_cleanup_tools
 */

/**
 * Hook class.
 */
class Hook_cleanup_self_learning
{
    /**
     * Find details about this cleanup hook.
     *
     * @return ?array Map of cleanup hook info (null: hook is disabled).
     */
    public function info()
    {
        $info = array();
        $info['title'] = do_lang_tempcode('SELF_LEARNING_CACHE');
        $info['description'] = do_lang_tempcode('DESCRIPTION_SELF_LEARNING_CACHE');
        $info['type'] = 'cache';

        return $info;
    }

    /**
     * Run the cleanup hook action.
     *
     * @return tempcode Results
     */
    public function run()
    {
        Self_learning_cache::erase_smart_cache();

        return new Tempcode();
    }
}

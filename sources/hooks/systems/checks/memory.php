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
 * @package    core
 */

/**
 * Hook class.
 */
class Hook_check_memory
{
    /**
     * Check various input var restrictions.
     *
     * @return array List of warnings
     */
    public function run()
    {
        $warning = array();
        if ((function_exists('memory_get_usage')) && (@ini_get('memory_limit') != '') && (@ini_get('memory_limit') != '-1') && (@ini_get('memory_limit') != '0') && (intval(trim(str_replace('M', '', @ini_get('memory_limit')))) < 32)) {
            $warning[] = do_lang_tempcode('LOW_MEMORY_LIMIT');
        }
        return $warning;
    }
}

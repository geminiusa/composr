<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    classifieds
 */

/**
 * Hook class.
 */
class Hook_page_groupings_classifieds
{
    /**
     * Run function for do_next_menu hooks. They find links to put on standard navigation menus of the system.
     *
     * @param  ?MEMBER $member_id Member ID to run as (null: current member)
     * @param  boolean $extensive_docs Whether to use extensive documentation tooltips, rather than short summaries
     * @return array List of tuple of links (page grouping, icon, do-next-style linking data), label, help (optional) and/or nulls
     */
    public function run($member_id = null, $extensive_docs = false)
    {
        return array(
            array('setup', 'menu/classifieds', array('admin_classifieds', array(), get_module_zone('admin_classifieds')), do_lang_tempcode('classifieds:CLASSIFIEDS_PRICING'), 'classifieds:DOC_CLASSIFIEDS_PRICING'),
            array('social', 'menu/classifieds', array('classifieds', array(), get_module_zone('classifieds')), do_lang_tempcode('classifieds:CLASSIFIED_ADVERTS')),
        );
    }
}

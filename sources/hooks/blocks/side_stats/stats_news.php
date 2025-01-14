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
 * @package    news
 */

/**
 * Hook class.
 */
class Hook_stats_news
{
    /**
     * Show a stats section.
     *
     * @return tempcode The result of execution.
     */
    public function run()
    {
        if (!addon_installed('news')) {
            return new Tempcode();
        }

        require_lang('news');

        $bits = new Tempcode();

        if (get_option('news_show_stats_count_total_posts') == '1') {
            $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array('_GUID' => '1b886065ad1190c2b7862024c8aad430', 'KEY' => do_lang_tempcode('COUNT_POSTS'), 'VALUE' => integer_format($GLOBALS['SITE_DB']->query_select_value('news', 'COUNT(*)')))));
        }
        if (get_option('news_show_stats_count_blogs') == '1') {
            $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array('_GUID' => 'da519440ca5ad67869000bae8caab935', 'KEY' => do_lang_tempcode('BLOGS'), 'VALUE' => integer_format($GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . get_table_prefix() . 'news_categories WHERE nc_owner IS NOT NULL')))));
        }
        if ($bits->is_empty_shell()) {
            return new Tempcode();
        }
        $section = do_template('BLOCK_SIDE_STATS_SECTION', array('_GUID' => '8971029f901a88baa03b021ac56c7836', 'SECTION' => do_lang_tempcode('NEWS'), 'CONTENT' => $bits));

        return $section;
    }
}

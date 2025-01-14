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
 * @package    wiki
 */

/**
 * Hook class.
 */
class Hook_whatsnew_wiki
{
    /**
     * Run function for newsletter hooks.
     *
     * @param  TIME $cutoff_time The time that the entries found must be newer than
     * @param  LANGUAGE_NAME $lang The language the entries found must be in
     * @param  string $filter Category filter to apply
     * @return array Tuple of result details
     */
    public function run($cutoff_time, $lang, $filter)
    {
        if (!addon_installed('wiki')) {
            return array();
        }

        unset($filter); // Not used

        require_lang('wiki');

        $max = intval(get_option('max_newsletter_whatsnew'));

        $new = new Tempcode();

        $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'wiki_pages WHERE add_date>' . strval($cutoff_time) . ' ORDER BY add_date DESC', $max);
        if (count($rows) == $max) {
            return array();
        }
        foreach ($rows as $row) {
            $id = $row['id'];
            $_url = build_url(array('page' => 'wiki', 'type' => 'browse', 'id' => $row['id']), get_module_zone('wiki'), null, false, false, true);
            $url = $_url->evaluate();
            $name = get_translated_text($row['title'], null, $lang);
            $description = get_translated_text($row['description'], null, $lang);
            $member_id = null;
            $new->attach(do_template('NEWSLETTER_WHATSNEW_RESOURCE_FCOMCODE', array('_GUID' => '29571e3829c6723b2ca946436a6cadb2', 'MEMBER_ID' => $member_id, 'URL' => $url, 'NAME' => $name, 'DESCRIPTION' => $description, 'CONTENT_TYPE' => 'wiki_page', 'CONTENT_ID' => strval($id)), null, false, null, '.txt', 'text'));

            handle_has_checked_recently($url); // We know it works, so mark it valid so as to not waste CPU checking within the generated Comcode
        }

        return array($new, do_lang('WIKI', '', '', '', $lang));
    }
}

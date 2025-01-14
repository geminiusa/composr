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
 * @package    core_adminzone_dashboard
 */

/**
 * Block class.
 */
class Block_main_staff_links
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Jack Franklin';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 3;
        $info['locked'] = false;
        $info['parameters'] = array();
        $info['update_require_upgrade'] = 1;
        return $info;
    }

    /**
     * Uninstall the block.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('stafflinks');
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = '(count($_POST)>0)?null:array()'; // No cache on POST as this is when we save text data
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 60 * 5;
        return $info;
    }

    /**
     * Install the block.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        if ((is_null($upgrade_from)) || ($upgrade_from < 3)) {
            $GLOBALS['SITE_DB']->create_table('stafflinks', array(
                'id' => '*AUTO',
                'link' => 'URLPATH',
                'link_title' => 'SHORT_TEXT',
                'link_desc' => 'LONG_TEXT',
            ));

            $default_links = array(
                'compo.sr' => 'http://compo.sr/',
                'compo.sr (topics with unread posts)' => get_brand_page_url(array('page' => 'vforums', 'type' => 'unread'), 'forum'),
                'ocProducts (web development services)' => 'http://ocproducts.com/',
                'Transifex (Composr language translations)' => 'https://www.transifex.com/organization/ocproducts/dashboard',
                'Google Alerts' => 'http://www.google.com/alerts',
                'Google Analytics' => 'http://www.google.com/analytics/',
                'Google Webmaster Tools' => 'https://www.google.com/webmasters/tools',
                'Google Apps (gmail for domains, etc)' => 'http://www.google.com/apps/intl/en/group/index.html',
                'Google Chrome (web browser)' => 'http://www.google.com/chrome',
                'Google Chrome addons' => 'https://chrome.google.com/extensions/featured/web_dev',
                'SharedCount (social sharing stats)' => 'http://www.sharedcount.com/',
                'Facebook Insights (Facebook Analytics)' => 'https://developers.facebook.com/docs/insights/',
                'Paint.net (free graphics tool)' => 'http://www.getpaint.net/',
                'PNGGauntlet (compress PNG files, Windows)' => 'http://benhollis.net/software/pnggauntlet/',
                'ImageOptim (compress PNG files, Mac)' => 'http://imageoptim.pornel.net/',
                'Find Icons (free icons)' => 'http://findicons.com/',
                'stock.xchng (free stock art)' => 'http://sxc.hu/',
                'Kompozer (Web design tool)' => 'http://www.kompozer.net/',
                'DiffMerge' => 'http://www.sourcegear.com/diffmerge/',
                'Jing (record screencasts)' => 'http://www.jingproject.com/',
                'SiteRay (site quality auditing)' => 'http://www.silktide.com/siteray',
                'Smashing Magazine (web design articles)' => 'http://www.smashingmagazine.com/',
                'w3schools (learn web technologies)' => 'http://www.w3schools.com/',
                // NB: Not including a web host, as the user will likely already have one
                //'GoDaddy (Domains and SSL certificates)'=>'http://www.godaddy.com/', // A bit overly-specific, plus similar to the above
            );
            foreach ($default_links as $link_title => $url) {
                $GLOBALS['SITE_DB']->query_insert('stafflinks', array(
                    'link' => $url,
                    'link_title' => $link_title,
                    'link_desc' => $link_title,
                ));
            }
        }
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return tempcode The result of execution.
     */
    public function run($map)
    {
        require_lang('staff_checklist');

        $newdata = post_param_string('staff_links_edit', null);
        if (!is_null($newdata)) {
            $items = explode("\n", $newdata);
            $GLOBALS['SITE_DB']->query_delete('stafflinks');

            foreach ($items as $i) {
                $q = trim($i);
                if (!empty($q)) {
                    $bits = explode('=', $q);
                    if (count($bits) >= 2) {
                        $last_bit = array_pop($bits);
                        $bits = array(implode('=', $bits), $last_bit);
                        $link = $bits[0];
                    } else {
                        $link = $q;
                    }

                    require_code('files2');
                    $meta_details = get_webpage_meta_details($link);
                    $link_title = $meta_details['t_title'];

                    if (count($bits) == 2) {
                        $link_desc = $bits[1];
                    } else {
                        $link_desc = $link_title;
                    }
                    $GLOBALS['SITE_DB']->query_insert('stafflinks', array(
                        'link' => $link,
                        'link_title' => $link_title,
                        'link_desc' => $link_desc,
                    ));
                }
            }

            decache('main_staff_links');
        }

        $rows = $GLOBALS['SITE_DB']->query_select('stafflinks', array('*'));
        $formatted_staff_links = array();
        $unformatted_staff_links = array();
        foreach ($rows as $r) {
            if ($r['link_title'] == '') {
                $r['link_title'] = $r['link_desc'];
            }
            if (strlen($r['link_title']) > strlen($r['link_desc'])) {
                $r['link_title'] = $r['link_desc'];
            }

            $formatted_staff_links[] = array(
                'URL' => $r['link'],
                'TITLE' => $r['link_title'],
                'DESC' => ($r['link_title'] == $r['link_desc']) ? '' : $r['link_desc'],
            );
            $unformatted_staff_links[] = array('LINKS' => $r['link'] . '=' . $r['link_desc']);
        }

        $map_comcode = '';
        foreach ($map as $key => $val) {
            $map_comcode .= ' ' . $key . '="' . addslashes($val) . '"';
        }
        return do_template('BLOCK_MAIN_STAFF_LINKS', array('_GUID' => '555150e7f1626ae0689158b1ecc1d85b', 'URL' => get_self_url(), 'BLOCK_NAME' => 'main_staff_links', 'MAP' => $map_comcode, 'FORMATTED_LINKS' => $formatted_staff_links, 'UNFORMATTED_LINKS' => $unformatted_staff_links));
    }
}

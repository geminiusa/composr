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
class Hook_sitemap_page_grouping extends Hook_sitemap_base
{
    /**
     * Find if a page-link will be covered by this node.
     *
     * @param  ID_TEXT $page_link The page-link.
     * @return integer A SITEMAP_NODE_* constant.
     */
    public function handles_page_link($page_link)
    {
        $matches = array();
        if (preg_match('#^([^:]*):([^:]*):([^:]*)#', $page_link, $matches) != 0) {
            $zone = $matches[1];
            $page = $matches[2];
            $type = $matches[3];

            if (($zone == 'adminzone' && $page == 'admin' && $type != 'search') || ($zone == 'cms' && $page == 'cms')) {
                return SITEMAP_NODE_HANDLED;
            }
        }
        return SITEMAP_NODE_NOT_HANDLED;
    }

    /**
     * Find details of a position in the Sitemap.
     *
     * @param  ID_TEXT $page_link The page-link we are finding.
     * @param  ?string $callback Callback function to send discovered page-links to (null: return).
     * @param  ?array $valid_node_types List of node types we will return/recurse-through (null: no limit)
     * @param  ?integer $child_cutoff Maximum number of children before we cut off all children (null: no limit).
     * @param  ?integer $max_recurse_depth How deep to go from the Sitemap root (null: no limit).
     * @param  integer $recurse_level Our recursion depth (used to limit recursion, or to calculate importance of page-link, used for instance by XML Sitemap [deeper is typically less important]).
     * @param  integer $options A bitmask of SITEMAP_GEN_* options.
     * @param  ID_TEXT $zone The zone we will consider ourselves to be operating in (needed due to transparent redirects feature)
     * @param  integer $meta_gather A bitmask of SITEMAP_GATHER_* constants, of extra data to include.
     * @param  ?array $orphaned_pages Database row (null: lookup).
     * @param  boolean $return_anyway Whether to return the structure even if there was a callback. Do not pass this setting through via recursion due to memory concerns, it is used only to gather information to detect and prevent parent/child duplication of default entry points.
     * @return ?array Node structure (null: working via callback / error).
     */
    public function get_node($page_link, $callback = null, $valid_node_types = null, $child_cutoff = null, $max_recurse_depth = null, $recurse_level = 0, $options = 0, $zone = '_SEARCH', $meta_gather = 0, $orphaned_pages = null, $return_anyway = false)
    {
        require_lang('menus');

        $matches = array();
        preg_match('#^([^:]*):([^:]*):([^:]*)#', $page_link, $matches);
        $page_grouping = $matches[3];

        $icon = mixed();
        $lang_string = strtoupper($page_grouping);
        $description = mixed();

        // Locate all pages in page groupings, and the icon for this page grouping
        $pages_found = array();
        $links = get_page_grouping_links();
        foreach ($links as $link) {
            list($_page_grouping) = $link;

            if (($_page_grouping == '') || ($_page_grouping == $page_grouping)) {
                if ((is_array($link)) && (is_string($link[2][2]))) {
                    $pages_found[$link[2][2] . ':' . $link[2][0]] = true;
                }
            }

            if (($_page_grouping == '') && (is_array($link[2])) && (($link[2][0] == 'cms') || ($link[2][0] == 'admin')) && ($link[2][1] == array('type' => $page_grouping))) {
                $icon = $link[1];
                $lang_string = $link[3];
                if (($meta_gather & SITEMAP_GATHER_DESCRIPTION) != 0) {
                    if (isset($link[4])) {
                        $description = comcode_lang_string($link[4]);
                    }
                }
            }
        }

        if ($zone == '_SEARCH') {
            // Work out what the zone should be from the $page_grouping (overrides $zone, which we don't trust and must replace)
            switch ($page_grouping) {
                case 'structure':
                case 'audit':
                case 'style':
                case 'setup':
                case 'tools':
                case 'security':
                    $zone = 'adminzone';
                    break;

                case 'cms':
                    $zone = 'cms';
                    break;

                case 'collaboration':
                case 'pages':
                case 'rich_content':
                case 'site_meta':
                case 'social':
                default:
                    $zone = (get_option('collapse_user_zones') == '1') ? '' : 'site';
                    break;
            }
        }

        $require_permission_support = (($options & SITEMAP_GEN_REQUIRE_PERMISSION_SUPPORT) != 0);

        // Meddle with the page-link, as we don't want to support direct linking to it
        $deployed_page_link = $page_link;
        if (!$require_permission_support) {
            if ($zone != 'adminzone' && $zone != 'cms') {
                if ($page_grouping == 'collaboration') {
                    $deployed_page_link = 'collaboration:';
                } else {
                    if (($options & SITEMAP_GEN_NO_EMPTY_PAGE_LINKS) == 0) {
                        $deployed_page_link = ''; // Can't actually always visit it (well you can, but we don't want to promote it)
                    }
                }
            }
        }

        // Our node
        $struct = array(
            'title' => is_object($lang_string) ? $lang_string : do_lang_tempcode($lang_string),
            'content_type' => 'page_grouping',
            'content_id' => $page_grouping,
            'modifiers' => array(),
            'only_on_page' => '',
            'page_link' => $deployed_page_link,
            'url' => null,
            'extra_meta' => array(
                'description' => $description,
                'image' => ($icon === null) ? null : find_theme_image('icons/24x24/' . $icon),
                'image_2x' => ($icon === null) ? null : find_theme_image('icons/48x48/' . $icon),
                'add_date' => null,
                'edit_date' => null,
                'submitter' => null,
                'views' => null,
                'rating' => null,
                'meta_keywords' => null,
                'meta_description' => null,
                'categories' => null,
                'validated' => null,
                'db_row' => null,
            ),
            'permissions' => array(
                array(
                    'type' => 'zone',
                    'zone_name' => $zone,
                    'is_owned_at_this_level' => false,
                ),
            ),
            'children' => null,
            'has_possible_children' => true,

            // These are likely to be changed in individual hooks
            'sitemap_priority' => SITEMAP_IMPORTANCE_MEDIUM,
            'sitemap_refreshfreq' => 'weekly',

            'privilege_page' => null,
        );

        if (!$this->_check_node_permissions($struct)) {
            return null;
        }

        if ($callback !== null) {
            call_user_func($callback, $struct);
        }

        // Categories done after node callback, to ensure sensible ordering
        if (($max_recurse_depth === null) || ($recurse_level < $max_recurse_depth)) {
            $children = array();

            $root_comcode_pages_validation = get_root_comcode_pages($zone);

            $page_sitemap_ob = $this->_get_sitemap_object('page');
            $entry_point_sitemap_ob = $this->_get_sitemap_object('entry_point');
            $comcode_page_sitemap_ob = $this->_get_sitemap_object('comcode_page');

            // Directly defined in page grouping hook
            $child_links = array();
            foreach ($links as $link) {
                if ($link[0] == $page_grouping) {
                    $title = $link[3];
                    $icon = $link[1];

                    if (!is_array($link[2])) { // Plain URL
                        $children[] = array(
                            'title' => $title,
                            'content_type' => 'url',
                            'content_id' => null,
                            'modifiers' => array(),
                            'only_on_page' => '',
                            'page_link' => null,
                            'url' => $link[2],
                            'extra_meta' => array(
                                'description' => null,
                                'image' => null,
                                'image_2x' => null,
                                'add_date' => null,
                                'edit_date' => null,
                                'submitter' => null,
                                'views' => null,
                                'rating' => null,
                                'meta_keywords' => null,
                                'meta_description' => null,
                                'categories' => null,
                                'validated' => null,
                                'db_row' => null,
                            ),
                            'permissions' => array(),
                            'has_possible_children' => false,

                            // These are likely to be changed in individual hooks
                            'sitemap_priority' => SITEMAP_IMPORTANCE_MEDIUM,
                            'sitemap_refreshfreq' => 'weekly',
                        );
                        continue;
                    }

                    $page = $link[2][0];
                    $_zone = $link[2][2];
                    if (!is_string($_zone)) {
                        continue; // Gone missing somehow
                    }
                    if ($zone != $_zone) { // Not doesn't match. If the page exists in our node's zone as a transparent redirect, override it as in here
                        require_code('site');
                        $details = _request_page($page, $zone, 'redirect');
                        if ($details !== false) {
                            $_zone = $zone;
                        }
                    }

                    $child_page_link = $_zone . ':' . $page;
                    foreach ($link[2][1] as $key => $val) {
                        if (!is_string($val)) {
                            $val = strval($val);
                        }

                        if ($key == 'type' || $key == 'id') {
                            $child_page_link .= ':' . urlencode($val);
                        } else {
                            $child_page_link .= ':' . urlencode($key) . '=' . urlencode($val);
                        }
                    }

                    $details = $this->_request_page_details($page, $_zone);
                    if ($details === false) {
                        continue;
                    }
                    $page_type = strtolower($details[0]);

                    $child_description = null;
                    if (isset($link[4])) {
                        $child_description = (is_object($link[4])) ? $link[4] : comcode_lang_string($link[4]);
                    }

                    $child_links[] = array($title, $child_page_link, $icon, $page_type, $child_description);
                }
            }

            // Extra ones to get merged in? (orphaned children)
            if ($page_grouping == 'pages' || $page_grouping == 'tools' || $page_grouping == 'cms') {
                if ($orphaned_pages === null) {
                    // Any left-behind pages
                    $orphaned_pages = array();
                    $pages = find_all_pages_wrap($zone, false, /*$consider_redirects=*/true);
                    foreach ($pages as $page => $page_type) {
                        if (is_integer($page)) {
                            $page = strval($page);
                        }

                        if (preg_match('#^redirect:#', $page_type) != 0) {
                            $details = $this->_request_page_details($page, $zone);
                            $page_type = strtolower($details[0]);
                            $pages[$page] = $page_type;
                        }

                        if ((!isset($pages_found[$zone . ':' . $page])) && ((strpos($page_type, 'comcode') === false) || (isset($root_comcode_pages_validation[$page])))) {
                            if ($this->_is_page_omitted_from_sitemap($zone, $page)) {
                                continue;
                            }

                            $orphaned_pages[$page] = $page_type;
                        }
                    }
                }

                foreach ($orphaned_pages as $page => $page_type) {
                    if (is_integer($page)) {
                        $page = strval($page);
                    }

                    if ($page == get_zone_default_page($zone)) {
                        continue;
                    }

                    $child_page_link = $zone . ':' . $page;

                    $child_links[] = array(titleify($page), $child_page_link, null, $page_type, null);
                }
            }

            $consider_validation = (($options & SITEMAP_GEN_CONSIDER_VALIDATION) != 0);

            // Render children, in title order
            foreach ($child_links as $child_link) {
                $title = $child_link[0];
                $description = $child_link[4];
                $icon = $child_link[2];
                $child_page_link = $child_link[1];
                $page_type = $child_link[3];

                $child_row = ($icon === null) ? null/*we know nothing of relevance*/ : array($title, $icon, $description);

                if (($valid_node_types !== null) && (!in_array('page', $valid_node_types))) {
                    continue;
                }

                if (strpos($page_type, 'comcode') !== false) {
                    if (($valid_node_types !== null) && (!in_array('comcode_page', $valid_node_types))) {
                        continue;
                    }

                    if (($consider_validation) && (isset($root_comcode_pages_validation[$page])) && ($root_comcode_pages_validation[$page] == 0)) {
                        continue;
                    }

                    $child_node = $comcode_page_sitemap_ob->get_node($child_page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level + 1, $options, $zone, $meta_gather, $child_row);
                } else {
                    if (($valid_node_types !== null) && (!in_array('page', $valid_node_types))) {
                        continue;
                    }

                    if (preg_match('#^([^:]*):([^:]*)(:browse|:\w+=|$)#', $child_page_link, $matches) != 0) {
                        $child_node = $page_sitemap_ob->get_node($child_page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level + 1, $options, $zone, $meta_gather, $child_row);
                    } else {
                        $child_node = $entry_point_sitemap_ob->get_node($child_page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level + 1, $options, $zone, $meta_gather, $child_row);
                    }
                }
                if ($child_node !== null) {
                    $children[] = $child_node;
                }
            }

            sort_maps_by($children, 'title');

            $struct['children'] = $children;
        }

        return ($callback === null || $return_anyway) ? $struct : null;
    }
}

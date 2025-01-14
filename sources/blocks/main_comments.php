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
 * @package    core_feedback_features
 */

/**
 * Block class.
 */
class Block_main_comments
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array('param', 'page', 'extra_param_from', 'reverse', 'forum', 'invisible_if_no_comments', 'reviews', 'max', 'title', 'explicit_allow');
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    /*
    function caching_environment() // We can't cache this block, because it needs to execute in order to allow commenting
    {
        $info['cache_on']='array(((array_key_exists(\'max\',$map)) && ($map[\'max\']!='-1'))?intval($map[\'max\']):NULL,((!array_key_exists(\'reviews\',$map)) || ($map[\'reviews\']==\'1\')),has_privilege(get_member(),\'comment\'),array_key_exists(\'extra_param_from\',$map)?$map[\'extra_param_from\']:\'\',array_key_exists(\'param\',$map)?$map[\'param\']:\'main\',array_key_exists(\'page\',$map)?$map[\'page\']:get_page_name(),array_key_exists(\'forum\',$map)?$map[\'forum\']:NULL,((array_key_exists(\'invisible_if_no_comments\',$map)) && ($map[\'invisible_if_no_comments\']==\'1\')),((array_key_exists(\'reverse\',$map)) && ($map[\'reverse\']==\'1\')),array_key_exists(\'title\',$map)?$map[\'title\']:\'\',(array_key_exists(\'explicit_allow\', $map)) ? ($map[\'explicit_allow\'] == \'1\') : false)';
        $info['ttl']=60*5;
        return $info;
    }*/

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return tempcode The result of execution.
     */
    public function run($map)
    {
        if (!array_key_exists('param', $map)) {
            $map['param'] = 'main';
        }
        if (!array_key_exists('page', $map)) {
            $map['page'] = str_replace('-', '_', get_page_name());
        }

        if (array_key_exists('extra_param_from', $map)) {
            $extra = '_' . $map['extra_param_from'];
        } else {
            $extra = '';
        }

        $explicit_allow = (array_key_exists('explicit_allow', $map)) ? ($map['explicit_allow'] == '1') : false;

        require_code('feedback');

        $submitted = (post_param_integer('_comment_form_post', 0) == 1);

        $self_url = build_url(array('page' => '_SELF'), '_SELF', null, true, false, true);
        $self_title = empty($map['title']) ? $map['page'] : $map['title'];
        $test_changed = post_param_string('title', null);
        if (!is_null($test_changed)) {
            decache('main_comments');
        }
        $hidden = $submitted ? actualise_post_comment(true, 'block_main_comments', $map['page'] . '_' . $map['param'] . $extra, $self_url, $self_title, array_key_exists('forum', $map) ? $map['forum'] : null, false, null, $explicit_allow) : false;

        if ((array_key_exists('title', $_POST)) && ($hidden) && ($submitted)) {
            attach_message(do_lang_tempcode('MESSAGE_POSTED'), 'inform');

            if (get_forum_type() == 'cns') {
                if (addon_installed('unvalidated')) {
                    require_code('submit');
                    $validate_url = get_self_url(true, false, array('keep_session' => null));
                    $_validate_url = build_url(array('page' => 'topics', 'type' => 'validate_post', 'id' => $GLOBALS['LAST_POST_ID'], 'redirect' => $validate_url), get_module_zone('topics'), null, false, false, true);
                    $validate_url = $_validate_url->evaluate();
                    send_validation_request('MAKE_POST', 'f_posts', false, $GLOBALS['LAST_POST_ID'], $validate_url);
                }
            }
        }

        $invisible_if_no_comments = ((array_key_exists('invisible_if_no_comments', $map)) && ($map['invisible_if_no_comments'] == '1'));
        $reverse = ((array_key_exists('reverse', $map)) && ($map['reverse'] == '1'));
        $allow_reviews = ((array_key_exists('reviews', $map)) && ($map['reviews'] == '1'));
        $num_to_show_limit = ((array_key_exists('max', $map)) && ($map['max'] != '-1')) ? intval($map['max']) : null;

        return get_comments('block_main_comments', true, $map['page'] . '_' . $map['param'] . $extra, $invisible_if_no_comments, array_key_exists('forum', $map) ? $map['forum'] : null, null, null, $explicit_allow, $reverse, null, $allow_reviews, $num_to_show_limit);
    }
}

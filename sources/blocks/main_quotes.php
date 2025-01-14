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
 * @package    random_quotes
 */

/**
 * Block class.
 */
class Block_main_quotes
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
        $info['parameters'] = array('param', 'title');
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = 'array(array_key_exists(\'title\',$map)?$map[\'title\']:\'-\',array_key_exists(\'param\',$map)?$map[\'param\']:\'quotes\')';
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_PERMISSIVE_GROUPS;
        $info['ttl'] = 5;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return tempcode The result of execution.
     */
    public function run($map)
    {
        require_lang('quotes');

        $file = array_key_exists('param', $map) ? $map['param'] : 'quotes';
        $title = array_key_exists('title', $map) ? $map['title'] : do_lang('QUOTES');

        require_css('random_quotes');

        require_code('textfiles');

        $place = _find_text_file_path($file, '');
        if ($place == '') {
            warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html($file)));
        }

        if (!file_exists($place)) {
            warn_exit(do_lang_tempcode('DIRECTORY_NOT_FOUND', escape_html($place)));
        }
        $edit_url = new Tempcode();
        if (($file == 'quotes') && (has_actual_page_access(get_member(), 'quotes', 'adminzone'))) {
            $edit_url = build_url(array('page' => 'quotes'), 'adminzone');
        }
        return do_template('BLOCK_MAIN_QUOTES', array('_GUID' => '7cab7422f603f7b1197c940de48b99aa', 'TITLE' => $title, 'EDIT_URL' => $edit_url, 'FILE' => $file, 'CONTENT' => comcode_to_tempcode($this->get_random_line($place), null, true)));
    }

    /**
     * Get a random line from a file.
     *
     * @param  PATH $filename The filename
     * @return string The random line
     */
    public function get_random_line($filename)
    {
        $myfile = @fopen(filter_naughty($filename, true), GOOGLE_APPENGINE ? 'rb' : 'rt');
        if ($myfile === false) {
            return '';
        }
        @flock($myfile, LOCK_SH);
        $i = 0;
        $line = array();
        while (true) {
            $line[$i] = fgets($myfile, 1024);

            if (($line[$i] === false) || ($line[$i] === null)) {
                break;
            }

            if (trim($line[$i]) != '') {
                $i++;
            }
        }
        if ($i == 0) {
            return '';
        }
        $r = mt_rand(0, $i - 1);
        @flock($myfile, LOCK_UN);
        fclose($myfile);
        return trim($line[$r]);
    }
}

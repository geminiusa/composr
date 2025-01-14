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
 * @package    commandr
 */

/**
 * Hook class.
 */
class Hook_commandr_fs_raw
{
    /**
     * Standard commandr_fs listing function for Commandr FS hooks.
     *
     * @param  array $meta_dir The current meta-directory path
     * @param  string $meta_root_node The root node of the current meta-directory
     * @param  object $commandr_fs A reference to the Commandr filesystem object
     * @return ~array The final directory listing (false: failure)
     */
    public function listing($meta_dir, $meta_root_node, &$commandr_fs)
    {
        $path = get_custom_file_base();
        foreach ($meta_dir as $meta_dir_section) {
            $path .= '/' . filter_naughty($meta_dir_section);
        }

        $listing = array();
        if (is_dir($path)) {
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (($file != '.') && ($file != '..') && ($file != '.git')) {
                    $listing[] = array(
                        $file,
                        is_dir($path . '/' . $file) ? COMMANDRFS_DIR : COMMANDRFS_FILE,
                        is_dir($path . '/' . $file) ? null : filesize($path . '/' . $file),
                        filemtime($path . '/' . $file),
                    );
                }
            }
            return $listing;
        }

        return false; // Directory doesn't exist
    }

    /**
     * Standard commandr_fs directory creation function for Commandr FS hooks.
     *
     * @param  array $meta_dir The current meta-directory path
     * @param  string $meta_root_node The root node of the current meta-directory
     * @param  string $new_dir_name The new directory name
     * @param  object $commandr_fs A reference to the Commandr filesystem object
     * @return boolean Success?
     */
    public function make_directory($meta_dir, $meta_root_node, $new_dir_name, &$commandr_fs)
    {
        $new_dir_name = filter_naughty($new_dir_name);
        $path = get_custom_file_base();
        foreach ($meta_dir as $meta_dir_section) {
            $path .= '/' . filter_naughty($meta_dir_section);
        }

        if ((is_dir($path)) && (!file_exists($path . '/' . $new_dir_name)) && (is_writable_wrap($path))) {
            $ret = @mkdir($path . '/' . $new_dir_name, 0777) or warn_exit(do_lang_tempcode('WRITE_ERROR', escape_html($path . '/' . $new_dir_name)));
            fix_permissions($path . '/' . $new_dir_name, 0777);
            sync_file($path . '/' . $new_dir_name);
            return $ret;
        } else {
            return false; // Directory exists
        }
    }

    /**
     * Standard commandr_fs directory removal function for Commandr FS hooks.
     *
     * @param  array $meta_dir The current meta-directory path
     * @param  string $meta_root_node The root node of the current meta-directory
     * @param  string $dir_name The directory name
     * @param  object $commandr_fs A reference to the Commandr filesystem object
     * @return boolean Success?
     */
    public function remove_directory($meta_dir, $meta_root_node, $dir_name, &$commandr_fs)
    {
        $dir_name = filter_naughty($dir_name);
        $path = get_custom_file_base();
        foreach ($meta_dir as $meta_dir_section) {
            $path .= '/' . filter_naughty($meta_dir_section);
        }

        if ((is_dir($path)) && (file_exists($path . '/' . $dir_name)) && (is_writable_wrap($path . '/' . $dir_name))) {
            require_code('files');
            deldir_contents($path . '/' . $dir_name);
            $ret = @rmdir($path . '/' . $dir_name) or warn_exit(do_lang_tempcode('WRITE_ERROR', escape_html($path . '/' . $dir_name)));
            sync_file($path . '/' . $dir_name);
            return true;
        } else {
            return false; // Directory doesn't exist
        }
    }

    /**
     * Standard commandr_fs file removal function for Commandr FS hooks.
     *
     * @param  array $meta_dir The current meta-directory path
     * @param  string $meta_root_node The root node of the current meta-directory
     * @param  string $file_name The file name
     * @param  object $commandr_fs A reference to the Commandr filesystem object
     * @return boolean Success?
     */
    public function remove_file($meta_dir, $meta_root_node, $file_name, &$commandr_fs)
    {
        $file_name = filter_naughty($file_name);
        $path = get_custom_file_base();
        foreach ($meta_dir as $meta_dir_section) {
            $path .= '/' . filter_naughty($meta_dir_section);
        }

        if ((is_dir($path)) && (file_exists($path . '/' . $file_name)) && (is_writable_wrap($path . '/' . $file_name))) {
            $ret = @unlink($path . '/' . $file_name) or intelligent_write_error($path . '/' . $file_name);
            sync_file($path . '/' . $file_name);
            return $ret;
        } else {
            return false; // File doesn't exist
        }
    }

    /**
     * Standard commandr_fs file reading function for Commandr FS hooks.
     *
     * @param  array $meta_dir The current meta-directory path
     * @param  string $meta_root_node The root node of the current meta-directory
     * @param  string $file_name The file name
     * @param  object $commandr_fs A reference to the Commandr filesystem object
     * @return ~string The file contents (false: failure)
     */
    public function read_file($meta_dir, $meta_root_node, $file_name, &$commandr_fs)
    {
        $file_name = filter_naughty($file_name);
        $path = get_custom_file_base();
        foreach ($meta_dir as $meta_dir_section) {
            $path .= '/' . filter_naughty($meta_dir_section);
        }

        if ((is_dir($path)) && (file_exists($path . '/' . $file_name)) && (is_readable($path . '/' . $file_name))) {
            return file_get_contents($path . '/' . $file_name);
        } else {
            return false; // File doesn't exist
        }
    }

    /**
     * Standard commandr_fs file writing function for Commandr FS hooks.
     *
     * @param  array $meta_dir The current meta-directory path
     * @param  string $meta_root_node The root node of the current meta-directory
     * @param  string $file_name The file name
     * @param  string $contents The new file contents
     * @param  object $commandr_fs A reference to the Commandr filesystem object
     * @return boolean Success?
     */
    public function write_file($meta_dir, $meta_root_node, $file_name, $contents, &$commandr_fs)
    {
        $file_name = filter_naughty($file_name);
        $path = get_custom_file_base();
        foreach ($meta_dir as $meta_dir_section) {
            $path .= '/' . filter_naughty($meta_dir_section);
        }

        if ((is_dir($path)) && (((file_exists($path . '/' . $file_name)) && (is_writable_wrap($path . '/' . $file_name))) || ((!file_exists($path . '/' . $file_name)) && (is_writable_wrap($path))))) {
            $fh = @fopen($path . '/' . $file_name, GOOGLE_APPENGINE ? 'wb' : 'wt') or intelligent_write_error($path . '/' . $file_name);
            $output = fwrite($fh, $contents);
            fclose($fh);
            if ($output < strlen($contents)) {
                warn_exit(do_lang_tempcode('COULD_NOT_SAVE_FILE'));
            }
            fix_permissions($path . '/' . $file_name);
            sync_file($path . $file_name);
            return $output;
        } else {
            return false; // File doesn't exist
        }
    }
}

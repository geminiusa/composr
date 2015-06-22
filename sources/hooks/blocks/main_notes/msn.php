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
 * @package    msn
 */

/**
 * Hook class.
 */
class Hook_notes_msn
{
    /**
     * Decaching trigger for main_notes saves. See if we have to decache based on the passed filename.
     *
     * @param PATH $file Filename.
     */
    public function run($file)
    {
        if ((strpos($file, '/netlink') !== false) || ($file == 'netlink')) {
            decache('side_network');
        }
    }
}

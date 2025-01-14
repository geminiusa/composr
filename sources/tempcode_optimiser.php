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

/*
    Note that optimisation also happens in Tempcode compilation:
     - Comments optimised out.
     - Some symbols are made static.
     - Simple language string calls are made static.
     - Some directives and symbols are converted to simple PHP.
     - Static NOT expressions are optimised out (*1)
     - Some directives with static expressions, and ternary symbol calls, are optimised out (*1).

    *1 - usually due to symbols being made static, and hence knowing the outcome
*/

/**
 * Optimise for memory and execution time. This is typically used before something is cached.
 *
 * @param  object $ob Optimise a Tempcode object.
 */
function optimise_tempcode(&$ob)
{
    // Merge seq_part groups (they were only put in separately to make attach super-fast)
    if (isset($ob->seq_parts[0])) {
        $cnt = count($ob->seq_parts);
        for ($i = 1; $i < $cnt; $i++) {
            $ob->seq_parts[0] = array_merge($ob->seq_parts[0], $ob->seq_parts[$i]);
        }
        $ob->seq_parts = array($ob->seq_parts[0]);
    }

    // Remove unused bindings (we don't do inclusion tests individual seq_parts, as the binding sets are often shared via references)
    $found = array();
    foreach ($ob->seq_parts as &$seq_part_group) {
        foreach ($seq_part_group as &$seq_part) {
            $code = $ob->code_to_preexecute[$seq_part[0]];

            // Indication of dynamic variable use, cannot optimise
            if (isset($seq_part[1]['vars'])) {
                return;
            }
            if (strpos($code, "'vars'=>") !== false) {
                return;
            }

            if ((($seq_part[2] == TC_KNOWN) || ($seq_part[2] == TC_PARAMETER)) && (isset($ob->code_to_preexecute[$seq_part[0]]))) {
                foreach ($seq_part[1] as $key => $_) {
                    if (is_integer($key)) {
                        $key = strval($key);
                    }

                    if (strpos($code, '\$bound_' . $key) !== false) {
                        $found[$key] = true;
                    }
                }
            }
        }
    }
    foreach ($ob->seq_parts as &$seq_part_group) {
        foreach ($seq_part_group as &$seq_part) {
            if ((($seq_part[2] == TC_KNOWN) || ($seq_part[2] == TC_PARAMETER)) && (isset($ob->code_to_preexecute[$seq_part[0]]))) {
                foreach ($seq_part[1] as $key => $_) {
                    if (is_integer($key)) {
                        $key = strval($key);
                    }

                    if (!isset($found[$key])) {
                        unset($seq_part[1][$key]);
                    }
                }
            }
        }
    }
}

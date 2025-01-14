<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license        http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright    ocProducts Ltd
 * @package        core
 */

/*NO_API_CHECK*/
/*CQC: No check*/

/**
 * jsmin.php - PHP implementation of Douglas Crockford's JSMin.
 *
 * This is pretty much a direct port of jsmin.c to PHP with just a few
 * PHP-specific performance tweaks. Also, whereas jsmin.c reads from stdin and
 * outputs to stdout, this library accepts a string as input and returns another
 * string as output.
 *
 * PHP 5 or higher is required.
 *
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 *
 * --
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * @author Ryan Grove <ryan@wonko.com>
 * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright 2008 Ryan Grove <ryan@wonko.com> (PHP port)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.1.1 (2008-03-02)
 * @link http://code.google.com/p/jsmin-php/
 */
/*
Licensing note by ocProducts...

The "Good, not Evil" bit was inserted to this MIT-derived licence by Douglas Crockford, as a joke [reference: his talk, "The JSON Saga"].
Therefore we can interpret this in a very moderate way. Also it's definition is clear, so no one could rule a particular usage as evil as there is no legal definition of evil.
Lastly, this cogent point has been made:
"The terms of Evil are not outlined. And to use license to stop freedom is Evil [in the spirit of the original MIT licence the statement is written into], therefore the only Evil is using the software to stop its free use..."
*/

class JSMin
{
    var $a = '';
    var $b = '';
    var $input = '';
    var $inputIndex = 0;
    var $inputLength = 0;
    var $output = '';

    // -- Public Instance Methods ------------------------------------------------

    /**
     * Constructor function
     *
     * @param    string $input Javascript to minimise
     */
    function __construct($input)
    {
        $this->input = str_replace("\r\n", "\n", $input);
        $this->inputLength = strlen($this->input);
    }

    // -- Protected Instance Methods ---------------------------------------------

    /**
     * Handle internal parse situation
     *
     * @param    integer $d Action type
     * @return    ?array        Error (null: no error)
     */
    function action($d)
    {
        $chr_lf = 10;

        switch ($d) {
            // Note how these cascade

            case 1: // Absorb normal character
                $this->output .= $this->a;

            case 2: // Handle next being a string
                $this->a = $this->b;

                if ($this->a === "'" || $this->a === '"') // Entering a quoted string, jump through it
                {
                    while (true) {
                        $this->output .= $this->a;
                        $this->a = $this->get();

                        if ($this->a === null) {
                            return null;
                        }

                        if ($this->a === $this->b) {
                            break;
                        }

                        /*if (ord($this->a) <= $chr_lf)		We don't need to find this error
                        {
                            return array('Unterminated string literal.');
                        }*/

                        if ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a = $this->get();
                        }
                    }
                }

            case 3: // Handle next being a regexp, jump through it (NB: $this->b will be left unhandled after this, and will be used on next 'action' call)
                $this->b = $this->get();
                if ($this->b == '/') {
                    $this->b = $this->next__bypass_comments($this->b);
                    //if (is_array($this->b)) return $this->b;

                    // Entering a regexp
                    if ($this->b === '/' && (
                            $this->a === '(' || $this->a === ',' || $this->a === '=' ||
                            $this->a === ':' || $this->a === '[' || $this->a === '!' ||
                            $this->a === '&' || $this->a === '|' || $this->a === '?')
                    ) {

                        $this->output .= $this->a . $this->b;

                        while (true) {
                            $this->a = $this->get();

                            if ($this->a === null) {
                                return null;
                            }

                            if ($this->a === '/') {
                                break;
                            } elseif ($this->a === '\\') {
                                $this->output .= $this->a;
                                $this->a = $this->get();
                            } elseif (ord($this->a) <= $chr_lf) {
                                //return array('Unterminated regular expression literal.');	We don't need to handle errors
                                break;
                            }

                            $this->output .= $this->a;
                        }

                        $this->b = $this->get();
                        if ($this->b == '/') {
                            $this->b = $this->next__bypass_comments($this->b);
                        }
                        //if (is_array($this->b)) return $this->b;
                    }
                }
        }

        return null;
    }

    /**
     * Get the next stream token
     *
     * @return    ?string        Next stream token (null: no next token)
     */
    function get()
    {
        $ptr = &$this->inputIndex;
        if ($ptr < $this->inputLength) {
            $c = '';
            do {
                if (!isset($this->input[$ptr])) break;
                $_c = $this->input[$ptr];
                $o = ord($_c);
                $alphanumeric = ($o >= 65 && $o <= 90 || $o >= 97 && $o <= 122 || $o >= 48 && $o <= 57 || $c == '\\' || $c == '_' || $c == '$' || $o > 126);
                if (($alphanumeric) || ($c == '')) {
                    if ($c == '') {
                        $_o = $o;
                    }
                    $c .= $_c;
                    $ptr++;
                }
            } while ($alphanumeric);
        } else {
            return null;
        }

        if ($c === "\n" || $_o >= 32/*$chr_space*/) {
            return $c;
        }

        if ($c === "\r") {
            return "\n";
        }

        return ' ';
    }

    /**
     * Find whether a character is alphanumeric
     *
     * @param    string $c Character
     * @return    boolean        Whether it is
     */
    function isAlphaNum($c)
    {
        if ($c === null) {
            return false;
        }
        $o = ord($c);
        return $o >= 65 && $o <= 90 || $o >= 97 && $o <= 122 || $o >= 48 && $o <= 57 || $c == '\\' || $c == '_' || $c == '$' || $o > 126;
    }

    /**
     * Do minification process
     *
     * @return    string        Minified Javascript
     */
    function min()
    {
        $this->a = "\n"; // Something that does nothing
        $test = $this->action(3); // Initialises $this->b, essentially
        //if (is_array($test)) return $this->input; // Error

        while ($this->a !== null) {
            // Handle various white-space scenarios, or process as normal

            switch ($this->a) {
                case ' ':
                    if ($this->isAlphaNum($this->b)) {
                        $test = $this->action(1); // Keyword separation
                        //if (is_array($test)) return $this->input; // Error
                    } else {
                        $test = $this->action(2); // May be ignored, load in $b
                        //if (is_array($test)) return $this->input; // Error
                    }
                    break;

                case "\n":
                    switch ($this->b) {
                        case '{':
                        case '[':
                        case '(':
                        case '+':
                        case '-':
                            $test = $this->action(1); // Keyword separation
                            //if (is_array($test)) return $this->input; // Error
                            break;

                        case ' ':
                            $test = $this->action(3); // May be ignored, load in $b
                            //if (is_array($test)) return $this->input; // Error
                            break;

                        default:
                            if ($this->isAlphaNum($this->b)) {
                                $test = $this->action(1); // Keyword separation
                                //if (is_array($test)) return $this->input; // Error
                            } else {
                                $test = $this->action(2); // May be ignored, load in $b
                                //if (is_array($test)) return $this->input; // Error
                            }
                    }
                    break;

                default:
                    switch ($this->b) {
                        case ' ':
                            if ($this->isAlphaNum($this->a)) {
                                $test = $this->action(1); // Process $a then load in $b
                                //if (is_array($test)) return $this->input; // Error
                                break;
                            }

                            $test = $this->action(3); // May be ignored
                            //if (is_array($test)) return $this->input; // Error
                            break;

                        case "\n":
                            switch ($this->a) {
                                case '}':
                                case ']':
                                case ')':
                                case '+':
                                case '-':
                                case '"':
                                case "'":
                                    $test = $this->action(1); // Keyword in $a, process $a then $b
                                    //if (is_array($test)) return $this->input; // Error
                                    break;

                                default:
                                    if ($this->isAlphaNum($this->a)) {
                                        $test = $this->action(1);
                                        //if (is_array($test)) return $this->input; // Error
                                    } else {
                                        $test = $this->action(3);
                                        //if (is_array($test)) return $this->input; // Error
                                    }
                            }
                            break;

                        default:
                            $test = $this->action(1); // Process $a (this is the main code path, when the white-space cases of $a and $b have passed)
                            //if (is_array($test)) return $this->input; // Error
                            break;
                    }
            }
        }

        return $this->output;
    }

    /**
     * Get the next item in the stream (complex cases)
     *
     * @param    string $c Next item needing further processing
     * @return    mixed            Next item or error (array)
     */
    function next__bypass_comments($c)
    {
        if ($c === '/') {
            switch ($this->peek()) {
                case '/': // JS comment line
                    $chr_lf = 10;
                    while (true) {
                        $c = $this->get();

                        if ($c === null) {
                            return null;
                        }

                        if (ord($c) <= $chr_lf) {
                            return $c;
                        }
                    }

                case '*': // JS comment block
                    $this->get();

                    while (true) {
                        switch ($this->get()) {
                            case '*':
                                if ($this->peek() === '/') {
                                    $this->get();
                                    return ' ';
                                }
                                break;

                            case null:
                                return '';
                            //return array('Unterminated comment.');	We don't need to handle errors
                        }
                    }

                default:
                    return $c;
            }
        }

        return $c;
    }

    /**
     * Find what's next in the stream
     *
     * @return    string        What's next
     */
    function peek()
    {
        $c = $this->get();
        if ($c !== null) {
            $this->inputIndex -= strlen($c);
        }
        return $c;
    }
}

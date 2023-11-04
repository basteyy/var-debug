<?php
/**
 * @author Sebastian Eiweleit <sebastian@eiweleit.de>
 * @website https://github.com/basteyy
 * @website https://eiweleit.de
 */

declare(strict_types=1);

use JetBrains\PhpStorm\NoReturn;

if (!function_exists('varDebug')) {

    /**
     * Dump all passed variables and exit the script. Used a styled output. Accordion inspired by Raúl Barrera
     * @param ...$mixed
     * @see https://codepen.io/raubaca/pen/PZzpVe
     */
    #[NoReturn] function varDebug(...$mixed): void
    {
        /** @var string $given_variables Holds the output for the given variables */
        $given_variables_output = '';

        /** @var  $environmental_output */
        $environmental_output = '';

        /** @var  $backtrace_output */
        $backtrace_output = '';

        /** @var  $item_counter */
        $item_counter = 1;

        /** @var string $item_template The template for every item */
        $item_template = '
            <div class="item">
                <input type="checkbox" id="_%1$s"><label for="_%1$s">#%1$s %3$s</label>
                <div class="body">                                
                    <div class="title">
                        <span class="type">%2$s</span>
                        <span class="name">%5$s</span>
                    </div>     
                    <pre><code id="debug_%1$s" class="language-php">%4$s</code></pre>
                </div>
            </div>';

        $global_template = '
            <div class="item global">
                <input type="checkbox" id="_%1$s"><label for="_%1$s"><span>#%1$s %3$s <span style="font-weight: lighter; font-style: italic"><abbr title="Global variables are accessible from everywhere in your code">(global)</abbr></span></span> </label>      
                <div class="body">  
                    %4$s
                </div>
            </div>';

        $global_item_template = '
                    <div class="item">  
                        <div class="title">
                            <span class="type"></span>
                            <span class="name copy">%1$s</span>
                        </div>                         
                        <pre><code id="debug_%1$s" class="language-php">%2$s</code></pre>
                    </div>';



        $backtrace_template = '
            <div class="item global">
                <input type="checkbox" id="_%1$s"><label for="_%1$s"><span>#%1$s %3$s <span style="font-weight: lighter; font-style: italic"><abbr title="Backtrace walked the application back to the beginning">(backtrace)</abbr></span></span> </label>      
                <div class="body">  
                    %4$s
                </div>
            </div>';

        $backtrace_item_template = '
            <div class="item global">
                <input type="checkbox" id="_%1$s">
                    <label for="_%1$s"> <span>#%2$s %3$s <abbr title="Line in file where the function was called" style="margin-left: 2em">%4$s</abbr><abbr title="Used function" style="margin-left: 2em;font-style: italic">%5$s</abbr></span> </label>      
                <div class="body"> %6$s </div>
            </div>';

        // Check if already any output is sent to the browser
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        ob_start();

        // Cache output for later use
        $cache_output = function ($item) {
            ob_start();
            try {
                var_dump($item);
                $data = ob_get_clean();
                return is_string($data) ? (htmlentities($data)) : 'nodata' . $data;
            } catch (Throwable $ex) {
                // PHP8 ArgumentCountError for 0 arguments, probably.
                // in PHP < 8 this was just a warning
                ob_end_clean();
                throw $ex;
            }
        };

        $unknown_explanation_helper = function ($name) : string {
            if ($name === null) {
                return '<span style="font-weight: lighter; font-style: italic"><abbr title="The name of this variable could not be determined. Maybe the values which are shown to are passed directly to varDebug">unknown</abbr></span>';
            } else {
                return '<span class="copy">$' . $name . '</span>';
            }
        };

        $x = 0;
        $data_collection = '';
        foreach ($mixed as $item) {

            // get the type of the item
            $type = gettype($item);

            // try to get the name of the variable
            $global_name = array_keys($GLOBALS, $item, true)[0] ?? null;

            $given_variables_output .= sprintf($item_template,
                $item_counter,
                $type,                                              // type of the variable
                $global_name ? '$' . $global_name : 'unknown' ,     // name of the variable
                $cache_output($item),                               // value of the variable
                $unknown_explanation_helper($global_name)           // explanation for unknown variables
            );

            $item_counter++;
        }

        $environmental = [];

        if (!defined('VAR_DEBUG_REQUEST') || true === VAR_DEBUG_REQUEST) {
            $environmental['_REQUEST'] = $_REQUEST;
        }

        if (!defined('VAR_DEBUG_POST') || true === VAR_DEBUG_POST) {
            $environmental['_POST'] = $_POST;
        }

        if (!defined('VAR_DEBUG_GET') || true === VAR_DEBUG_GET) {
            $environmental['_GET'] = $_GET;
        }

        if (!defined('VAR_DEBUG_SERVER') || true === VAR_DEBUG_SERVER) {
            $environmental['_SERVER'] = $_SERVER;
        }


        foreach ($environmental as $env => $values) {
            $data_collection = '';

            if (count($values) > 0 ) {

                foreach ($values as $name => $content) {

                    $data_collection .= '';

                    $data_collection .= sprintf($global_item_template,
                        '$' . $env . '[\'' . $name . '\']',
                        $cache_output($content));
                }

            } else {
                $data_collection = 'No Items found' ;
            }

            // $data_collection .= $cache_output();
            $data_collection .= '';

            $environmental_output .= sprintf($global_template, $item_counter, 'global', '$' . $env, $data_collection);
            $item_counter++;
        }


        // Check if backtrace isn't disabled
        $backtrace_output = '';
        if (!defined('VAR_DEBUG_BACKTRACE') || VAR_DEBUG_BACKTRACE) {
            $backtrace = '';

            $backtrace_counter = $item_counter;
            $item_counter++;

            $backtrace_step_counter = 1;

            foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $file) {

                if (!$file['file']) continue; // Skip internal functions (like varDebug()

                $filename = $file['file'];
                $line = $file['line'];
                $function = $file['function'];

                // File exists and is readable?
                $file_part = '';
                if (is_readable($filename)) {
                    // Open file and search for function
                    $file_content = file_get_contents($filename);
                    $file_content = explode("\n", $file_content);
                    $file_content = array_slice($file_content, $line - 10, 20);
                    $file_content = implode("\n", $file_content);
                    $file_content = htmlentities($file_content);

                    if (!str_starts_with($file_content, '<?php') && !str_starts_with($file_content, '<?=')) {
                        $file_content = '<?php' . PHP_EOL . $file_content;
                    }

                    $file_part = '<pre><code class="language-php">' . highlight_string($file_content, true) . '</code></pre>';
                }

                $backtrace .= sprintf($backtrace_item_template,
                    $item_counter,                  // total counter
                    $backtrace_step_counter,        // step counter inside backtrace
                    $filename,                      // filename
                    $line,                          // line number
                    $function,                      // function name
                    $file_part                      // file content if available
                );

                $item_counter++;
                $backtrace_step_counter++;
            }

            $backtrace_output .= sprintf($backtrace_template, $backtrace_counter, 'backtrace', 'BACKTRACE', $backtrace);
        }

        http_response_code(503);
        ob_clean();
        include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';
        exit();
    }
}
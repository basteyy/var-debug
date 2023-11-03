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
        ob_start();
        $cache_output = function ($item) {
            ob_start();
            try {
                var_dump($item);
                $data = ob_get_clean();
                return is_string($data) ? (htmlentities($data)) : 'nodata' . $data;
            } catch (\Throwable $ex) {
                // PHP8 ArgumentCountError for 0 arguments, probably.
                // in php<8 this was just a warning
                ob_end_clean();
                throw $ex;
            }
        };

        $x = 0;
        $data_collection = '';
        foreach ($mixed as $item) {
            $x++;
            $xx = time() . $x;
            $data_collection .= '<div><input type="checkbox" id="_debug_' . $xx . '" checked /><label for="_debug_' . $xx . '">#' . $x .
                ' <span class="google" data-debug="debug_' . $xx . '">Google</span></label> <pre><code id="debug_' . $xx . '">';
            $data_collection .= $cache_output($item);
            $data_collection .= '</code></pre></div>';
        }

        $environmental = ['SERVER' => $_SERVER, 'POST' => $_POST, 'GET' => $_GET, 'REQUEST' => $_REQUEST];

        foreach ($environmental as $env => $values) {
            $data_collection .= '<div id="' . $env . '"><input type="checkbox" id="_' . $env . '"><label for="_' . $env . '">$_' . $env . '</label> <pre><code><table>';

            foreach ($values as $name => $content) {
                $data_collection .= sprintf('<tr><td class="title"><span class="copyme">$_%s[\'%s\']</span> </td> <td>=&gt;</td>  <td><pre class="in-table"><code>%s</code></pre></td></tr>',
                    $env,
                    $name,
                    $cache_output
                    ($content));
            }

            // $data_collection .= $cache_output();
            $data_collection .= '</table></code></pre></div>' . PHP_EOL;
        }
        http_response_code(503);
        ob_clean();
        include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';
        exit();
    }
}
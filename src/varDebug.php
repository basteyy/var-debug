<?php declare(strict_types=1);

if (!function_exists('varDebug')) {
    /**
     * varDebug is just a simple var_dump on steroids
     * @param ...$data
     */
    function varDebug(...$data)
    {
        ob_end_clean();
        echo '<pre>';
        foreach ($data as $item) {
            echo '<code>';
            var_dump($item);
            echo '</code><hr />';
        }
        echo '</pre>';
        die();
    }
}
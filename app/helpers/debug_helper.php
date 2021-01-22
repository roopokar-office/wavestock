<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('dd')) {
    function dd(...$vars) {
        foreach ($vars as $v) {
            echo '<pre>';
            print_r($v);
            echo '</pre>';
        }

        exit(1);
    }
}

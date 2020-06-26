<?php

if ( ! function_exists('service')) {
    function service($service = null) {
        $class = '\\App\\Http\\Services\\'.ucfirst($service).'Service';
        return resolve($class);
    }
}
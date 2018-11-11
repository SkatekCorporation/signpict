<?php

require "vendor/autoload.php";

define("TMP", (__DIR__ . '/tmp'));

define("DEFAULT_DESTINATION", __DIR__ . DS . 'images' . DS . 'destination');
define("DEFAULT_SOURCE", __DIR__ . DS . 'images' . DS . 'source');
define("DEFAULT_LOGO", __DIR__ . DS . 'images' . DS . 'logo');

function getOptions(){
    return getopt("s:d:c:l:", ['source:', 'destination:', 'copy:', 'logo:']);
}
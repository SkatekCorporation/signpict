<?php

require "vendor/autoload.php";

define("TMP", (__DIR__ . '/tmp'));

define("DEFAULT_DESTINATION", __DIR__ . DS . 'images' . DS . 'destination' . DS);
define("DEFAULT_SOURCE", __DIR__ . DS . 'images' . DS . 'source' . DS);
define("DEFAULT_LOGO", __DIR__ . DS . 'images' . DS . 'logo' . DS);

function getOptions(){
    return getopt("s:d:c:l:", ['source:', 'destination:', 'copy:', 'logo:']);
}
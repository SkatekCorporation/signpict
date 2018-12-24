<?php

require "requires.php";

use Cake\Filesystem\Folder;
use App\App;

$options = getOptions();

if(empty($options['s'])){
    $options['s'] = DEFAULT_SOURCE;
}

if(empty($options['d'])){
    $options['d'] = DEFAULT_DESTINATION;
}

if(empty($options['l'])){
    $options['l'] = DEFAULT_LOGO;
}

$app = new App(new Folder($options['s']), new Folder($options['d']), null, $options);

$app->run(500, 500);
// $app->getLogo();

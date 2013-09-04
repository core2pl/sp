<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 04.09.13
 * Time: 21:55
 * To change this template use File | Settings | File Templates.
 */

$config = [
    'routings' => [
        ['/install/:action' => '\objects\installer']
    ],
    'theme' => 'default',
    'site' => [
        'root_directory' => '/'
    ]
];
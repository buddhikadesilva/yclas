<?php defined('SYSPATH') or die('No direct script access.');
return array
(
    'default' => 'file',
    //'prefix'  => 'cache1_',

    'file'  => array
    (
        'driver'             => 'file',
        'cache_dir'          => APPPATH.'cache/',
        'default_expire'     => 3600,
        'ignore_on_delete'   => array(),
    ),

    'apc'      => array(
        'driver'             => 'apc',
        'default_expire'     => 3600,
    ),

    'apcu'      => array(
        'driver'             => 'apcu',
        'default_expire'     => 3600,
    ),

    'memcache' => array
    (
        'driver'             => 'memcache',
        'default_expire'     => 3600,
        'compression'        => FALSE,              // Use Zlib compression
                                                    // (can cause issues with integers)
        'servers'            => array
        (
            'local' => array
            (
                'host'             => 'localhost',  // Memcache Server
                'port'             => 11211,        // Memcache port number
                'persistent'       => FALSE,        // Persistent connection
            ),
        ),
        'instant_death'      => TRUE,
    ),

);

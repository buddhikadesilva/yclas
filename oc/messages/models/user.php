<?php defined('SYSPATH') OR die('No direct script access.');

return array(

    'email' => array(
        'unique' => __('A user with the email you specified already exists'),
    ),
    'phone' => array(
        'unique' => __('Phone number in use'),
        'phone'  => __('Invalid phone number, please put your country extension +XX'),
    ),

);

<?php defined('SYSPATH') or die('No direct access allowed.');


/**
 * custom options for the theme
 * @var array
 */
return array(   'theme' => array(	       'type'      => 'text',
                                            'display'   => 'select',
                                            'label'     => __('Change the color theme'),
                                            'options'   => array(   'default'   => 'Blue',
                                                                    'green'     => 'Green',
                                                                    'orange'    => 'Orange',
                                                                ),
                                            'default'   => 'default',
                                            'required'  => TRUE,
                                            'category'  => __('Color'),
                                            ),

                /*'admin_theme' => array(     'type'      => 'text',
                                            'display'   => 'select',
                                            'label'     => __('Change the admin color theme'),
                                            'options'   => array(   'bootstrap' => 'Original',
                                                                    'cerulean'  => 'Dark Blue',
                                                                    'cosmo'     => 'Metro Style',
                                                                    'spacelab'  => 'Nice Grey',
                                                                    'united'    => 'Purple / Orange',
                                                                ), 
                                            'default'   => 'bootstrap',
                                            'required'  => TRUE),*/

                'category_badge' => array(  'type'      => 'text',
                                            'display'   => 'select',
                                            'label'     => __('Disable category counter badge'),
                                            'options'   => array(   '1' => __('Yes'),
                                                                    '0'  => __('No'),
                                                                ), 
                                            'default'   => '0',
                                            'required'  => TRUE,
                                            'category'  => __('Layout')),

                'hide_description_icon' => array(   'type'      => 'text',
                                                    'display'   => 'select',
                                                    'label'     => __('Hide icon on category/location description'),
                                                    'options'   => array(   '1' => __('Yes'),
                                                                            '0' => __('No'),
                                                                            ), 
                                                    'default'   => '0',
                                                    'required'  => TRUE,
                                                    'category'  => __('Listing')),

);
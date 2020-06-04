<?php defined('SYSPATH') or die('No direct access allowed.');


/**
 * custom options for the theme
 * @var array
 */
return array(   'theme' => array(	       'type'      => 'text',
                                            'display'   => 'select',
                                            'label'     => __('Change the color theme'),
                                            'options'   => array(   'default'   => 'Default',
																	'mint'   => 'Mint',
																	'elite'   => 'Charcoal/Gold',
																	'plumb'   => 'Plumb',
                                                                ),
                                            'default'   => 'default',
                                            'required'  => TRUE,
                                            'category'  => __('Color')
                                            ),

                'category_badge' => array(  'type'      => 'text',
                                            'display'   => 'select',
                                            'label'     => __('Disable category counter badge'),
                                            'options'   => array(   '1' => __('Yes'),
                                                                    '0'  => __('No'),
                                                                ), 
                                            'default'   => '0',
                                            'required'  => TRUE,
                                            'category'  => __('Layout')),

                'maintitle_banner' => array(   'type'      => 'text',
                                            'display'   => 'text',
                                            'label'     => __('Text of the title in the main banner in home'),
                                            'category'  => __('Homepage')),

                'maintitle_lowerbanner' => array(   'type'      => 'text',
                                            'display'   => 'text',
                                            'label'     => __('Text of the title in the lower banner in home'),
                                            'category'  => __('Homepage')),
                                                            
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
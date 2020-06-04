<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * blog widget reader
 *
 * @author      Oliver <oliver@open-classifieds.com>
 * @package     Widget
 * @copyright   (c) 2009-2018 Open Classifieds Team
 * @license     GPL v3
 */


class Widget_Blog extends Widget
{

    public function __construct()
    {

        $this->title        = __('Blog posts');
        $this->description  = __('Blog posts reader');

        $this->fields = array(  'blog_posts_limit' => array(  'type'      => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Blog posts limit, if none display all'),
                                                        'default'   => '5',
                                                        'required'  => FALSE),

                                'widget_title'  => array(  'type'      => 'text',
                                                        'display'   => 'text',
                                                        'label'     => __('Widget title displayed'),
                                                        'default'   => 'Latest blog posts',
                                                        'required'  => FALSE),
                                );
    }

    /**
     * get the title for the widget
     * @param string $title we will use it for the loaded widgets
     * @return string
     */
    public function title($title = NULL)
    {
        return parent::title($this->widget_title);
    }

    /**
     * Automatically executed before the widget action. Can be used to set
     * class properties, do authorization checks, and execute other custom code.
     *
     * @return  void
     */
    public function before()
    {
        $posts = (new Model_Post())
                ->where('status','=',Model_Post::STATUS_ACTIVE)
                ->where('id_forum', 'IS', NULL)
                ->order_by('created','desc');

        if ($this->blog_posts_limit != NULL OR $this->blog_posts_limit != '')
        {
            $posts = $posts->limit($this->blog_posts_limit);
        }

        $this->posts = $posts->cached()->find_all();
    }

}
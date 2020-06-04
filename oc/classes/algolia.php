<?php
/**
 * Class to work on algolia using the cli
 *
 * @package    OC
 * @category   Helper
 * @author     Oliver <oliver@open-classifieds.com>
 * @copyright  (c) 2009-2015 Open Classifieds Team
 * @license    GPL v3
 */

class Algolia
{
    public static function reindex()
    {
        if (! (bool) Core::config('general.algolia_search')
            OR (Core::config('general.algolia_search_application_id') == null OR mb_strlen(Core::config('general.algolia_search_application_id')) == 0)
            OR (Core::config('general.algolia_search_admin_key') == null OR mb_strlen(Core::config('general.algolia_search_admin_key')) == 0))
            return;

        $ads = self::get_ads();
        $categories = self::get_categories();
        $locations = self::get_locations();
        $users = self::get_users();

        require_once Kohana::find_file('vendor', 'algoliasearch-client-php/algoliasearch','php');
        $client = new \AlgoliaSearch\Client(Core::config('general.algolia_search_application_id'), Core::config('general.algolia_search_admin_key'));

        $index_ads = $client->initIndex('yclas_ads');
        $index_ads->addObjects($ads);
        $index_ads->setSettings(
            [
                'searchableAttributes' => [
                    'title',
                    'description',
                    'category',
                ],
                'customRanking' => [
                    'desc(objectID)'
                ],
            ]
        );

        $index_categories = $client->initIndex('yclas_categories');
        $index_categories->addObjects($categories);
        $index_categories->setSettings(
            [
                'searchableAttributes' => [
                    'name',
                    'description',
                ],
                'customRanking' => [
                    'desc(objectID)'
                ],
            ]
        );

        $index_locations = $client->initIndex('yclas_locations');
        $index_locations->addObjects($locations);
        $index_locations->setSettings(
            [
                'searchableAttributes' => [
                    'name',
                    'description',
                ],
                'customRanking' => [
                    'desc(objectID)'
                ],
            ]
        );

        $index_users = $client->initIndex('yclas_users');
        $index_users->addObjects($users);
        $index_users->setSettings(
            [
                'searchableAttributes' => [
                    'name',
                ],
                'customRanking' => [
                    'desc(objectID)'
                ],
            ]
        );

        return;
    }

    protected static function add_permalink($items, $model)
    {
        foreach($items as $key => $item)
        {
            switch ($model) {
                case 'category':
                case 'location':
                    $permalink = Route::url('list', array($model => $item['seoname']));
                    break;
                case 'user':
                    $permalink = Route::url('profile', array('seoname' => $item['seoname']));
                    break;
            }

            $items[$key]['permalink'] = $permalink;
        }

        return $items;
    }

    protected static function get_ads()
    {
        $ads = new Model_Ad();
        $ads->where('status', '=', Model_Ad::STATUS_PUBLISHED);
        $ads = $ads->find_all();

        foreach ($ads as $key => $ad) {
            $index_ads[$key]['objectID'] = $ad->id_ad;
            $index_ads[$key]['title'] = $ad->title;
            $index_ads[$key]['description'] = $ad->description;
            $index_ads[$key]['category'] = $ad->category->name;
            $index_ads[$key]['location'] = $ad->location->name;
            $index_ads[$key]['permalink'] = Route::url('ad', array('controller'=>'ad','category' => $ad->category->seoname, 'seotitle' => $ad->seotitle));
        }

        return $index_ads;
    }

    protected static function get_categories()
    {
        $categories = DB::select(DB::expr('id_category objectID, name, seoname, description'))
            ->from('categories')
            ->execute()
            ->as_array();
        $categories = self::add_permalink($categories, 'category');

        return $categories;
    }

    protected static function get_locations()
    {
        $locations = DB::select(DB::expr('id_location objectID, name, seoname, description'))
            ->from('locations')
            ->execute()
            ->as_array();
        $locations = self::add_permalink($locations, 'location');

        return $locations;
    }

    protected static function get_users()
    {
       $users = DB::select(DB::expr('id_user objectID, name, seoname'))
            ->from('users')
            ->execute()
            ->as_array();
        $users = self::add_permalink($users, 'user');

        return $users;
    }
}

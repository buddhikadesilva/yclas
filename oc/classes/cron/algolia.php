<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cron for Algolia
 *
 * @author      Oliver <oliver@open-classifieds.com>
 * @package     Cron
 * @copyright   (c) 2009-2014 Open Classifieds Team
 * @license     GPL v3
 *
 */
class Cron_Algolia {

    /**
     * reindex algolia indexes
     * @return void
     */
    public static function reindex()
    {
        try {
            Algolia::reindex();
        } catch (Exception $e) {
            return FALSE;
        }

        return;
    }

}

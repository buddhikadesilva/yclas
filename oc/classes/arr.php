<?php defined('SYSPATH') or die('No direct script access.');
/**
 * arr
 *
 * @package    OC
 * @category   Helper
 * @author     Chema <chema@open-classifieds.com>
 * @copyright  (c) 2009-2015 Open Classifieds Team
 * @license    GPL v3
 */


class Arr extends Kohana_Arr {

    /**
     * Converts an array to XML
     *
     * @param array $array
     * @param SimpleXMLElement $xml
     * @param string $child_name
     *
     * @return SimpleXMLElement $xml
     */
    public static function to_xml($array, SimpleXMLElement $xml, $child_name)
    {
        foreach ($array as $k => $v) {
            if(is_array($v)) {
                (is_int($k)) ? self::to_xml($v, $xml->addChild($child_name), $v) : self::to_xml($v, $xml->addChild(strtolower($k)), $child_name);
            } else {
                (is_int($k)) ? $xml->addChild($child_name, $v) : $xml->addChild(strtolower($k), $v);
            }
        }

        return $xml->asXML();
    }

    /**
     * Re array the odd indexing of multiple file uploads from the format:
     *
     * $_FILES['field']['key']['index']
     *
     * to
     *
     * $_FILES['field']['index']['key']
     *
     * @param array $files_post
     *
     * @return array $files
     */
    public static function re_array_multiple_file_uploads($files_post) {
        $files = [];
        $files_count = count($files_post['name']);
        $files_keys = array_keys($files_post);

        for ($i = 0; $i < $files_count; $i++)
        {
            foreach ($files_keys as $key) {
                $files[$i][$key] = $files_post[$key][$i];
            }
        }

        return $files;
    }
}
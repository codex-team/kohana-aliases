<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Model_Aliases
{
    public $uri;
    public $hash;
    public $type;
    public $id;
    public $dt_create;
    public $deprecated;

    public function __construct() {}

    /**
     * Return a route name that does not use by input route
     *
     * @param string $route
     *
     * @return string $newRoute
     *
     * @throws Kohana_Exception
     */
    private static function generateAlias($route)
    {
        $alias = self::getAlias($route);

        $hashedRoute = self::createRawHash($route);

        /**
         * This alias is free to use. Return $route back
         */
        if (empty($alias) AND ! Model_Uri::isSystemAlias($route)) {
            return $route;
        }

        /**
         * If alias already exist in DB and it has been marked up as deprecated
         * then we delete that route from DB and return $route
         */
        if ( ! empty($alias) AND Arr::get($alias, 'deprecated')) {
            self::deleteAlias($hashedRoute);

            return $route;
        }

        /**
         * If alias is already exist and it is not deprecated then we going
         * to find first free alias with hyphen and number at the end.
         *
         * For 1 to infinity
         */
        for ($index = 1;; $index++) {
            $newAlias = $route . '-' . $index;
            $aliasExist = !! self::getAlias($newAlias) OR Model_Uri::isSystemAlias($newAlias);

            if (empty($aliasExist)) {
                return $newAlias;
                break;
            }
        }
    }

    /**
     * Returns Controller, Action and Id by URI <route>(/<action>)
     *
     * 1. Find alias in database by <route>
     * 2. Get Controller and Subcontroller
     * 3. Get Action
     *
     * @param string $route
     * @param string $action
     *
     * @return array {controller: string, action: string, id: integer}
     *
     * @throws Aliases_HTTP_Exception_404
     * @throws Aliases_Exception
     */
    public static function getRealRequestParams($route, $action = null)
    {
        /**
         * Find route row in database
         */
        $alias = self::getAlias($route);

        /**
         * Route was not found in database
         * Then throw Aliases_HTTP_Exception_404
         */
        if (empty($alias)) {
            throw new Aliases_HTTP_Exception_404('The requested URL ' . $route . ' was not found on this server.');
        }

        if (!Arr::get(Aliases_Controller::MAP, $alias['type'])) {
            throw new Aliases_Exception('Wrong target type given');
        }

        /**
         * For <route>:
         *     Controller_<alias_target_type>_Index
         *
         * For <route>/<action>:
         *     Controller_<alias_target_type>_Modify->action_<action>
         */
        if ($action == null) {
            return array(
                'controller' => 'Controller_'
                                . Aliases_Controller::MAP[$alias['type']]
                                . '_'
                                . Aliases_Subcontroller::MAP[Aliases_Subcontroller::INDEX],
                'action'     => 'action_show',
                'id'         => $alias['id']
            );
        } else {
            return array(
                'controller' => 'Controller_'
                                . Aliases_Controller::MAP[$alias['type']]
                                . '_'
                                . Aliases_Subcontroller::MAP[Aliases_Subcontroller::MODIFY],
                'action'     => 'action_' . $action,
                'id'         => $alias['id']
            );
        }
    }

    /**
     * Add new alias
     *
     * @param string $alias         Alias
     * @param integer $type         Substance type
     * @param integer $id           Substance id
     *
     * @return string
     *
     * @throws Kohana_Exception
     */
    public static function addAlias($alias, $type, $id)
    {
        if ( ! empty($alias)) {
            $newAlias = self::generateAlias($alias);
            $dt_create = DATE::$timezone;
            $hash = self::createRawHash($newAlias);
            $alias = Model_DB_Aliases::insert($newAlias, $hash, $type, $id, $dt_create);

            if ($alias) {
                return $newAlias;
            }
        }

        return '';
    }

    /**
     * @param string $route
     *
     * @return object
     */
    public static function getAlias($route)
    {
        $hashedRoute = self::createRawHash($route);

        return Model_DB_Aliases::select($hashedRoute);
    }

    /**
     * Updates Alias and sets the old one as deprecated
     *
     * @param string $oldAlias  old alias
     * @param string $alias     new alias
     * @param integer $type     substance type
     * @param integer $id       substance id
     *
     * @return string
     *
     * @throws Kohana_Exception
     */
    public static function updateAlias($oldAlias, $alias, $type, $id)
    {
        $hashedOldRoute = self::createRawHash($oldAlias);

        /**
         * Set current Alias as deprecated
         */
        Model_DB_Aliases::update($hashedOldRoute);

        /**
         * Add a new one
         */
        $addedAlias = self::addAlias($alias, $type, $id);

        return $addedAlias;
    }

    /**
     * Delete Alias by it's hash
     *
     * @param string $hash
     *
     * @return object
     */
    public static function deleteAlias($hash)
    {
        return Model_DB_Aliases::delete($hash);
    }

    /**
     * Return route hashed to raw binary string
     *
     * @param $route
     *
     * @return string
     */
    private static function createRawHash($route)
    {
        return md5($route, true);
    }

    /**
     * Generate URI for target title
     * Return string of low-cased latin letters and digits with hyphens
     * instead of spaces and other not known symbols
     *
     * @param string $title
     *
     * @return string
     */
    public static function generateUri($title)
    {
        /** transliterate russian letters */
        $convertedString = self::rus2translit($title);

        /** replace all non-latin symbols to hyphen */
        $convertedString = preg_replace("/[^0-9a-zA-Z]/", "-", $convertedString);

        /** replace several hyphen to one */
        $convertedString = preg_replace('/-{2,}/', '-', $convertedString);

        /** trim hyphen from borders */
        $convertedString = trim($convertedString, '-');

        /** lowercase string */
        $convertedString = strtolower($convertedString);

        return $convertedString;
    }

    /**
     * Transliterate string from russian
     *
     * @param string $string
     *
     * @return string $convertedString
     */
    private static function rus2translit($string)
    {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => "",    'ы' => 'y',   'ъ' => "",
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => "",    'Ы' => 'Y',   'Ъ' => "",
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );

        $convertedString = strtr($string, $converter);

        return $convertedString;
    }
}

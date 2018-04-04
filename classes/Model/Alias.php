<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Alias System https://github.com/codex-team/kohana-aliases
 *
 * @author CodeX Team <team@ifmo.su>
 * @author Khaydarov Murod
 * @license MIT
 */
class Model_Alias
{
    public $uri;
    public $hash_raw;
    public $hash;
    public $target_type;
    public $target_id;
    public $dt_create;
    public $deprecated;

    public function __construct($uri = null, $target_type = null, $target_id = null, $dt_create = null, $deprecated = 0)
    {
        $this->uri          = $uri;
        $this->hash         = md5($this->uri);
        $this->hash_raw     = md5($this->uri, true);
        $this->target_type  = $target_type;
        $this->target_id    = $target_id;
        $this->dt_create    = $dt_create;
        $this->deprecated   = $deprecated;
    }

    private function save()
    {
        return Dao_Alias::insert()
            ->set('uri', $this->uri)
            ->set('hash', $this->hash_raw)
            ->set('target_type', $this->target_type)
            ->set('target_id', $this->target_id)
            ->set('deprecated', $this->deprecated)
            ->clearcache('hash:'. $this->hash)
            ->execute();
    }

    /**
     * Return a route name that does not use by input route
     *
     * @param string $route
     * @return string $newRoute
     */
    private static function generateAlias($route)
    {
        $alias = self::getAlias($route);

        $hashedRoute = md5($route, true);

        /**
         * Setting $newAlias [String] = $route as default until we looking for new unengaged alias.
         */
        $newAlias = $route;

        if (isset($alias) && Arr::get($alias, 'deprecated')) {

            self::deleteAlias($hashedRoute);
            return $newAlias;

        } elseif (!empty($alias) && !Arr::get($alias, 'deprecated')) {

            for($index = 1; ; $index++)
            {
                $newAlias = $newAlias.'-'.$index;
                $aliasExist = !!self::getAlias($newAlias) || Model_Uri::isSystemAlias($newAlias);

                if (empty($aliasExist)) {

                    return $newAlias;
                    break;

                } else {

                    $newAlias = $route;

                }
            }

        } elseif (!empty($alias)) {

            $newAlias = $route;

        }

        return $newAlias;
    }

    /**
     * Returns Controller, Action and Id by alias
     *
     * @param $route [String] - alias from uri
     * @param $sub_action [String] = null - default value
     *
     * @return array with contorller , action and id
     *
     * @throws HTTP_Exception_404
     */
    public function getRealRequestParams($route, $sub_action = null)
    {
        $model_uri = Model_Uri::Instance();

        $alias = self::getAlias($route);

        if (empty($alias)) {
            throw new HTTP_Exception_404('The requested URL '.$route.' was not found on this server.');
        }

        if (empty(Model_Uri::CONTROLLERS_MAP)) {
            throw new Exception('Controllers map is empty');
        }

        if (!Arr::get(Model_Uri::CONTROLLERS_MAP, $alias['target_type'])) {
            throw new Exception('Wrong target type given');
        }

        if ($sub_action == null)
            return array(
                'controller' => 'Controller_' . Model_Uri::CONTROLLERS_MAP[$alias['target_type']] . '_' . Model_Uri::ACTIONS_MAP[$model_uri::INDEX],
                'action'     => 'action_show',
                'id'         => $alias['target_id']
            );
        else
            return array(
                'controller' => 'Controller_' . Model_Uri::CONTROLLERS_MAP[$alias['target_type']] . '_' . Model_Uri::ACTIONS_MAP[$model_uri::MODIFY],
                'action'     => 'action_' . $sub_action,
                'id'         => $alias['target_id']
            );
    }

    /**
     * Add new alias
     *
     * @param $alias [String] Alias
     * @param $target_type [int] - substance type
     * @param $target_id [int] - substance id
     * @param $deprecated [int] - is alias deprecated
     */

    public static function addAlias($alias, $target_type, $target_id, $deprecated = 0)
    {
        if (!empty($alias)) {
            $newAlias = self::generateAlias($alias);
            $dt_create = DATE::$timezone;
            $model_alias = new Model_Alias($newAlias, $target_type, $target_id, $dt_create, $deprecated);
            $model_alias->save();
        }

        return isset($model_alias->uri) ? $model_alias->uri : '';
    }

    public static function getAlias($route = null)
    {
        $hashedRoute    = md5($route);
        $hashedRouteRaw = md5($route, true);

        $alias = Dao_Alias::select()
            ->where('hash', '=', $hashedRouteRaw)
            ->limit(1)
            ->cached(Date::HOUR, 'hash:'.$hashedRoute)
            ->execute();

        return $alias;
    }

    /**
     * Updates Alias and sets Old one deprecated = 1
     * $alias [String] - new uri, $type [Int] - substance type (Model_Uri - $controllersMap), $id [Int]
     */
    public static function updateAlias($oldAlias = null, $alias, $target_type, $target_id)
    {
        $hashedOldRoute = md5($oldAlias, true);

        $update = Dao_Alias::update()
            ->set('deprecated', 1)
            ->where('hash', '=', $hashedOldRouteRaw)
            ->clearcache('hash:'.$hashedOldRoute)
            ->execute();

        return self::addAlias($alias, $target_type, $target_id);
    }

    public static function deleteAlias($hash_raw)
    {
        $delete = Dao_Alias::delete()
            ->where('hash', '=', $hash_raw)
            ->clearcache('hash:'.$hash)
            ->execute();

        return $delete;
    }

    public static function generateUri($string)
    {
        // transliterator
        $converted_string = self::rus2translit($string);

        // replace all other symbols to hyphen
        $converted_string = preg_replace("/[^0-9a-zA-Z]/", "-", $converted_string);

        // replace several hyphen to one
        $converted_string = preg_replace('/-{2,}/', '-', $converted_string);

        // trim hyphen from borders
        $converted_string = trim($converted_string, '-');

        return strtolower($converted_string);
    }

    /**
     * @param string $string
     * @return string $converted_string
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

        $converted_string = strtr($string, $converter);

        return $converted_string;
    }

}

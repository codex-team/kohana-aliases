<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Model_DB_Aliases
{
    public static $tableName = 'Aliases';

    /**
     * Insert a new one alias to database
     *
     * @param string  $uri
     * @param string  $hash         binary hash for route
     * @param integer $type
     * @param integer $id
     * @param integer $dt_create
     * @param int     $deprecated
     *
     * @return object
     */
    public static function insert($uri, $hash, $type, $id, $dt_create, $deprecated = 0)
    {
        $result = DB::insert(self::$tableName, array(
            'uri',
            'hash',
            'type',
            'id',
            'dt_create',
            'deprecated',
        ))->values(array(
            $uri,
            $hash,
            $type,
            $id,
            $dt_create,
            $deprecated
        ))->execute();

        return $result;
    }

    /**
     * Find route in database
     *
     * @param string $hash binary hash for route
     *
     * @return object
     */
    public static function select($hash)
    {
        $alias = DB::select()->from(self::$tableName)
                   ->where('hash', '=', $hash)
                   ->limit(1)
                   ->execute();

        return $alias->current();
    }

    /**
     * Set route as deprecated
     *
     * @param string $hash binary hash for route
     *
     * @return object
     */
    public static function update($hash)
    {
        $data = array(
            'deprecated' => '1',
        );

        $update = DB::update(self::$tableName)
                    ->set($data)
                    ->where('hash', '=', $hash)
                    ->execute();

        return $update;
    }

    /**
     * Delete route from database
     *
     * @param string $hash binary hash for route
     *
     * @return object
     */
    public static function delete($hash)
    {
        $result = DB::delete(self::$tableName)
                    ->where('hash', '=', $hash)
                    ->execute();

        return $result;
    }
}


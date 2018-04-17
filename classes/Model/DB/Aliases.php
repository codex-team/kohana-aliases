<?php defined('SYSPATH') or die('No direct script access.');

class Model_DB_Aliases extends Kohana_Model_DB_Aliases
{
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
        return parent::insert($uri, $hash, $type, $id, $dt_create, $deprecated = 0);
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
        return parent::select($hash);
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
        return parent::update($hash);
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
        return parent::delete($hash);
    }
}


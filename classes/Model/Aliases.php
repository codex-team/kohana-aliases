<?php defined('SYSPATH') or die('No direct script access.');

class Model_Aliases extends Kohana_Model_Aliases
{
    /**
     * Insert a new one alias to database
     *
     * @param string  $uri
     * @param string  $hash
     * @param integer $type
     * @param integer $id
     * @param integer $dt_create
     * @param int     $deprecated
     *
     * @return object
     */
    public function insert($uri, $hash, $type, $id, $dt_create, $deprecated = 0)
    {
        return parent::insert($uri, $hash, $type, $id, $dt_create, $deprecated = 0);
    }

    /**
     * Find route in database
     *
     * @param string $hash
     *
     * @return object
     */
    public function select($hash)
    {
        return parent::select($hash);
    }

    /**
     * Set route as deprecated
     *
     * @param string $hash
     *
     * @return object
     */
    public function update($hash)
    {
        return parent::update($hash);
    }

    /**
     * Delete route from database
     *
     * @param string $hash
     *
     * @return object
     */
    public function delete($hash)
    {
        return parent::delete($hash);
    }
}

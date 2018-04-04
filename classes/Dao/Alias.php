<?php defined('SYSPATH') or die('No direct script access.');

class Dao_Alias extends Dao_MySQL_Base
{
    protected $cache_key    = 'Dao_Aliases';
    protected $table        = 'Aliases';
}

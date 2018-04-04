<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Alias System https://github.com/codex-team/kohana-aliases
 *
 * @author CodeX Team <team@ifmo.su>
 * @author Khaydarov Murod
 */
class Model_Kohana_Uri
{
    private static $_instance;

    /**
     * Actions
     */
    const INDEX   = 1;
    const MODIFY  = 2;

    const ACTIONS_MAP  = array(
        self::INDEX     => 'Index',
        self::MODIFY    => 'Modify',
    );

    /**
     * Class Methods
     */
    private function __construct() {}
    private function __clone() {}
    private function __sleep() {}
    private function __wakeup() {}

    public static function Instance()
    {
        if (self::$_instance == null)
            self::$_instance = new self();

        return self::$_instance;
    }

    public static function isSystemAlias($alias)
    {
        $system_aliases = Kohana::$config->load('system_aliases')['system'];

        return in_array($alias, $system_aliases) ? 1 : 0;
    }
}

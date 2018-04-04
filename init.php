<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Alias System https://github.com/codex-team/kohana-aliases
 *
 * @author CodeX Team <team@ifmo.su>
 * @author Khaydarov Murod
 */

$STRING = '[0-9a-zA-Z]*';

Route::prepend('URI', '<route>(/<subaction>)', array(
        'route' => $STRING,
    ))
    ->filter(
        function (Route $route, $params, Request $request)
        {
            $alias = $params['route'];

            if (Model_Uri::isSystemAlias($alias)) {
                return false;
            }
        }
    )->defaults(array(
        'controller' => 'Uri',
        'action' => 'get',
    ));

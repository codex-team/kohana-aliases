<?php defined('SYSPATH') or die('No direct script access.');

return array(
    /**
     * List of uri which could not be used as aliases
     * They are processed via defined routes
     */
    'system' => array(
        /**
         * Lock root uri as a system
         */
        '',

        /**
         * Do not process the following uries with the Alias system
         */
//        'admin',
//        'login',
//        'logout',
    )
);

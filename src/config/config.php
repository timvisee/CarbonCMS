<?php

/*
 * Carbon CMS configuration file
 *
 * IMPORTANT NOTES:
 * - Any changes in this configuration file may break the site!
 * - Do never share this file with others, this file contains sensitive information!
 * - Do never remove this file!
 */

return Array(

    // TODO: Rename site_url -> site_domain
    // TODO: More 'general site' configuration options
    // TODO: Add site configuration to to the Carbon CMS registery (possibly optionally!)

    'general' => Array(
        'site_url'          => 'http://localhost/app/carbon_cms/src/',
        'site_path'         => '/app/carbon_cms/src'
    ),

    'database' => Array(
        'host'              => '127.0.0.1',
        'port'              => 3306,
        'database'          => 'carbon_cms',
        'username'          => 'root',
        'password'          => 'password',
        'table_prefix'      => 'carbon_'
    ),

    'hash' => Array(
        'hash_algorithm'    => 'sha256',
        'hash_key'          => '7cc8b7833dba4e03dd6d1441aa25262fabe29862b494aff2168f1a6b35f1f406'
    ),

    'carbon' => Array(
        'debug' => true
    )

);
<?php declare(strict_types=1);
/*
 * Ultrafunk plugin PHP configuration
 *
 */


namespace Ultrafunk\Plugin\Config;


/**************************************************************************************************************************/



// Automatically updated on 'npm run build-dev' or 'npm run build-prod'
const IS_PROD_BUILD = true;
const IS_DEBUG      = false;



/**************************************************************************************************************************/


const VERSION = '1.45.13';

const PLUGIN_ENV = [
  'site_url'            => IS_PROD_BUILD ? 'https://ultrafunk.com'               : 'https://wordpress.ultrafunk.com',
  'handler_file_path'   => IS_PROD_BUILD ? 'inc/request/handlers/'               : 'inc/request/handlers/',
  'handler_class_path'  => IS_PROD_BUILD ? '\Ultrafunk\Plugin\Request\Handler\\' : '\Ultrafunk\Plugin\Request\Handler\\',
  'template_file_path'  => IS_PROD_BUILD ? '/php/templates/'                     : '/php/templates/',
  'template_file'       => IS_PROD_BUILD ? 'content-list-player.php'             : 'content-list-player.php',
  'template_class_path' => IS_PROD_BUILD ? '\Ultrafunk\Theme\Templates\\'        : '\Ultrafunk\Theme\Templates\\',
  'template_class'      => IS_PROD_BUILD ? 'ListPlayer'                          : 'ListPlayer',
];

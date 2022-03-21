<?php declare(strict_types=1);
/*
Plugin Name:  Ultrafunk
Plugin URI:   https://github.com/ultrafunk/ultrafunk-plugin
Author:       Ultrafunk
Author URI:   https://ultrafunk.com
Description:  ultrafunk.com WordPress plugin
Version:      1.41.0
Tested up to: 5.9
Requires PHP: 8.0
License:      MIT License
License URI:  https://opensource.org/licenses/MIT
*/


namespace Ultrafunk\Plugin;


/**************************************************************************************************************************/


if (!\defined('ABSPATH')) exit;

define('ULTRAFUNK_PLUGIN_PATH',  plugin_dir_path(__FILE__));
define('ULTRAFUNK_THEME_ACTIVE', str_starts_with(get_option('template'), 'ultrafunk'));


/**************************************************************************************************************************/


// Check if the needed (companion) Ultrafunk theme is installed and active
if (ULTRAFUNK_THEME_ACTIVE === false)
{
  if (is_admin())
  {
    add_action('admin_notices', function()
    {
      ?>
      <div class="notice notice-warning is-dismissible">
        <p>The <b><a href="https://github.com/ultrafunk/ultrafunk-plugin/">Ultrafunk plugin</a></b> requires the
        <b><a href="https://github.com/ultrafunk/ultrafunk-theme/">Ultrafunk theme</a></b> to function!
        <a href="/wp-admin/themes.php">Show installed themes</a></p>
      </div>
      <?php
    });
  }
}
else
{
  require_once ULTRAFUNK_PLUGIN_PATH . 'inc/constants.php';
  require_once ULTRAFUNK_PLUGIN_PATH . 'inc/custom/post_types.php';
  require_once ULTRAFUNK_PLUGIN_PATH . 'inc/custom/taxonomies.php';
  require_once ULTRAFUNK_PLUGIN_PATH . 'inc/custom/meta.php';
  
  if (is_admin())
  {
    require_once ULTRAFUNK_PLUGIN_PATH . 'inc/admin/customize.php';
    require_once ULTRAFUNK_PLUGIN_PATH . 'inc/admin/tracks.php';
  }
  else
  {
    require_once ULTRAFUNK_PLUGIN_PATH . 'inc/shared.php';
    require_once ULTRAFUNK_PLUGIN_PATH . 'inc/globals.php';
    require_once ULTRAFUNK_PLUGIN_PATH . 'inc/request/default-routes.php';
    require_once ULTRAFUNK_PLUGIN_PATH . 'inc/request/route-request.php';
    require_once ULTRAFUNK_PLUGIN_PATH . 'inc/request/request-handler.php';
    require_once ULTRAFUNK_PLUGIN_PATH . 'inc/front/customize.php';
  }
}


/**************************************************************************************************************************/


//
// Activate the plugin
//
function activate() : void
{ 
  if (ULTRAFUNK_THEME_ACTIVE)
    \Ultrafunk\Plugin\PostTypes\register_custom(); 

  flush_rewrite_rules(); 
}
register_activation_hook(__FILE__, '\Ultrafunk\Plugin\activate');

//
// Deactivate the plugin
//
function deactivate() : void
{
  unregister_post_type('uf_track');
  flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, '\Ultrafunk\Plugin\deactivate');

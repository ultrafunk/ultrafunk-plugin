<?php declare(strict_types=1);
/*
 * Ultrafunk plugin admin settings
 *
 */


namespace Ultrafunk\Plugin\Admin\Settings;


use const Ultrafunk\Plugin\Config\PLUGIN_ENV;


/**************************************************************************************************************************/


function add_menu_item() : void
{
  add_menu_page(
    'Ultrafunk',
    'Ultrafunk',
    'manage_options',
    'uf_settings',
    '\Ultrafunk\Plugin\Admin\Settings\plugin_settings',
    'dashicons-admin-generic',
    999
  );
}
add_action('admin_menu', '\Ultrafunk\Plugin\Admin\Settings\add_menu_item');


/**************************************************************************************************************************/


function plugin_settings() : void
{
  if (current_user_can('delete_users') === false)
    wp_die('You do not have sufficient permissions to access this page.');

  $uf_settings = get_uf_settings();

  if (isset($_POST['uf-save-settings']) && is_valid_nonce('settings'))
    save_settings($uf_settings);

  if (isset($_POST['uf-save-top-artists']) && is_valid_nonce('top_artists'))
    $result = save_top_artists($uf_settings);

  if (isset($_POST['uf-update-page-cache-info']) && is_valid_nonce('update_page_cache_info'))
    update_page_cache_info(PLUGIN_ENV['page_cache_path']);

  if (isset($_POST['uf-delete-error-log']) && is_valid_nonce('error_log'))
    delete_error_log();

  \Ultrafunk\Plugin\Admin\Settings\settings_template($uf_settings, (isset($result) ? $result : null));
}


/**************************************************************************************************************************/


function save_settings(array &$uf_settings) : void
{
  $uf_settings['list_tracks_per_page']    = get_post_value('list_tracks_per_page');
  $uf_settings['gallery_tracks_per_page'] = get_post_value('gallery_tracks_per_page');

  update_option("uf_settings", $uf_settings);

  ?><div class="notice notice-success is-dismissible"><p>Settings updated</p></div><?php
}

function save_top_artists(array &$uf_settings) : array
{
  $uf_settings['channels_num_top_artists'] = get_post_value('channels_num_top_artists');
  $uf_settings['show_top_artists_log']     = get_post_is_checked('show_top_artists_log');

  update_option("uf_settings", $uf_settings);

  $set_top_artists_result = \Ultrafunk\Plugin\Admin\TopArtists\set_data(absint($uf_settings['channels_num_top_artists']), $uf_settings['show_top_artists_log'])
  ?><div class="notice notice-success is-dismissible"><p>Top Artists for all Channels created / updated in <?php echo esc_html($set_top_artists_result['time']); ?> seconds.</p></div><?php

  return $set_top_artists_result;
}

function update_page_cache_info(string $cache_path) : void
{
  $start_time = microtime(true);

  require ULTRAFUNK_PLUGIN_PATH . 'inc/admin/page-cache-info.php';

  $info_updated = \Ultrafunk\Plugin\Admin\PageCacheInfo\update_transient($cache_path);

  if ($info_updated)
  {
    ?><div class="notice notice-success is-dismissible"><p>Page cache info updated in <?php echo round((microtime(true) - $start_time), 3) . ' seconds.'; ?></p></div><?php
  }
  else
  {
    ?><div class="notice notice-error is-dismissible"><p>Unable to update page cache info!</p></div><?php
  }
}

function delete_error_log() : void
{
  wp_delete_file(ini_get('error_log'));
  ?><div class="notice notice-success is-dismissible"><p>PHP error log deleted</p></div><?php
}


/**************************************************************************************************************************/


function get_uf_settings() : array
{
  $settings        = \Ultrafunk\Plugin\Storage\DEFAULT_SETTINGS;
  $stored_settings = get_option("uf_settings");

  if (!empty($stored_settings))
  {
    foreach ($stored_settings as $key => $stored_setting)
    {
      if (isset($settings[$key]))
        $settings[$key] = $stored_setting;
    }
  }

  return $settings;
}

function get_post_value(string $key, int $default_value = -1) : int
{
  return (isset($_POST[$key]) ? intval($_POST[$key]) : $default_value);
}

function get_post_is_checked(string $key) : bool
{
  return (isset($_POST[$key]) ? true : false);
}

function is_valid_nonce(string $uid) : bool
{
  return (check_admin_referer("_uf_{$uid}_", "_uf_nonce_{$uid}_") === 1);
}

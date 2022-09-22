<?php declare(strict_types=1);
/*
 * Ultrafunk plugin admin settings
 *
 */


namespace Ultrafunk\Plugin\Admin\Settings;


/**************************************************************************************************************************/


function add_menu_item() : void
{
	add_options_page('Ultrafunk', 'Ultrafunk', 'manage_options', 'settings', '\Ultrafunk\Plugin\Admin\Settings\plugin_settings');
}
add_action('admin_menu', '\Ultrafunk\Plugin\Admin\Settings\add_menu_item');


/**************************************************************************************************************************/


function plugin_settings() : void
{
  if (current_user_can('delete_users') === false)
    wp_die('You do not have sufficient permissions to access this page.');

  $uf_settings = get_settings();

  if (isset($_POST['uf-save-general-settings']) && is_valid_nonce('general_settings'))
  {
    $uf_settings['list_tracks_per_page']    = get_post_value('list_tracks_per_page');
    $uf_settings['gallery_tracks_per_page'] = get_post_value('gallery_tracks_per_page');

    update_option("uf_settings", $uf_settings);

    ?><div class="updated"><p>General settings updated</p></div><?php
  }

	if (isset($_POST['uf-save-top-artists']) && is_valid_nonce('top_artists'))
  {
    $uf_settings['channel_num_top_artists'] = get_post_value('channel_num_top_artists');
    $uf_settings['show_top_artists_log']    = get_post_string('show_top_artists_log');

    update_option("uf_settings", $uf_settings);

    $result = \Ultrafunk\Plugin\Admin\TopArtists\set_data(absint($uf_settings['channel_num_top_artists']), ($uf_settings['show_top_artists_log'] === '1'))
    ?><div class="updated"><p>Top Artists for all Channels created / updated in <?php echo $result['time']; ?> seconds.</p></div><?php
  }

  \Ultrafunk\Plugin\Admin\Settings\settings_template($uf_settings, (isset($result) ? $result : null));
}


/**************************************************************************************************************************/


function get_settings() : array
{
	$settings        = \Ultrafunk\Plugin\Constants\DEFAULT_SETTINGS;
	$stored_settings = get_option("uf_settings");

  if (!empty($stored_settings))
  {
    foreach ($stored_settings as $key => $stored_setting)
      $settings[$key] = $stored_setting;
  }

  return $settings;
}

function get_post_value(string $key, int $default_value = -1) : int
{
  return (isset($_POST[$key]) ? intval($_POST[$key]) : $default_value);
}

function get_post_string(string $key, string $default_string = '') : string
{
  return (isset($_POST[$key]) ? sanitize_title($_POST[$key]) : $default_string);
}

function is_valid_nonce(string $uid) : bool
{
  return (check_admin_referer("_uf_{$uid}_", "_uf_nonce_{$uid}_") === 1);
}

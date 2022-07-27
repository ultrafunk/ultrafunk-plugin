<?php declare(strict_types=1);
/*
 * Ultrafunk plugin admin settings
 *
 */


namespace Ultrafunk\Plugin\Admin\Settings;


use function Ultrafunk\Plugin\Admin\TopArtists\set_channels_top_artists;


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

	if (isset($_POST['uf-update-settings']))
  {
    // Nonce check
    if (check_admin_referer('_uf_update_settings_', '_uf_nonce_'))
    {
      $uf_settings['channel_max_top_artists'] = get_post_value('channel_max_top_artists');
      $uf_settings['show_top_artists_log']    = get_post_value('show_top_artists_log');

      update_option("uf_settings", $uf_settings);

      $result = set_channels_top_artists(absint($uf_settings['channel_max_top_artists']), ($uf_settings['show_top_artists_log'] === '1'))

      ?>
      <div class="updated"><p>Top Artists for all Channels created / updated in <?php echo $result['time']; ?> seconds.</p></div>
      <?php
    }
  }

  ?>
  <div class="wrap">

  <h2>Ultrafunk Settings</h2>

  <form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
  <?php wp_nonce_field('_uf_update_settings_', '_uf_nonce_'); ?>

  <h3>Top Artists for <a href="/channels/">All Channels</a></h3>
  <p>Number of top artists to generate for each channel (min: 5 => max: 15).<br>The result is stored as a transient (uf_channels_top_artists) with no expiration.</p>
  <p><input type="number" name="channel_max_top_artists" min="5" max="15" value="<?php echo esc_attr($uf_settings['channel_max_top_artists']); ?>" /></p>
  <p><label><input type="checkbox" name="show_top_artists_log" value="1" <?php checked(1, $uf_settings['show_top_artists_log'], true); ?> />Show create / update log</label></p>

  <p><input type="submit" class="button button-primary" name="uf-update-settings" value="Update Top Artists for All Channels" /></p>
  </form>

  <?php echo isset($result['log']) ? '<br><hr><br><pre>' . $result['log'] . '</pre>' : ''; ?>

  </div>
  <?php
}


/**************************************************************************************************************************/


function get_settings() : array
{
	$settings = array(
    'channel_max_top_artists' => 10,
    'show_top_artists_log'    => '1',
	);

	$stored_settings = get_option("uf_settings");

  if (!empty($stored_settings))
  {
    foreach ($stored_settings as $key => $stored_setting)
      $settings[$key] = $stored_setting;
  }

  return $settings;
}

function get_post_value(string $post_key) : string
{
  return (isset($_POST[$post_key]) ? $_POST[$post_key] : '');
}

<?php declare(strict_types=1);
/*
 * Ultrafunk plugin admin settings template
 *
 */


namespace Ultrafunk\Plugin\Admin\Settings;


/**************************************************************************************************************************/


function settings_template(array $uf_settings, array $result = null) : void
{
  ?>
  <div class="wrap">

  <h2>General Ultrafunk Settings</h2>

  <form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
  <?php wp_nonce_field('_uf_general_settings_', '_uf_nonce_general_settings_'); ?>

  <table>
  <tr><td><h3>List Player</h3></td></tr>
  <tr>
  <td>Number of tracks to show on each page (min: 10, max: 50, default: 25):</td>
  <td><input type="number" name="list_tracks_per_page" min="10" max="50" value="<?php echo esc_attr($uf_settings['list_tracks_per_page']); ?>" /></td>
  </tr>
  <tr><td><h3>Gallery Player</h3></td></tr>
  <tr>
  <td>Number of tracks to show on each page (min: 4, max: 24, default: 12):</td>
  <td><input type="number" name="gallery_tracks_per_page" min="4" max="24" value="<?php echo esc_attr($uf_settings['gallery_tracks_per_page']); ?>" /></td>
  </tr>
  </table>

  <p><input type="submit" class="button button-primary" name="uf-save-general-settings" value="Save Settings" /></p>
  </form>

  <br>
  <h1>Other Ultrafunk Settings</h1>

  <form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
  <?php wp_nonce_field('_uf_top_artists_', '_uf_nonce_top_artists_'); ?>

  <h3>Top Artists for <a href="/channels/">All Channels</a></h3>
  <table>
  <tr>
  <td>Number of top artists to generate for each channel (min: 5, max: 15, default: 10).<br>The result is stored as a transient (uf_channels_top_artists) with no expiration.</td>
  <td><input type="number" name="channel_num_top_artists" min="5" max="15" value="<?php echo esc_attr($uf_settings['channel_num_top_artists']); ?>" /></td>
  </tr>
  </table>
  <p><label><input type="checkbox" name="show_top_artists_log" value="1" <?php checked(1, $uf_settings['show_top_artists_log'], true); ?> />Show create / update log</label></p>

  <p><input type="submit" class="button button-primary" name="uf-save-top-artists" value="Update Top Artists for All Channels" /></p>
  </form>

  <?php echo isset($result['log']) ? '<br><hr><br><pre>' . $result['log'] . '</pre>' : ''; ?>

  </div>
  <?php
}

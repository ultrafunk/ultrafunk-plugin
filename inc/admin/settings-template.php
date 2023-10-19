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

  <h2>Ultrafunk Settings</h2>

  <form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
  <?php wp_nonce_field('_uf_settings_', '_uf_nonce_settings_'); ?>

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

  <p><input type="submit" class="button button-primary" name="uf-save-settings" value="Save Settings" /></p>
  </form>

  <br>
  <form method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>">
  <?php wp_nonce_field('_uf_top_artists_', '_uf_nonce_top_artists_'); ?>

  <h3>Top Artists for <a href="/channels/">All Channels</a></h3>
  <table>
  <tr>
  <td>Number of top artists to generate for each channel (min: 5, max: 15, default: 10).<br>The result is stored as a transient (uf_channels_top_artists) with no expiration.</td>
  <td><input type="number" name="channels_num_top_artists" min="5" max="15" value="<?php echo esc_attr($uf_settings['channels_num_top_artists']); ?>" /></td>
  </tr>
  </table>
  <p><label><input type="checkbox" name="show_top_artists_log" value="1" <?php checked(1, $uf_settings['show_top_artists_log'], true); ?> />Show create / update log</label></p>

  <p><input type="submit" class="button button-primary" name="uf-save-top-artists" value="Update Top Artists for All Channels" /></p>
  </form>

  <?php
  if (isset($result['log']))
    echo '<br><hr><br><pre>' . $result['log'] . '</pre>';
  else
    display_php_error_log();
  ?>

  </div>
  <?php
}

function display_php_error_log()
{
  if (!empty(ini_get('error_log')) && file_exists(ini_get('error_log')))
  {
    ?>
    <br>
    <h3>PHP Error Log: <?php echo ini_get('error_log'); ?></h3>
    <textarea id="uf-plugin-php-error-log" readonly rows="40">
    <?php

    $logfile_content = '';

    if ($file_handle = fopen(ini_get('error_log'), 'r'))
    {
      while (!feof($file_handle))
        $logfile_content .= fread($file_handle, 1 * 1024 * 1024);
    }

    echo $logfile_content;

    ?></textarea><?php
  }
  else
  {
    ?><br><h3>PHP Error Log empty or not found: <?php ini_get('error_log'); ?></h3><?php
  }
}

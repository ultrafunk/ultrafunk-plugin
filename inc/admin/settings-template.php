<?php declare(strict_types=1);
/*
 * Ultrafunk plugin admin settings template
 *
 */


namespace Ultrafunk\Plugin\Admin\Settings;


use function Ultrafunk\Plugin\Shared\human_file_size;


/**************************************************************************************************************************/


function settings_template(array $uf_settings, array $result = null) : void
{
  $theme_version = ULTRAFUNK_THEME_ACTIVE
    ? \Ultrafunk\Theme\Config\VERSION
    : 'N/A (Theme not Activated or Installed)';

  $page_cache_stats = get_transient('uf_page_cache_stats');

  if ($page_cache_stats === false)
    $page_cache_stats = ['updated_at' => 0, 'total_bytes' => 0, 'total_files' => 0, 'total_dirs' => 0];

  ?>
  <div class="wrap">

  <h2>Ultrafunk Settings</h2>
  <b>Theme Version:</b> <?php echo $theme_version; ?> - <b>Plugin Version:</b> <?php echo \Ultrafunk\Plugin\Config\VERSION; ?>

  <form method="post" action="">
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
  <form method="post" action="">
  <?php wp_nonce_field('_uf_top_artists_', '_uf_nonce_top_artists_'); ?>

  <h3>Top Artists for <a href="/channels/">All Channels</a></h3>
  <table>
  <tr>
  <td>Number of top artists to generate for each channel (min: 5, max: 15, default: 10).<br>The result is stored as a transient (uf_channels_top_artists) with no expiration.</td>
  <td><input type="number" name="channels_num_top_artists" min="5" max="15" value="<?php echo esc_attr($uf_settings['channels_num_top_artists']); ?>" /></td>
  </tr>
  </table>
  <p><label><input type="checkbox" name="show_top_artists_log" <?php checked(true, $uf_settings['show_top_artists_log'], true); ?> />Show create / update log</label></p>

  <p><input type="submit" class="button button-primary" name="uf-save-top-artists" value="Update Top Artists for All Channels" /></p>
  </form>

  <br>
  <form method="post" action="">
  <?php wp_nonce_field('_uf_update_page_cache_stats_', '_uf_nonce_update_page_cache_stats_'); ?>
  <h3>Page Cache Statistics</h3>
  <p>
    <table>
      <tr><td><b>Last updated:</b></td><td>&nbsp;</td><td><?php echo gmdate('d-M-Y H:i:s', $page_cache_stats['updated_at']); ?> UTC</td></tr>
      <tr><td><b>Total Page Cache Size:</b></td><td>&nbsp;</td><td><?php echo esc_html(human_file_size($page_cache_stats['total_bytes'])); ?></td></tr>
      <tr><td><b>Number of Pages Cached:</b></td><td>&nbsp;</td><td><?php echo esc_html($page_cache_stats['total_files']); ?></td></tr>
    </table>
  </p>
  <p><input type="submit" class="button button-primary" name="uf-update-page-cache-stats" value="Update Page Cache Stats" /></p>
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
    <h3>PHP error log: <span style="font-weight: 400;"><?php echo esc_html(ini_get('error_log')); ?></span></h3>

    <form method="post" action="">
    <?php wp_nonce_field('_uf_error_log_', '_uf_nonce_error_log_'); ?>
    <p><input type="submit" class="button button-primary" name="uf-delete-error-log" value="Delete Error Log" /></p>
    </form>

    <textarea id="uf-plugin-php-error-log" readonly rows="35">
    <?php

    WP_Filesystem();
    global $wp_filesystem;

    $logfile_content = $wp_filesystem->get_contents(ini_get('error_log'));
    echo esc_html($logfile_content);

    ?></textarea><?php
  }
  else
  {
    ?><br><h3>PHP error log empty or not found: <span style="font-weight: 400;"><?php echo esc_html(ini_get('error_log')); ?></span></h3><?php
  }
}

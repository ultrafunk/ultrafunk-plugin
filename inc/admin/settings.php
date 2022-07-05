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

	if (isset($_POST['uf-update-settings']))
  {
    // Nonce check
    if (check_admin_referer('_uf_update_settings_', '_uf_nonce_'))
    {
      $result = set_channels_top_artists(true);

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
  <p><input type="submit" class="button button-primary" name="uf-update-settings" value="Update Top Artists for Channels" /></p>
  </form>
  <pre><?php echo isset($result) ? $result['log'] : ''; ?></pre>
  </div>
  <?php 
}


/**************************************************************************************************************************/


function set_channels_top_artists(bool $create_log = false) : array
{
  $start_time  = microtime(true);
  $interval    = $start_time;
  $top_artists = [];
  $log_entries = '';

  $tracks = get_posts([
    'post_type'        => 'uf_track',
    'posts_per_page'   => -1,
    'suppress_filters' => true,
  ]);

  if ($create_log)
  {
    $log_entries .= 'Get All Tracks..: ' . round((microtime(true) - $interval), 4) . ' seconds <br>';
    $log_entries .= 'All Tracks Count: ' . count($tracks) . '<br>';
    $interval     = microtime(true);
  }

  foreach($tracks as $track)
  {
    $artists  = get_object_term_cache($track->ID, 'uf_artist');
    $channels = get_object_term_cache($track->ID, 'uf_channel');

    foreach($channels as $channel)
    {
      foreach($artists as $artist)
      {
        $top_artists[$channel->term_id][$artist->term_id] = isset($top_artists[$channel->term_id][$artist->term_id])
          ? ($top_artists[$channel->term_id][$artist->term_id] + 1)
          : 1;
      }
    }
  }

  if ($create_log)
  {
    $log_entries .= 'Create List.....: ' . round((microtime(true) - $interval), 4) . ' seconds <br>';
    $interval     = microtime(true);
  }

  foreach($top_artists as &$artists)
  {
    arsort($artists, SORT_NUMERIC);
    $artists = \array_slice($artists, 0, 13, true);
  }

  if ($create_log)
  {
    $log_entries .= 'Sort + Trim list: ' . round((microtime(true) - $interval), 4) . ' seconds <br>';
  }

  set_transient('uf_channels_top_artists', $top_artists, 0);

  $end_time = microtime(true);

  if ($create_log)
  {
    $log_entries .= 'Channels Count..: ' . count($top_artists) . '<br>';
    $log_entries .= 'All tracks size.: ' . strlen(serialize($tracks)) . ' bytes<br>';
    $log_entries .= 'Transient size..: ' . strlen(serialize($top_artists)) . ' bytes<br>';
    $log_entries .= log_transient_data($top_artists);
  }

  return [ 'time' => round(($end_time - $start_time), 4), 'log' => $log_entries ];
}


/**************************************************************************************************************************/


function log_transient_data(array $top_artists) : string
{
  $log_html = '';
  
  foreach($top_artists as $channel => $artists)
  {
    $log_html .= '<br>' . get_term_by('id', $channel, 'uf_channel')->name . ' => ' . $channel . '<br>';
    
    foreach($artists as $artist => $count)
    {
      $log_html .= $count . ' => '. get_term_by('id', $artist, 'uf_artist')->name . '<br>';
    }
  }

  return $log_html;
}

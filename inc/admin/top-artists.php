<?php declare(strict_types=1);
/*
 * Set Top Artists transient for All Channels
 *
 */


namespace Ultrafunk\Plugin\Admin\TopArtists;


/**************************************************************************************************************************/


function set_data(int $max_entries = 10, bool $create_log = false) : array
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
    $log_entries .= 'Get All Tracks..: ' . round((microtime(true) - $interval), 3) . ' seconds <br>';
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
    $log_entries .= 'Create List.....: ' . round((microtime(true) - $interval), 3) . ' seconds <br>';
    $interval     = microtime(true);
  }

  foreach($top_artists as &$artists)
  {
    arsort($artists, SORT_NUMERIC);
    $artists = \array_slice($artists, 0, $max_entries, true);
  }

  if ($create_log)
  {
    $log_entries .= 'Sort + Trim list: ' . round((microtime(true) - $interval), 3) . ' seconds <br>';
  }

  set_transient('uf_channels_top_artists', $top_artists, 0);
  set_transient('uf_channels_top_artists_updated_at', time(), YEAR_IN_SECONDS);

  $end_time = microtime(true);

  if ($create_log)
  {
    $log_entries .= 'Channels Count..: ' . count($top_artists) . '<br>';
    $log_entries .= 'All tracks size.: ' . strlen(serialize($tracks)) . ' bytes<br>';
    $log_entries .= 'Transient size..: ' . strlen(serialize($top_artists)) . ' bytes<br>';
    $log_entries .= log_transient_data($top_artists);
  }

  return ['time' => round(($end_time - $start_time), 3), 'log' => $log_entries];
}

function log_transient_data(array $top_artists) : string
{
  $log_html = '';

  foreach($top_artists as $channel => $artists)
  {
    $log_html .= '<br><b style="text-transform: uppercase; font-size: 16px;">' . esc_html(get_term_by('id', $channel, 'uf_channel')->name) . '</b> (Channel ID: ' . esc_html($channel) . ')<br>';

    foreach($artists as $artist => $count)
    {
      $log_html .= esc_html(get_term_by('id', $artist, 'uf_artist')->name) . ' (' . esc_html($count) . ' tracks)<br>';
    }
  }

  return $log_html;
}

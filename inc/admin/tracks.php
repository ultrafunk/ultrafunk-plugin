<?php declare(strict_types=1);
/*
 * Track admin functions (On new, update & save)
 *
 */


namespace Ultrafunk\Plugin\Admin\Tracks;


use DateInterval;
use DateTime;

use Ultrafunk\Plugin\Shared\TRACK_TYPE;

use const Ultrafunk\Plugin\Shared\YOUTUBE_VIDEO_ID_REGEX;


/**************************************************************************************************************************/


//
// Automatically populate meta fields based on current edited $post object on save
//
function on_save_set_meta(int $post_id, object $post, bool $update) : void
{
  // Don't update on REST requests to avoid save_post_uf_track triggering twice using the Gutenberg editor...
  if (defined('REST_REQUEST') && REST_REQUEST)
    return;

  // Don't update meta fields on autosave...
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;

  // Don't update meta fields on creation or when trashed...
  if (($post->post_status === 'auto-draft') || ($post->post_status === 'trash'))
    return;

  $track_source_data  = get_track_source_data($post->post_content);
  $track_artist_title = preg_split('/\s{1,}[\x{002D}\x{00B7}\x{2013}]\s{1,}/u', $post->post_title, 2);

  if (($track_source_data  !== null)  &&
      ($track_artist_title !== false) &&
      (count($track_artist_title) === 2))
  {
    update_post_meta($post->ID, 'track_artist',      $track_artist_title[0]);
    update_post_meta($post->ID, 'track_title',       $track_artist_title[1]);
    update_post_meta($post->ID, 'track_source_type', $track_source_data[0]);
    update_post_meta($post->ID, 'track_source_data', $track_source_data[1]);
    update_post_meta($post->ID, 'track_duration',    ($track_source_data[0] === TRACK_TYPE::YOUTUBE)
                                                       ? get_youtube_duration($track_source_data[2])
                                                       : 0);

    $track_artist_slug = sanitize_title($track_artist_title[0]);
    $track_artist_term = get_term_by('slug', $track_artist_slug, 'uf_artist');

    if (($track_artist_term !== false) && ($track_artist_slug !== $post->track_artist_slug))
    {
      update_post_meta($post->ID, 'track_artist_id',   $track_artist_term->term_id);
      update_post_meta($post->ID, 'track_artist_slug', $track_artist_slug);

      set_admin_notice($post->ID, 'notice-success', "<b>\"$post->post_title\"</b> saved with <b>track_artist_id:</b> $track_artist_term->term_id and <b>track_artist_slug:</b> $track_artist_slug");
    }
    else
    {
      validate_id_and_slug($post);
    }
  }
  else
  {
    set_admin_notice($post->ID, 'notice-error', "Unable to set track metadata for <b>\"$post->post_title\"</b> with track_id: $post->ID");
  }
}
add_action('save_post_uf_track', '\Ultrafunk\Plugin\Admin\Tracks\on_save_set_meta', 10, 3);

//
// Parse and return track source data from the post content
//
function get_track_source_data(string $post_content) : ?array
{
  $youtube_id_prefixes = ['/watch?v=', '/embed/', 'youtu.be/'];

  foreach($youtube_id_prefixes as $find_string)
  {
    $find_pos = strripos($post_content, $find_string);

    if ($find_pos !== false)
    {
      $video_id_found = substr($post_content, ($find_pos + strlen($find_string)), 11);

      if (1 === preg_match(YOUTUBE_VIDEO_ID_REGEX, $video_id_found, $video_id_validated))
        return [TRACK_TYPE::YOUTUBE, "youtube.com/watch?v=$video_id_validated[0]", $video_id_validated[0]];
    }
  }

  $find_pos = stripos($post_content, 'soundcloud.com/');

  if ($find_pos !== false)
    return [TRACK_TYPE::SOUNDCLOUD, substr($post_content, $find_pos, (strpos($post_content, '"', $find_pos) - $find_pos)), ''];

  return null;
}

//
// Validate and match track_artist_id and track_artist_slug as best we can...
//
function validate_id_and_slug(object $post) : void
{
  if (empty($post->track_artist_id))
    update_post_meta($post->ID, 'track_artist_id', -1);

  if (empty($post->track_artist_slug))
    update_post_meta($post->ID, 'track_artist_slug', 'N/A');

  if (((int)$post->track_artist_id === -1) && ($post->track_artist_slug === 'N/A'))
  {
    set_admin_notice($post->ID, 'notice-error', "<b>\"$post->post_title\"</b> has no valid <b>track_artist_id</b> and <b>track_artist_slug</b>");
  }
  else
  {
    $track_artist_id_term   = get_term_by('id',   $post->track_artist_id,   'uf_artist');
    $track_artist_slug_term = get_term_by('slug', $post->track_artist_slug, 'uf_artist');

    if (($track_artist_id_term !== false) && ($track_artist_slug_term !== false))
    {
      if ($track_artist_id_term->term_id !== $track_artist_slug_term->term_id)
        set_admin_notice($post->ID, 'notice-error', "<b>track_artist_id:</b> $post->track_artist_id and <b>track_artist_slug:</b> $post->track_artist_slug does not match for track: <b>\"$post->post_title\"</b>");
    }
    else
    {
      if ($track_artist_id_term === false)
        set_admin_notice($post->ID, 'notice-error', "Invalid <b>track_artist_id:</b> $post->track_artist_id for track: <b>\"$post->post_title\"</b>");
      else if ($track_artist_slug_term === false)
        set_admin_notice($post->ID, 'notice-error', "Invalid <b>track_artist_slug:</b> $post->track_artist_slug for track: <b>\"$post->post_title\"</b>");
    }
  }
}

//
// Return YouTube video duration in seconds using the YouTube Data API:
// https://developers.google.com/youtube/v3
//
function get_youtube_duration(string $video_id) : int
{
  if (empty($video_id))
    return 0;

  $api_key = UF_YOUTUBE_DATA_API_KEY;
  $referer = \Ultrafunk\Plugin\Config\PLUGIN_ENV['site_url'];
  $args    = ['headers' => ['referer' => $referer]];

  $response = wp_remote_get("https://www.googleapis.com/youtube/v3/videos?id=$video_id&key=$api_key&part=contentDetails&fields=items(contentDetails(duration))", $args);

  if (is_wp_error($response) || !is_array($response))
    return 0;

  $video_data = json_decode(wp_remote_retrieve_body($response), true);

  if (!count($video_data['items']))
    return 0;

  $dateTime = new DateTime('@0');
  $dateTime->add(new DateInterval($video_data['items'][0]['contentDetails']['duration']));
  $duration_seconds = intval($dateTime->getTimestamp());

  return $duration_seconds;
}


/**************************************************************************************************************************/


const SET_META_NOTICE_TRANSIENT = 'on_save_set_meta_notice';

//
// Set transient notice information for admin notices
//
function set_admin_notice(int $post_id, string $type = 'notice-error', string $text = 'Unknown error!') : void
{
  set_transient(SET_META_NOTICE_TRANSIENT, ['post_id' => $post_id, 'type' => $type, 'text' => $text], (60 * 5));
}

//
// Show admin notice if transient is set and we are on the correct screen
//
function show_notice() : void
{
  $screen = get_current_screen();

  // Notices do not work on Gutenberg edit screens
  if (isset($screen) && ($screen->base === 'post') && ($screen->post_type === 'uf_track'))
    return;

  $notice_data = get_transient(SET_META_NOTICE_TRANSIENT);

  if ($notice_data !== false)
  {
    delete_transient(SET_META_NOTICE_TRANSIENT);

    ?>
    <div class="notice <?php echo esc_attr($notice_data['type']); ?> is-dismissible">
      <style>b { font-weight: 700; }</style>
      <p>
        <?php echo wp_kses_post($notice_data['text']); ?>
        <?php if ($notice_data['type'] !== 'notice-success') { ?>
          &nbsp;&nbsp;&nbsp;<a href="/wp-admin/post.php?post=<?php echo absint($notice_data['post_id']); ?>&action=edit"><b>EDIT TRACK</b></a>
        <?php } ?>
      </p>
    </div>
    <?php
  }
}
add_action('admin_notices', '\Ultrafunk\Plugin\Admin\Tracks\show_notice');

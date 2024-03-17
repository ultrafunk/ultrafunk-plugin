<?php declare(strict_types=1);
/*
 * Ultrafunk custom plugin filters + actions
 *
 */


namespace Ultrafunk\Plugin\Custom\FiltersActions;


use Ultrafunk\Plugin\Shared\ {
  PLAYER_TYPE,
  TRACK_TYPE,
};

use function Ultrafunk\Plugin\Globals\ {
  get_global,
  get_settings_value,
  is_custom_query,
  is_shuffle,
};


/**************************************************************************************************************************/


//
// Set custom post type(s) as default
//
function pre_get_posts(object $query) : void
{
  if (!is_admin() && $query->is_main_query())
  {
    if ($query->is_home() || $query->is_date())
      $query->set('post_type', ['uf_track']);

    if ($query->is_search() || is_shuffle(PLAYER_TYPE::GALLERY))
      $query->set('posts_per_page', get_global('gallery_per_page'));
    else if (is_page() === false)
      $query->set('posts_per_page', get_settings_value('gallery_tracks_per_page'));
  }
}
add_action('pre_get_posts', '\Ultrafunk\Plugin\Custom\FiltersActions\pre_get_posts');

//
// Show custom post type(s) for archive pages
//
function getarchives_where(string $where) : string
{
  $where = str_replace("post_type = 'post'", "post_type IN ('uf_track')", $where);
  return $where;
}
add_filter('getarchives_where', '\Ultrafunk\Plugin\Custom\FiltersActions\getarchives_where');

//
// Return better term search results by replacing special chars in the query string
//
function parse_query(object $query) : void
{
  $modify_query = $query->is_main_query() || is_custom_query();

  if (!is_admin() && $modify_query && $query->is_search())
  {
    $new_query_string = str_replace("'", " ", $query->query['s']);                  // Replace ' with space for better term search results...
    $new_query_string = preg_replace('/(?<! )&(?! )/', '&amp;', $new_query_string); // Encode & without any spaces as &amp; for better term search results...

    if ($new_query_string !== $query->query['s'])
      $query->set('s', $new_query_string);
  }
}
add_action('parse_query', '\Ultrafunk\Plugin\Custom\FiltersActions\parse_query');

//
// Filter out all list-player search results that are not tracks (uf_track)
//
function posts_results(array $posts, object $query) : array
{
  $filter_results = (is_custom_query() || (defined('REST_REQUEST') && REST_REQUEST));

  if (!is_admin() && $filter_results && $query->is_search())
  {
    // array_values(array_filter($posts)) to "repack" filter result array (= index starts at 0...)
    return array_values(array_filter($posts, function($entry)
    {
      return ($entry->post_type === 'uf_track');
    }));
  }

  return $posts;
}
add_filter('posts_results', '\Ultrafunk\Plugin\Custom\FiltersActions\posts_results', 10, 2);

//
// Add uniqid and other custom options for SoundCloud and YouTube iframe embeds
//
function youtube_iframe_set_args(string &$iframe_tag) : void
{
  $iframe_tag = str_ireplace('<iframe', sprintf('<iframe id="youtube-%s"', uniqid()), $iframe_tag);
  $iframe_tag = str_ireplace('feature=oembed', sprintf('feature=oembed&enablejsapi=1&disablekb=1&origin=%s', \Ultrafunk\Plugin\Config\PLUGIN_ENV['site_url']), $iframe_tag);
}

function soundcloud_iframe_set_args(string &$iframe_tag) : void
{
  $iframe_tag = str_ireplace('<iframe', sprintf('<iframe id="soundcloud-%s" allow="autoplay"', uniqid()), $iframe_tag);
  $iframe_tag = str_ireplace('visual=true', 'visual=true&single_active=false', $iframe_tag);
}

function embed_oembed_html(string $cache, string $url, array $attr, int $post_id) : string
{
  $track_type = intval(get_post_meta($post_id, 'track_source_type', true));

  if ($track_type === TRACK_TYPE::YOUTUBE)
    youtube_iframe_set_args($cache);
  else if ($track_type === TRACK_TYPE::SOUNDCLOUD)
    soundcloud_iframe_set_args($cache);

  return $cache;
}
add_filter('embed_oembed_html', '\Ultrafunk\Plugin\Custom\FiltersActions\embed_oembed_html', 10, 4);

//
// Try to create a functioning iframe embed from a stale oEmbed Cache entry and log info for further inspection
//
function embed_maybe_make_link(string $output, string $url) : string
{
  global $wp_query;
  $post_meta    = get_post_meta($wp_query->post->ID);
  $track_type   = intval($post_meta['track_source_type'][0]);
  $embed_domain = ($track_type === TRACK_TYPE::YOUTUBE) ? 'youtube.com' : 'soundcloud.com';

  foreach($post_meta as $key => $value)
  {
    if (str_starts_with($key, '_oembed_'))
    {
      if (stripos($value[0], $embed_domain) !== false)
      {
        $output = $value[0];

        if ($track_type === TRACK_TYPE::YOUTUBE)
          youtube_iframe_set_args($output);
        else if ($track_type === TRACK_TYPE::SOUNDCLOUD)
          soundcloud_iframe_set_args($output);

        error_log("oEmbed Cache ({$embed_domain}) - TrackID: {$wp_query->post->ID} => {$wp_query->post->post_title}", 0);
      }
    }
  }

  return $output;
}
add_filter('embed_maybe_make_link', '\Ultrafunk\Plugin\Custom\FiltersActions\embed_maybe_make_link', 10, 2);

//
// Add noindex + nofollow meta tags to all 404 and shuffle pages
//
function wp_robots(array $robots) : array
{
  if (is_404() || is_shuffle())
  {
    $robots['noindex']  = true;
    $robots['nofollow'] = true;
  }

  return $robots;
}
add_filter('wp_robots', '\Ultrafunk\Plugin\Custom\FiltersActions\wp_robots');

//
// Disable iframe lazy loading
//
function wp_lazy_loading_enabled(bool $default, string $tag_name, string $context) : bool
{
  if ('iframe' === $tag_name)
    return false;

  return $default;
}
add_filter('wp_lazy_loading_enabled', '\Ultrafunk\Plugin\Custom\FiltersActions\wp_lazy_loading_enabled', 10, 3);

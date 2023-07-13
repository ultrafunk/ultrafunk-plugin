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
// Enhance search results by replacing special chars in query string
// This should be done by default in WordPress?
//
function parse_query(object $query) : void
{
  $modify_query = $query->is_main_query() || is_custom_query();

  if (!is_admin() && $modify_query && $query->is_search())
  {
    // https://www.w3.org/wiki/Common_HTML_entities_used_for_typography
    $search  = ['&ndash;', '&mdash;', '&lsquo;', '&rsquo;', '&prime;', '&Prime;', '&ldquo;', '&rdquo;', '&quot;'];
    $replace = ['-'      , '-'      , "'"      , "'"      , "'"      , '"'      , '"'      , '"'      , '"'     ];

    $new_query_string = htmlentities($query->query['s']);
    $new_query_string = str_replace($search, $replace, $new_query_string);
    $new_query_string = html_entity_decode($new_query_string);

    //Search string "R&B" needs special handling to match "R&amp;B"
    $new_query_string = str_ireplace('r&b', 'r&amp;b', $new_query_string);

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
function embed_oembed_html(string $cache, string $url, array $attr, int $post_id) : string
{
  $track_type = intval(get_post_meta($post_id, 'track_source_type', true));

  if ($track_type === TRACK_TYPE::YOUTUBE)
  {
    $cache = str_ireplace('<iframe', sprintf('<iframe id="youtube-uid-%s"', uniqid()), $cache);
    $cache = str_ireplace('feature=oembed', sprintf('feature=oembed&enablejsapi=1&disablekb=1&origin=%s', \Ultrafunk\Plugin\Config\PLUGIN_ENV['site_url']), $cache);
  }
  else if ($track_type === TRACK_TYPE::SOUNDCLOUD)
  {
    $cache = str_ireplace('<iframe', sprintf('<iframe id="soundcloud-uid-%s" allow="autoplay"', uniqid()), $cache);
    $cache = str_ireplace('visual=true', 'visual=true&single_active=false', $cache);
  }

  return $cache;
}
add_filter('embed_oembed_html', '\Ultrafunk\Plugin\Custom\FiltersActions\embed_oembed_html', 10, 4);

//
// Add noindex meta tag to all 404 and shuffle pages
//
function wp_robots(array $robots) : array
{
  if (is_404() || is_shuffle(PLAYER_TYPE::GALLERY) || is_shuffle(PLAYER_TYPE::LIST))
    $robots['noindex'] = true;

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

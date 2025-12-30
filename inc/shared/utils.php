<?php declare(strict_types=1);
/*
 * Shared plugin + theme utility functions
 *
 */


namespace Ultrafunk\Plugin\Shared\Utils;


/**************************************************************************************************************************/
if (!defined('ABSPATH')) exit;
/**************************************************************************************************************************/


use Ultrafunk\Plugin\Shared\Constants\COOKIE_KEY;


/**************************************************************************************************************************/


//
// Output debug info to the browser console, this will not always work and may cause strange side effects!
//
function console_log(mixed $output) : void
{
  if (\Ultrafunk\Plugin\Config\IS_DEBUG)
    echo '<script>console.log(' . wp_json_encode($output, JSON_HEX_TAG) . ');</script>';
}


/**************************************************************************************************************************/


//
// Return HTML links: <a href=""></a> for an array of WP_Terms
//
function get_term_links(array $terms, string $path, string $separator = '',  int $primary_id = -1) : string
{
  $term_links = [];

  foreach ($terms as $term)
  {
    $class = (($primary_id !== -1) && ($term->term_id === $primary_id)) ? 'primary' : 'secondary';
    $term_links[] = "<a class=\"$class\" href=\"$path$term->slug/\">$term->name</a>";
  }

  return implode($separator, $term_links);
}


/**************************************************************************************************************************/


//
// Get named cookie value if it exists with range check and default value
//
function get_cookie_value(string $cookie_name, int $min_val, int $max_val, int $default_val) : int
{
  if (isset($_COOKIE[$cookie_name]))
  {
    $cookie_val = intval($_COOKIE[$cookie_name]);

    if (($cookie_val >= $min_val) && ($cookie_val <= $max_val))
      return $cookie_val;
  }

  return $default_val;
}

//
// Get UID cookie for random shuffle transient name
//
function get_shuffle_transient_name() : string
{
  if (isset($_COOKIE[COOKIE_KEY::UF_SHUFFLE_UID]))
  {
    $cookie = sanitize_user(wp_unslash($_COOKIE[COOKIE_KEY::UF_SHUFFLE_UID]), true);

    if (strlen($cookie) < 50)
      return sprintf('uf_random_shuffle_%s', $cookie);
  }

  return '';
}

//
//
//
function get_channels_top_artists_info() : array
{
  $channels_top_artists_info = get_transient('uf_channels_top_artists_info');

  if ($channels_top_artists_info === false)
    return [ 'updated_at' => 0, 'all_tracks_count' => wp_count_posts('uf_track')->publish ];

  return $channels_top_artists_info;
}

/*
//
// Get named cookie json data if it exists
//
function get_cookie_json(string $cookie_name) : ?object
{
  if (isset($_COOKIE[$cookie_name]))
  {
    $cookie_data = json_decode(stripslashes($_COOKIE[$cookie_name]));

    if ($cookie_data !== null)
      return $cookie_data;
  }

  return null;
}
*/

<?php declare(strict_types=1);
/*
 * Storage functions
 *
 */


namespace Ultrafunk\Plugin\Storage;


/**************************************************************************************************************************/


abstract class COOKIE_KEY
{
//const UF_USER_SETTINGS    = 'uf_user_settings';
  const UF_GALLERY_PER_PAGE = 'uf_gallery_per_page';
  const UF_LIST_PER_PAGE    = 'uf_list_per_page';
  const UF_PREFERRED_PLAYER = 'uf_preferred_player';
  const UF_SHUFFLE_UID      = 'uf_shuffle_uid';
  const UF_RESHUFFLE        = 'uf_reshuffle';
}

const DEFAULT_SETTINGS = [
  'list_tracks_per_page'     => 25,
  'gallery_tracks_per_page'  => 12,
  'channels_num_top_artists' => 10,
  'show_top_artists_log'     => '1',
];


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
      return sprintf('random_shuffle_%s', $cookie);
  }

  return '';
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

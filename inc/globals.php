<?php declare(strict_types=1);
/*
 * Ultrafunk plugin globals class (singleton) and related functions
 *
 */


namespace Ultrafunk\Plugin\Globals;


use Ultrafunk\Plugin\Shared\PLAYER_TYPE;

use Ultrafunk\Plugin\Storage\COOKIE_KEY;

use function Ultrafunk\Plugin\Storage\get_cookie_value;


/**************************************************************************************************************************/


// Call Globals class constructor manually since it is "static"
Globals::construct();


/**************************************************************************************************************************/


class Globals
{
  // Each prop has getter function for easy access
  public static array   $settings        = [];
  public static bool    $is_custom_query = false;
  public static ?object $request_params  = null;
  public static array   $session_vars    = [];
  public static ?string $cached_title    = null;
  public static ?string $cached_home_url = null;

  public static array $perf_data = [
    'display_perf_results' => true,
    'create_rnd_transient' => 0,
    'get_rnd_transient'    => 0,
    'route_request'        => 0,
  ];

  // Use get_global('prop_name') for these
  public static int $preferred_player = 0;
  public static int $list_per_page    = 0;
  public static int $gallery_per_page = 0;

  // Initialize global props here if needed
  public static function construct() : void
  {
    self::$settings         = get_option('uf_settings', \Ultrafunk\Plugin\Storage\DEFAULT_SETTINGS);
    self::$cached_home_url  = esc_url(home_url());
    self::$preferred_player = get_cookie_value(COOKIE_KEY::UF_PREFERRED_PLAYER,  0,  2, PLAYER_TYPE::LIST);
    self::$list_per_page    = get_cookie_value(COOKIE_KEY::UF_LIST_PER_PAGE,    10, 50, get_settings_value('list_tracks_per_page'));
    self::$gallery_per_page = get_cookie_value(COOKIE_KEY::UF_GALLERY_PER_PAGE,  4, 24, get_settings_value('gallery_tracks_per_page'));
  }
}


/**************************************************************************************************************************/


function get_global(string $property) : mixed
{
  return Globals::$$property;
}

function get_settings_value(string $key) : int
{
  return Globals::$settings[$key];
}

function is_custom_query() : bool
{
  return Globals::$is_custom_query;
}

function set_is_custom_query(bool $value = true) : void
{
  Globals::$is_custom_query = $value;
}

function get_request_params() : ?object
{
  return Globals::$request_params;
}

function set_request_params(object &$params) : void
{
  Globals::$request_params = $params;
}

function get_session_vars() : array
{
  return Globals::$session_vars;
}

function set_session_vars(array $session_vars) : void
{
  Globals::$session_vars = $session_vars;
}

function is_request(string $resource, ?string $type = null) : bool
{
  if ($type === null)
    return isset(Globals::$request_params->get[$resource]);

  return (!empty(Globals::$request_params->get[$resource]) &&
         (Globals::$request_params->get[$resource] === $type));
}

function is_response(string $resource, ?string $type = null) : bool
{
  if ($type === null)
    return isset(Globals::$request_params->response[$resource]);

  return (!empty(Globals::$request_params->response[$resource]) &&
         (Globals::$request_params->response[$resource] === $type));
}

function is_termlist(string $type = null) : bool
{
  return is_request('termlist', $type);
}

function is_list_player(string $type = null) : bool
{
  return is_request('list_player', $type);
}

function is_shuffle(int $player_type = PLAYER_TYPE::ALL) : bool
{
  if ($player_type === PLAYER_TYPE::GALLERY)
    return !empty(Globals::$request_params->is_shuffle);

  if ($player_type === PLAYER_TYPE::LIST)
    return is_request('list_player', 'shuffle');

  if ($player_type === PLAYER_TYPE::ALL)
    return (is_shuffle(PLAYER_TYPE::GALLERY) || is_shuffle(PLAYER_TYPE::LIST));

  return false;
}

function get_cached_title() : ?string
{
  return Globals::$cached_title;
}

function set_cached_title(string $title) : void
{
  Globals::$cached_title = $title;
}

function get_cached_home_url(string $path = '') : ?string
{
  return Globals::$cached_home_url . $path;
}

function get_perf_data() : array
{
  return Globals::$perf_data;
}

function perf_start(string $startTimerKey) : void
{
  Globals::$perf_data[$startTimerKey] = hrtime(true);
}

function perf_stop(string $perfTimerKey, string $startTimerKey) : void
{
  Globals::$perf_data[$perfTimerKey] = round(((hrtime(true) - Globals::$perf_data[$startTimerKey]) / 1e+6), 2);
}

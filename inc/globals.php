<?php declare(strict_types=1);
/*
 * Ultrafunk plugin globals class and related functions
 *
 */


namespace Ultrafunk\Plugin\Globals;


use const Ultrafunk\Plugin\Constants\PLUGIN_ENV;

use Ultrafunk\Plugin\Constants\ {
  PLAYER_TYPE,
  COOKIE_KEY,
};

use function Ultrafunk\Plugin\Shared\get_cookie_value;


/**************************************************************************************************************************/


// Call Globals class constructor manually since it is "static"
Globals::construct();


/**************************************************************************************************************************/


class Globals
{
  // Each prop has getter function for fast access
  public static $is_custom_query = false;
  public static $request_params  = [];
  public static $session_vars    = [];
  public static $cached_title    = null;
  public static $cached_home_url = null;

  public static $perf_data = [
    'display_perf_results' => true,
    'create_rnd_transient' => 0,
    'get_rnd_transient'    => 0,
    'route_request'        => 0,
  ];

  // Use get_globals_prop('prop_name') for these
  public static $preferred_player = 0;
  public static $gallery_per_page = 0;
//public static $list_per_page    = 0;

  // Initialize global props here if needed
  public static function construct()
  {
    self::$cached_home_url  = esc_url(home_url());
    self::$preferred_player = get_cookie_value(COOKIE_KEY::UF_PREFERRED_PLAYER, 0, 2, PLAYER_TYPE::LIST);
    self::$gallery_per_page = get_cookie_value(COOKIE_KEY::UF_GALLERY_PER_PAGE, 3, 24, \intval(get_option('posts_per_page', PLUGIN_ENV['gallery_per_page'])));

    /*
    $user_settings          = get_cookie_json(COOKIE_KEY::UF_USER_SETTINGS);
    self::$preferred_player = $user_settings->preferred_player  ?? 2;
    self::$gallery_per_page = $user_settings->$gallery_per_page ?? 12;
    self::$list_per_page    = $user_settings->list_per_page     ?? 25;
    */
  }
}


/**************************************************************************************************************************/


function get_globals_prop($property) : mixed
{
  return Globals::$$property;
}

function is_custom_query() : bool
{
  return Globals::$is_custom_query;
}

function set_is_custom_query(bool $value = true) : void
{
  Globals::$is_custom_query = $value;
}

function get_request_params() : array
{
  return Globals::$request_params;
}

function set_request_params(array $params) : void
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

function is_termlist(string $type_key = null) : bool
{
  if ($type_key === null)
    return !empty(Globals::$request_params['type']['termlist']);

  return (!empty(Globals::$request_params['type']['termlist']) &&
          !empty(Globals::$request_params['type'][$type_key]));
}

function is_list_player(string $type_key = null) : bool
{
  if ($type_key === null)
    return !empty(Globals::$request_params['type']['list_player']);

  return (!empty(Globals::$request_params['type']['list_player']) &&
          !empty(Globals::$request_params['type'][$type_key]));
}

function is_shuffle(int $player_type = PLAYER_TYPE::NONE) : bool
{
  if ($player_type === PLAYER_TYPE::GALLERY)
    return !empty(Globals::$request_params['is_shuffle']);

  if ($player_type === PLAYER_TYPE::LIST)
    return !empty(Globals::$request_params['type']['shuffle']);

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

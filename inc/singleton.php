<?php declare(strict_types=1);
/*
 * Ultrafunk plugin singleton class and related functions
 *
 */


namespace Ultrafunk\Plugin\Singleton;


/**************************************************************************************************************************/
if (!defined('ABSPATH')) exit;
/**************************************************************************************************************************/


use Ultrafunk\Plugin\Shared\Constants\ {
  PLAYER_TYPE,
  COOKIE_KEY,
};

use function Ultrafunk\Plugin\Shared\Utils\get_cookie_value;


/**************************************************************************************************************************/


final class Singleton
{
  private function __construct() {}
  private function __clone() {}

  // Each prop has getter function for easy access
  public static array   $settings        = [];
  public static bool    $is_custom_query = false;
  public static ?object $request_params  = null;
  public static array   $response_params = [];
  public static ?string $cached_title    = null;
  public static ?string $cached_home_url = null;

  // Use get_prop('prop_name') for these
  public static int $preferred_player = 0;
  public static int $list_per_page    = 0;
  public static int $gallery_per_page = 0;

  public static array $perf_data = [
    'display_perf_results' => true,
    'create_rnd_transient' => 0,
    'get_rnd_transient'    => 0,
    'route_request'        => 0,
  ];

  public static function initialize() : void
  {
    self::$settings         = get_option('uf_settings', \Ultrafunk\Plugin\Shared\Constants\DEFAULT_SETTINGS);
    self::$cached_home_url  = esc_url(home_url());
    self::$preferred_player = get_cookie_value(COOKIE_KEY::UF_PREFERRED_PLAYER,  0,  2, PLAYER_TYPE::LIST);
    self::$list_per_page    = get_cookie_value(COOKIE_KEY::UF_LIST_PER_PAGE,    10, 50, get_settings_value('list_tracks_per_page'));
    self::$gallery_per_page = get_cookie_value(COOKIE_KEY::UF_GALLERY_PER_PAGE,  4, 24, get_settings_value('gallery_tracks_per_page'));
  }
}


/**************************************************************************************************************************/


Singleton::initialize();


/**************************************************************************************************************************/


function get_prop(string $property) : mixed
{
  return Singleton::$$property;
}

function get_settings_value(string $key) : int
{
  return Singleton::$settings[$key];
}

function is_custom_query() : bool
{
  return Singleton::$is_custom_query;
}

function set_is_custom_query(bool $value = true) : void
{
  Singleton::$is_custom_query = $value;
}

function get_request_params() : ?object
{
  return Singleton::$request_params;
}

function set_request_params(object &$request_params) : void
{
  Singleton::$request_params = $request_params;
}

function get_response_params() : array
{
  return Singleton::$response_params;
}

function set_response_params(array $response_params) : void
{
  Singleton::$response_params = $response_params;
}

function is_request(string $resource, ?string $type = null) : bool
{
  if ($type === null)
    return isset(Singleton::$request_params->get[$resource]);

  return (!empty(Singleton::$request_params->get[$resource]) &&
         (Singleton::$request_params->get[$resource] === $type));
}

function is_response(string $resource, ?string $type = null) : bool
{
  if ($type === null)
    return isset(Singleton::$request_params->response[$resource]);

  return (!empty(Singleton::$request_params->response[$resource]) &&
         (Singleton::$request_params->response[$resource] === $type));
}

function is_termlist(?string $type = null) : bool
{
  return is_request('termlist', $type);
}

function is_list_player(?string $type = null) : bool
{
  return is_request('list_player', $type);
}

function is_shuffle(int $player_type = PLAYER_TYPE::ALL) : bool
{
  switch ($player_type)
  {
    case PLAYER_TYPE::GALLERY:
      return !empty(Singleton::$request_params->is_shuffle);

    case PLAYER_TYPE::LIST:
      return is_request('list_player', 'shuffle');

    case PLAYER_TYPE::ALL:
      return (is_shuffle(PLAYER_TYPE::GALLERY) || is_shuffle(PLAYER_TYPE::LIST));
  }

  return false;
}

function get_cached_title() : ?string
{
  return Singleton::$cached_title;
}

function set_cached_title(string $title) : void
{
  Singleton::$cached_title = $title;
}

function get_cached_home_url(string $path = '') : ?string
{
  return Singleton::$cached_home_url . $path;
}

function get_perf_data() : array
{
  return Singleton::$perf_data;
}

function perf_start(string $startTimerKey) : void
{
  Singleton::$perf_data[$startTimerKey] = hrtime(true);
}

function perf_stop(string $perfTimerKey, string $startTimerKey) : void
{
  Singleton::$perf_data[$perfTimerKey] = round(((hrtime(true) - Singleton::$perf_data[$startTimerKey]) / 1e+6), 2);
}

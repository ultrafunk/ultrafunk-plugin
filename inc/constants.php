<?php declare(strict_types=1);
/*
 * Ultrafunk plugin constants
 *
 */


namespace Ultrafunk\Plugin\Constants;


/**************************************************************************************************************************/


const VERSION = '1.44.69';

const PLUGIN_ENV = [
  'site_url'            => WP_DEBUG ? 'https://wordpress.ultrafunk.com'     : 'https://ultrafunk.com',
  'handler_file_path'   => WP_DEBUG ? 'inc/request/handlers/'               : 'inc/request/handlers/',
  'handler_class_path'  => WP_DEBUG ? '\Ultrafunk\Plugin\Request\Handler\\' : '\Ultrafunk\Plugin\Request\Handler\\',
  'template_file_path'  => WP_DEBUG ? '/php/templates/'                     : '/php/templates/',
  'template_file'       => WP_DEBUG ? 'content-list-player.php'             : 'content-list-player.php',
  'template_class_path' => WP_DEBUG ? '\Ultrafunk\Theme\Templates\\'        : '\Ultrafunk\Theme\Templates\\',
  'template_class'      => WP_DEBUG ? 'ListPlayer'                          : 'ListPlayer',
];

const DEFAULT_SETTINGS = array(
  'list_tracks_per_page'     => WP_DEBUG ?  25 : 25,
  'gallery_tracks_per_page'  => WP_DEBUG ?  12 : 12,
  'channels_num_top_artists' => WP_DEBUG ?  10 : 10,
  'show_top_artists_log'     => WP_DEBUG ? '1' : '1',
);

// ToDo: Use PHP enum when v8.1 is ready for use
abstract class PLAYER_TYPE
{
  const NONE    = 0;
  const GALLERY = 1;
  const LIST    = 2;
}

// ToDo: Use PHP enum when v8.1 is ready for use
abstract class TRACK_TYPE
{
  const NONE       = 0;
  const YOUTUBE    = 1;
  const SOUNDCLOUD = 2;
}

abstract class COOKIE_KEY
{
//const UF_USER_SETTINGS    = 'uf_user_settings';
  const UF_GALLERY_PER_PAGE = 'uf_gallery_per_page';
  const UF_LIST_PER_PAGE    = 'uf_list_per_page';
  const UF_PREFERRED_PLAYER = 'uf_preferred_player';
  const UF_SHUFFLE_UID      = 'uf_shuffle_uid';
  const UF_RESHUFFLE        = 'uf_reshuffle';
}

// https://webapps.stackexchange.com/a/101153
const YOUTUBE_VIDEO_ID_REGEX = '/[0-9A-Za-z_-]{10}[048AEIMQUYcgkosw]/';

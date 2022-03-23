<?php declare(strict_types=1);
/*
 * Ultrafunk plugin constants
 *
 */


namespace Ultrafunk\Plugin\Constants;


/**************************************************************************************************************************/


const VERSION = '1.41.4';

const PLUGIN_ENV = [
  'gallery_per_page' => WP_DEBUG ? 12 : 12,
  'list_per_page'    => WP_DEBUG ? 25 : 25,
  'site_url'         => WP_DEBUG ? 'https://wordpress.ultrafunk.com' : 'https://ultrafunk.com',
];

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
  const UF_USER_SETTINGS    = 'uf_user_settings';
  const UF_GALLERY_PER_PAGE = 'uf_gallery_per_page';
  const UF_PREFERRED_PLAYER = 'uf_preferred_player';
  const UF_SHUFFLE_UID      = 'uf_shuffle_uid';
  const UF_RESHUFFLE        = 'uf_reshuffle';
}

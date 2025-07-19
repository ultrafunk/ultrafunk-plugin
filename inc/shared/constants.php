<?php declare(strict_types=1);
/*
 * Shared plugin + theme constants
 *
 */


namespace Ultrafunk\Plugin\Shared\Constants;


/**************************************************************************************************************************/


// https://webapps.stackexchange.com/a/101153
const YOUTUBE_VIDEO_ID_REGEX = '/[0-9A-Za-z_-]{10}[048AEIMQUYcgkosw]/';

const DEFAULT_SETTINGS = [
  'list_tracks_per_page'     => 25,
  'gallery_tracks_per_page'  => 12,
  'channels_num_top_artists' => 10,
  'show_top_artists_log'     => true,
];

// ToDo: Use PHP enum when v8.1 is ready for use
abstract class PLAYER_TYPE
{
  const NONE    = 0;
  const GALLERY = 1;
  const LIST    = 2;
  const ALL     = 100;
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

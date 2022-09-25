<?php declare(strict_types=1);
/*
 * RouteRequest default routes
 *
 */


namespace Ultrafunk\Plugin\Request;


/**************************************************************************************************************************/


const DEFAULT_ROUTES =
[

/*
* Redirects first (highest priority)
*/

  [
    'route_uids'    => [ 'list/channel/soundcloud', 'list/shuffle/channel/soundcloud' ],
    'handler_file'  => 'redirect-route.php',
    'handler_class' => 'RedirectRoute',
    'routes' => [
      'channel_soundcloud'              => '/^list\/channel\/soundcloud$/',
      'channel_soundcloud_page'         => '/^list\/channel\/soundcloud\/page\/(?!0)\d{1,6}$/',
      'shuffle_channel_soundcloud'      => '/^list\/shuffle\/channel\/soundcloud$/',
      'shuffle_channel_soundcloud_page' => '/^list\/shuffle\/channel\/soundcloud\/page\/(?!0)\d{1,6}$/',
    ]
  ],

/*
* Then all the normal routes...
*/

  [
    'route_uids'    => [ '===list', 'list/page/' ],
    'handler_file'  => 'list-player-all.php',
    'handler_class' => 'ListPlayerAll',
    'routes' => [
      'list_player_all'      => '/^list$/',
      'list_player_all_page' => '/^list\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uids'    => [ 'shuffle/' ],
    'handler_file'  => 'shuffle.php',
    'handler_class' => 'Shuffle',
    'routes' => [
      'shuffle_all'       => '/^shuffle\/all$/',
      'shuffle_all_page'  => '/^shuffle\/all\/page\/(?!0)\d{1,6}$/',
      'shuffle_slug'      => '/^shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*$/',
      'shuffle_slug_page' => '/^shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uids'    => [ 'artists' ],
    'handler_file'  => 'termlist-artists.php',
    'handler_class' => 'TermlistArtists',
    'routes' => [
      'artists'        => '/^artists$/',
      'artists_letter' => '/^artists\/[a-z]$/',
    ]
  ],
  [
    'route_uids'    => [ '===channels' ],
    'handler_file'  => 'termlist-channels.php',
    'handler_class' => 'TermlistChannels',
    'routes' => [
      'channels' => '/^channels$/',
    ]
  ],
  [
    'route_uids'    => [ 'list/artist/', 'list/channel/' ],
    'handler_file'  => 'list-player-artist-channel.php',
    'handler_class' => 'ListPlayerArtistChannel',
    'routes' => [
      'list_player_artist'       => '/^list\/artist\/[a-z0-9-]*$/',
      'list_player_artist_page'  => '/^list\/artist\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
      'list_player_channel'      => '/^list\/channel\/[a-z0-9-]*$/',
      'list_player_channel_page' => '/^list\/channel\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uids'    => [ 'list/20' ],
    'handler_file'  => 'list-player-date.php',
    'handler_class' => 'ListPlayerDate',
    'routes' => [
      'list_player_date'      => '/^list\/20[0-9]{2}\/[0-3][0-9]$/',
      'list_player_date_page' => '/^list\/20[0-9]{2}\/[0-3][0-9]\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uids'    => [ 'list/shuffle/' ],
    'handler_file'  => 'list-player-shuffle.php',
    'handler_class' => 'ListPlayerShuffle',
    'routes' => [
      'shuffle_all'       => '/^list\/shuffle\/all$/',
      'shuffle_all_page'  => '/^list\/shuffle\/all\/page\/(?!0)\d{1,6}$/',
      'shuffle_slug'      => '/^list\/shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*$/',
      'shuffle_slug_page' => '/^list\/shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uids'    => [ 'list/search' ],
    'handler_file'  => 'list-player-search.php',
    'handler_class' => 'ListPlayerSearch',
    'routes' => [
      'list_player_search'      => '/^list\/search$/',
      'list_player_search_page' => '/^list\/search\/page\/(?!0)\d{1,6}$/',
    ]
  ],
];

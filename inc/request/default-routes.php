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
    'handler'    => ['file' => 'redirect-route.php', 'class' => 'RedirectRoute'],
    'route_uids' => ['list/channel/soundcloud', 'list/shuffle/channel/soundcloud'],
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
    'handler'    => ['file' => 'list-player-all.php', 'class' => 'ListPlayerAll'],
    'route_uids' => ['===list', 'list/page/'],
    'routes' => [
      'list_player_all'      => '/^list$/',
      'list_player_all_page' => '/^list\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'handler'    => ['file' => 'shuffle.php', 'class' => 'Shuffle'],
    'route_uids' => ['shuffle/'],
    'routes' => [
      'shuffle_all'       => '/^shuffle\/all$/',
      'shuffle_all_page'  => '/^shuffle\/all\/page\/(?!0)\d{1,6}$/',
      'shuffle_slug'      => '/^shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*$/',
      'shuffle_slug_page' => '/^shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'handler'    => ['file' => 'termlist-artists.php', 'class' => 'TermlistArtists'],
    'route_uids' => ['artists'],
    'routes' => [
      'artists'        => '/^artists$/',
      'artists_letter' => '/^artists\/[a-z]$/',
    ]
  ],
  [
    'handler'    => ['file' => 'termlist-channels.php', 'class' => 'TermlistChannels'],
    'route_uids' => ['===channels'],
    'routes' => [
      'channels' => '/^channels$/',
    ]
  ],
  [
    'handler'    => ['file' => 'list-player-artist-channel.php', 'class' => 'ListPlayerArtistChannel'],
    'route_uids' => ['list/artist/', 'list/channel/'],
    'routes' => [
      'list_player_artist'       => '/^list\/artist\/[a-z0-9-]*$/',
      'list_player_artist_page'  => '/^list\/artist\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
      'list_player_channel'      => '/^list\/channel\/[a-z0-9-]*$/',
      'list_player_channel_page' => '/^list\/channel\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'handler'    => ['file' => 'list-player-date.php', 'class' => 'ListPlayerDate'],
    'route_uids' => ['list/20'],
    'routes' => [
      'list_player_date'      => '/^list\/20[0-9]{2}\/[0-3][0-9]$/',
      'list_player_date_page' => '/^list\/20[0-9]{2}\/[0-3][0-9]\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'handler'    => ['file' => 'list-player-shuffle.php', 'class' => 'ListPlayerShuffle'],
    'route_uids' => ['list/shuffle/'],
    'routes' => [
      'shuffle_all'       => '/^list\/shuffle\/all$/',
      'shuffle_all_page'  => '/^list\/shuffle\/all\/page\/(?!0)\d{1,6}$/',
      'shuffle_slug'      => '/^list\/shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*$/',
      'shuffle_slug_page' => '/^list\/shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'handler'    => ['file' => 'list-player-search.php', 'class' => 'ListPlayerSearch'],
    'route_uids' => ['list/search'],
    'routes' => [
      'list_player_search'      => '/^list\/search$/',
      'list_player_search_page' => '/^list\/search\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'handler'    => ['file' => 'dj-player.php', 'class' => 'DJPlayer'],
    'route_uids' => ['dj-player'],
    'routes' => [
      'dj-player' => '/^dj-player$/',
    ]
  ],
];

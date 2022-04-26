<?php declare(strict_types=1);
/*
 * RouteRequest default routes
 *
 */


namespace Ultrafunk\Plugin\Request;


/**************************************************************************************************************************/


const DEFAULT_ROUTES =
[
  [
    'route_uid'          => 'list',
    'match_uid_exactly'  => true,
    'handler_file'       => 'inc/request/handlers/list-player-all.php',
    'handler_class'      => '\Ultrafunk\Plugin\Request\Handler\ListPlayerAll',
    'template_file'      => 'content-list-player.php',
    'template_namespace' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_all' => '/^list$/',
    ]
  ],
  [
    'route_uid'          => 'list/page/',
    'handler_file'       => 'inc/request/handlers/list-player-all.php',
    'handler_class'      => '\Ultrafunk\Plugin\Request\Handler\ListPlayerAll',
    'template_file'      => 'content-list-player.php',
    'template_namespace' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_all_page' => '/^list\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'     => 'shuffle/',
    'handler_file'  => 'inc/request/handlers/shuffle.php',
    'handler_class' => '\Ultrafunk\Plugin\Request\Handler\Shuffle',
    'routes' => [
      'shuffle_all'       => '/^shuffle\/all$/',
      'shuffle_all_page'  => '/^shuffle\/all\/page\/(?!0)\d{1,6}$/',
      'shuffle_slug'      => '/^shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*$/',
      'shuffle_slug_page' => '/^shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'          => 'artists',
    'handler_file'       => 'inc/request/handlers/termlist-artists.php',
    'handler_class'      => '\Ultrafunk\Plugin\Request\Handler\TermlistArtists',
    'template_file'      => 'content-termlist.php',
    'template_namespace' => '\Ultrafunk\Theme\Templates\Termlist',
    'routes' => [
      'artists'             => '/^artists$/',
    //'artists_page'        => '/^artists\/page\/(?!0)\d{1,6}$/',
      'artists_letter'      => '/^artists\/[a-z]$/',
    //'artists_letter_page' => '/^artists\/[a-z]\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'          => 'channels',
    'match_uid_exactly'  => true,
    'handler_file'       => 'inc/request/handlers/termlist-channels.php',
    'handler_class'      => '\Ultrafunk\Plugin\Request\Handler\TermlistChannels',
    'template_file'      => 'content-termlist.php',
    'template_namespace' => '\Ultrafunk\Theme\Templates\Termlist',
    'routes' => [
      'channels'      => '/^channels$/',
    //'channels_page' => '/^channels\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'          => 'list/artist/',
    'handler_file'       => 'inc/request/handlers/list-player-artist-channel.php',
    'handler_class'      => '\Ultrafunk\Plugin\Request\Handler\ListPlayerArtistChannel',
    'template_file'      => 'content-list-player.php',
    'template_namespace' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_artist'      => '/^list\/artist\/[a-z0-9-]*$/',
      'list_player_artist_page' => '/^list\/artist\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'          => 'list/channel/',
    'handler_file'       => 'inc/request/handlers/list-player-artist-channel.php',
    'handler_class'      => '\Ultrafunk\Plugin\Request\Handler\ListPlayerArtistChannel',
    'template_file'      => 'content-list-player.php',
    'template_namespace' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_channel'      => '/^list\/channel\/[a-z0-9-]*$/',
      'list_player_channel_page' => '/^list\/channel\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'          => 'list/20',
    'handler_file'       => 'inc/request/handlers/list-player-date.php',
    'handler_class'      => '\Ultrafunk\Plugin\Request\Handler\ListPlayerDate',
    'template_file'      => 'content-list-player.php',
    'template_namespace' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_date'      => '/^list\/20[0-9]{2}\/[0-3][0-9]$/',
      'list_player_date_page' => '/^list\/20[0-9]{2}\/[0-3][0-9]\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'          => 'list/shuffle/',
    'handler_file'       => 'inc/request/handlers/list-player-shuffle.php',
    'handler_class'      => '\Ultrafunk\Plugin\Request\Handler\ListPlayerShuffle',
    'template_file'      => 'content-list-player.php',
    'template_namespace' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'shuffle_all'       => '/^list\/shuffle\/all$/',
      'shuffle_all_page'  => '/^list\/shuffle\/all\/page\/(?!0)\d{1,6}$/',
      'shuffle_slug'      => '/^list\/shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*$/',
      'shuffle_slug_page' => '/^list\/shuffle\/(\bchannel\b|\bartist\b)*\/[a-z0-9-]*\/page\/(?!0)\d{1,6}$/',
    ]
  ],
  [
    'route_uid'          => 'list/search',
    'handler_file'       => 'inc/request/handlers/list-player-search.php',
    'handler_class'      => '\Ultrafunk\Plugin\Request\Handler\ListPlayerSearch',
    'template_file'      => 'content-list-player.php',
    'template_namespace' => '\Ultrafunk\Theme\Templates\ListPlayer',
    'routes' => [
      'list_player_search'      => '/^list\/search$/',
      'list_player_search_page' => '/^list\/search\/page\/(?!0)\d{1,6}$/',
    ]
  ],
];

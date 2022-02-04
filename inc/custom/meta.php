<?php declare(strict_types=1);
/*
* Ultrafunk custom meta-fields for REST API
*/


namespace Ultrafunk\Plugin\Meta;


/**************************************************************************************************************************/


//
// Register meta fields for REST API fetch
//
function register_fields()
{
  register_post_meta('uf_track', 'track_artist',
    [
      'type'         => 'string',
      'description'  => 'track_artist',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );

  register_post_meta('uf_track', 'track_artist_id',
    [
      'type'         => 'number',
      'description'  => 'track_artist_id',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );

  register_post_meta('uf_track', 'track_source_type',
    [
      'type'         => 'number',
      'description'  => 'track_source_type',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );

  register_post_meta('uf_track', 'track_source_data',
    [
      'type'         => 'string',
      'description'  => 'track_source_data',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );

  register_post_meta('uf_track', 'track_title',
    [
      'type'         => 'string',
      'description'  => 'track_title',
      'single'       => true,
      'show_in_rest' => true,
    ]
  );
}
add_action('rest_api_init', '\Ultrafunk\Plugin\Meta\register_fields');

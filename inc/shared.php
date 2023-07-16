<?php declare(strict_types=1);
/*
 * Shared plugin + theme constants and functions
 *
 */


namespace Ultrafunk\Plugin\Shared;


/**************************************************************************************************************************/


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

// https://webapps.stackexchange.com/a/101153
const YOUTUBE_VIDEO_ID_REGEX = '/[0-9A-Za-z_-]{10}[048AEIMQUYcgkosw]/';


/**************************************************************************************************************************/


//
// Output debug info to the browser console, this will not always work and may cause strange side effects!
//
function console_log(mixed $output) : void
{
  if (\Ultrafunk\Plugin\Config\IS_DEBUG)
    echo '<script>console.log(' . json_encode($output, JSON_HEX_TAG) . ');</script>';
}

//
// Return HTML links: <a href=""></a> for an array of WP_Terms
//
function get_term_links(array $terms, string $path, string $separator = '',  int $primary_id = -1) : string
{
  $term_links = [];

  foreach ($terms as $term)
  {
    $class = (($primary_id !== -1) && ($term->term_id === $primary_id)) ? 'primary' : 'secondary';
    $term_links[] = "<a class=\"$class\" href=\"$path$term->slug/\">$term->name</a>";
  }

  return implode($separator, $term_links);
}

<?php declare(strict_types=1);
/*
 * Get page cache info from server
 *
 */


namespace Ultrafunk\Plugin\Admin\PageCacheInfo;


/**************************************************************************************************************************/
if (!defined('ABSPATH')) exit;
/**************************************************************************************************************************/


//
// Based on: https://stackoverflow.com/a/21409562
//
function get_cache_dir_info(string $path) : array
{
  $num_bytes = 0;
  $num_files = 0;
  $real_path = realpath($path);

  if (($real_path !== false) && ($real_path !== '') && file_exists($real_path))
  {
    foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($real_path, \FilesystemIterator::SKIP_DOTS)) as $object)
    {
      $num_bytes += $object->getSize();
      $num_files++;
    }
  }

  return [
    'updated_at'  => time(),
    'total_bytes' => $num_bytes,
    'total_files' => $num_files,
  ];
}

function update_transient(string $cache_path) : bool
{
  if (isset($_SERVER['DOCUMENT_ROOT']))
  {
    $page_cache_info = get_cache_dir_info($_SERVER['DOCUMENT_ROOT'] . $cache_path);

    if (($page_cache_info['total_bytes'] !== 0) && ($page_cache_info['total_files'] !== 0))
    {
      set_transient('uf_page_cache_info', $page_cache_info, YEAR_IN_SECONDS);
      return true;
    }
  }

  return false;
}

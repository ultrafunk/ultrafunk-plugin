<?php declare(strict_types=1);
/*
 * Update site page cache stats
 *
 */


namespace Ultrafunk\Plugin\Admin\PageCacheStats;


/**************************************************************************************************************************/


//
// Based on: https://stackoverflow.com/a/21409562
//
function get_directory_stats(string $path) : array
{
  $num_bytes = 0;
  $num_files = 0;
  $num_dirs  = 0;
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
    'total_dirs'  => $num_dirs,
  ];
}

function update_page_cache_stats(string $cache_path) : bool
{
  if (isset($_SERVER['DOCUMENT_ROOT']))
  {
    $page_cache_data = get_directory_stats($_SERVER['DOCUMENT_ROOT'] . $cache_path);

    if (($page_cache_data['total_bytes'] !== 0) && ($page_cache_data['total_files'] !== 0))
    {
      set_transient('uf_page_cache_stats', $page_cache_data, (YEAR_IN_SECONDS * 10));
      return true;
    }
  }

  return false;
}

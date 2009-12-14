<?php

/**
 * sfDynamicsRenderer - Assets renderer. Manage minifying and grouping.
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license MIT
 */
class sfDynamicsRenderer
{
  /**
   * getAsset - render assets of a given type for a package
   *
   * @param  string                              $name
   * @param  sfDynamicsAssetCollectionDefinition $package
   * @param  string                              $type
   * @return string
   */
  public function getAsset($name, sfDynamicsAssetCollectionDefinition $package, $type)
  {
    $extension = sfDynamics::getExtensionFromType($type);
    $getAssets = 'get'.ucfirst($type).'s';

    if (count($assets = $package->$getAssets()))
    {
      $paths = $package->getPaths('/'.$extension);

      if (sfDynamicsConfig::isCacheEnabled())
      {
        $cacheKey = sfDynamicsCache::generateKey($package, $type);
        $cache = sfDynamics::getCache();

        if ($cache->has($cacheKey))
        {
          if (sfDynamicsConfig::isCacheUpToDateCheckEnabled())
          {
            foreach ($assets as $asset)
            {
              // this is needed to know the actual file path on disk, and to know its mtime.
              $asset->computePath($paths);
            }

            if ($cache->isStillUpToDate($package, $type, $cacheKey))
            {
              $result = $cache->get($cacheKey);
            }
          }
          else
          {
            $result = $cache->get($cacheKey);
          }
        }
      }

      // still no result? let's build it!
      if (!isset($result))
      {
        $result = $this->getConcatenatedAssets($package, $paths, $assets);
        $result = sfDynamicsConfig::getConcatenatedAssetFilterChainFor($type)->filter($result);

        if (sfDynamicsConfig::isCacheEnabled())
        {
          $cache->set($cacheKey, $result);
        }
      }

      return $result;
    }
    else
    {
      return '';
    }
  }

  /**
   * getConcatenatedAssets - Packs a list of assets in one string
   *
   * @param sfDynamicsPackageDefinition $package
   * @param string                      $type    either «js» or «css»
   * @param array                       $paths
   * @param array                       $assets
   * @return void
   */
  protected function getConcatenatedAssets($package, array $paths, array $assets)
  {
    $result = '';
    $attempts = array();

    foreach ($assets as $asset)
    {
      $asset->computePath($paths);

      $result .= $asset->getFilteredContent($package)."\n";
    }

    return $result;
  }

  /**
   * generateSupercache - creates supercache file for given packages
   *
   * @param mixed $url
   * @param mixed $packages
   * @param mixed $assets
   * @param mixed $type
   * @return void
   */
  public function generateSupercache($url, $packages, $assets, $type)
  {
    if (!sfDynamicsConfig::isSupercacheEnabled())
    {
      throw new BadMethodCallException('Supercache is disabled.');
    }

    if (!file_exists($filename = sfConfig::get('sf_web_dir').'/'.$url))
    {
      $src = '';

      foreach ($packages as $name => $package)
      {
        if ($renderedSrc = trim($this->getAsset($name, $package, $type)))
        {
          $src .= '/* '.$name.' */ '.$renderedSrc."\n";
        }
      }

      @file_put_contents($filename, $src);
      if (!file_exists($filename))
      {
        throw new sfException('Supercache could not be written: '.$filename);
      }
    }
  }
}

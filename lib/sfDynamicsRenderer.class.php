<?php

/**
 * sfDynamicsRenderer - Assets renderer. Manage minifying and grouping.
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license
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
      $paths = $package->getPaths();

      if (sfDynamicsConfig::isCacheEnabled())
      {
        $cache = sfDynamics::getCache();
        $cacheKey = sfDynamicsCache::generateKey($package, $type);

        if ($cache->has($cacheKey))
        {
          $result = $cache->get($cacheKey);
        }
      }

      if (!isset($result))
      {
        $result = $this->{'filter'.ucfirst($type)}($name, $this->getConcatenatedAssets($name, $extension, $paths, $assets));

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

  protected function filterJavascript(sfDynamicsAssetCollectionDefinition $package, $code)
  {
    if (sfDynamicsConfig::isJavascriptMinifierEnabled($package))
    {
      $code = JSMin::minify($code);
    }

    return $code;
  }

  protected function filterStylesheet(sfDynamicsAssetCollectionDefinition $package, $code)
  {
    if (sfDynamicsConfig::isStylesheetTidyEnabled($package))
    {
      $code = preg_replace('/\s\s+/m', ' ', str_replace(array("\n", "\t"), ' ', $code));
    }

    return $code;
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
  protected function getConcatenatedAssets($packageName, $type, array $paths, array $assets)
  {
    $result = '';
    $attempts = array();

    foreach ($assets as $asset)
    {
      foreach ($paths as $path)
      {
        $file = $path.'/'.$type.'/'.$asset.'.'.$type;

        if (file_exists($file) && is_readable($file) && is_file($file))
        {
          break;
        }

        $attempts[] = $file;
        $file = null;
      }

      if (is_null($file))
      {
        throw new sfDynamicsUnreadableAssetException(sprintf('Unreadable asset file for package «%s».%sAttempts in order: %s%s', $packageName, "\n\n", "\n - ", implode("\n - ", $attempts)));
      }

      $result .= file_get_contents($file)."\n";
    }

    return $result;
  }

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

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
      $paths = $package->getPaths('/'.$type);

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
        $result = $this->{'filterConcatenated'.ucfirst($type)}($package, $this->getConcatenatedAssets($name, $package, $extension, $paths, $assets));

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
   * filterJavascript - javascript specific rendering filters
   *
   * @param  sfDynamicsAssetCollectionDefinition $package
   * @param  string $code
   * @return string
   */
  protected function filterConcatenatedJavascript(sfDynamicsAssetCollectionDefinition $package, $code)
  {
    if (sfDynamicsConfig::isJavascriptMinifierEnabled($package))
    {
      $code = JSMin::minify($code);
    }

    return $code;
  }

  protected function filterJavascript(sfDynamicsAssetCollectionDefinition $package, $filename, $code)
  {
    return $code;
  }

  /**
   * filterStylesheet - stylesheet specific rendering filters
   *
   * @param  sfDynamicsAssetCollectionDefinition $package
   * @param  string $code
   * @return string
   */
  protected function filterConcatenatedStylesheet(sfDynamicsAssetCollectionDefinition $package, $code)
  {
    if (sfDynamicsConfig::isStylesheetTidyEnabled($package))
    {
      $code = preg_replace('/\s\s+/m', ' ', str_replace(array("\n", "\t"), ' ', $code));
    }

    return $code;
  }

  protected function filterStylesheet(sfDynamicsAssetCollectionDefinition $package, $filename, $code)
  {
    if (sfDynamicsConfig::isStylesheetImportResolutionEnabled($package))
    {
      $callback = create_function('$v', sprintf('return file_get_contents(%s.\'/\'.$v[2]);', var_export(dirname($filename), true)));
      $code = preg_replace_callback('/@import\s+url\((["\']?)([a-z\/\\._-]+)(\1)\);/i', $callback, $code);
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
  protected function getConcatenatedAssets($packageName, $package, $type, array $paths, array $assets)
  {
    $result = '';
    $attempts = array();

    foreach ($assets as $asset)
    {
      foreach ($paths as $path)
      {
        $file = $path.'/'.$asset.'.'.$type;

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

      if (sfConfig::get('sf_debug'))
      {
        $result .= sprintf("/* \n * sfDynamicsPlugin include: %s\n */", $file)."\n\n";
      }

      $result .= $this->{'filter'.ucfirst(sfDynamics::getTypeFromExtension($type))}($package, $file, file_get_contents($file))."\n\n";
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

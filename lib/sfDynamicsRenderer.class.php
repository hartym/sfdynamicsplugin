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
   * getJavascript - render javascripts for a package
   *
   * @param  string                              $name
   * @param  sfDynamicsAssetCollectionDefinition $package
   * @return string
   */
  public function getJavascript($name, sfDynamicsAssetCollectionDefinition $package)
  {
    if (count($javascripts = $package->getJavascripts()))
    {
      $paths = $package->getPaths();

      $cache = sfDynamics::getCache();

      if (sfDynamics::isCacheEnabled() && $cache->has($cacheKey = '/'.sfConfig::get('sf_environment').(sfConfig::get('sf_debug')?'/debug':'').'/js/'.md5($package->getCacheKey())))
      {
        $result = $cache->get($cacheKey);
      }
      else
      {
        $result = $this->getConcatenatedAssets('js', $paths, $javascripts);

        if (sfDynamics::isJavascriptMinifierEnabled($package))
        {
          $result = JSMin::minify($result);
        }

        if (sfDynamics::isCacheEnabled())
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
   * getStylesheet - render stylesheets for a package
   *
   * @param  string                              $name
   * @param  sfDynamicsAssetCollectionDefinition $package
   * @return string
   */
  public function getStylesheet($name, sfDynamicsAssetCollectionDefinition $package)
  {
    if (count($stylesheets = $package->getStylesheets()))
    {
      $paths = $package->getPaths();

      $cache = sfDynamics::getCache();

      if (sfDynamics::isCacheEnabled() && $cache->has($cacheKey = '/'.sfConfig::get('sf_environment').(sfConfig::get('sf_debug')?'/debug':'').'/css/'.md5($package->getCacheKey())))
      {
        $result = $cache->get($cacheKey);
      }
      else
      {
        $result = $this->getConcatenatedAssets('css', $paths, $stylesheets);

        if (sfDynamics::isStylesheetTidyEnabled($package))
        {
          $result = preg_replace('/\s\s+/m', ' ', str_replace(array("\n", "\t"), ' ', $result));
        }

        if (sfDynamics::isCacheEnabled())
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
   * @param mixed $type
   * @param mixed $paths
   * @param mixed $assets
   * @return void
   */
  protected function getConcatenatedAssets($type, $paths, $assets)
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
        throw new sfError404Exception('Unreadable asset file for package '.$this->name.'. Attempts in order: '.implode(', ', $attempts));
      }

      $result .= file_get_contents($file)."\n";
    }

    return $result;
  }

}

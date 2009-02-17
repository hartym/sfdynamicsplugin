<?php

class sfDynamicsRenderer
{
  public function getJavascript($name, $package)
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

  public function getStylesheet($name, $package)
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

<?php

/**
 * sfDynamicsConfig
 *
 * Centralise simple configuration triggers.
 *
 * @todo allow overiding this in app.yml (not urgent)
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 */
class sfDynamicsConfig
{
  static public function isCacheEnabled()
  {
    return !sfConfig::get('sf_debug');
  }

  static public function isSupercacheEnabled()
  {
    return !sfConfig::get('sf_debug');
  }

  /**
   * isJavascriptPackerEnabled - THIS IS BOGUS
   *
   * JS Packer does not work with javascripts without endline semicolon
   *
   * @param mixed $package
   * @return void
   */
  static public function isJavascriptPackerEnabled($package)
  {
    return false;
  }

  static public function isJavascriptMinifierEnabled($package)
  {
    return !sfConfig::get('sf_debug');
  }

  static public function isGroupingEnabledFor($type)
  {
    switch ($type)
    {
      case 'javascript':
      case 'stylesheet':
        return !sfConfig::get('sf_debug');

      default:
        throw new BadMethodCallException('Invalid asset type');
    }
  }

  static public function isStylesheetImportResolutionEnabled($package)
  {
    return true;
  }

  static public function isStylesheetRelativePathsResolutionEnabled($package)
  {
    return false;
  }

  static public function isStylesheetTidyEnabled($package)
  {
    return !sfConfig::get('sf_debug');
  }

  /**
   * getSuperCacheDir - path under sf_web_dir in which we'll store the supercached assets
   *
   * @return string
   */
  static public function getSuperCacheDir()
  {
    return sfConfig::get('app_sfDynamicsPlugin_supercache_web_path', 'dynamics');
  }
}

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

  static public function isStylesheetTidyEnabled($package)
  {
    return !sfConfig::get('sf_debug');
  }
}

<?php

/**
 * sfDynamics
 *
 * This is the helper class for most comonly used functions.
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @copyright Copyright (C) Sensio Labs
 * @author Romain Dorgueil <romain.dorgueil@sensio.com>
 * @license
 */
class sfDynamics
{
  static protected $manager = null;

  /**
   * Retrieve the default javascript behavior manager.
   *
   * @param mixed $context
   * @return void
   */
  static public function getManager($context=null)
  {
    if (is_null(self::$manager))
    {
      if (is_null($context))
      {
        $context = sfContext::getInstance();
      }

      self::$manager = new sfDynamicsManager($context);
    }

    return self::$manager;
  }

  /**
   * Loads one or more dynamics package
   *
   * @return void
   */
  static public function load()
  {
    $manager = self::getManager();

    foreach(func_get_args() as $arg)
    {
      $manager->load($arg);
    }
  }

  static public function getRenderer()
  {
    static $renderer = null;

    if(is_null($renderer))
    {
      $renderer = new sfDynamicsRenderer();
    }

    return $renderer;
  }

  static public function getCache()
  {
    static $cache = null;

    if(is_null($cache))
    {
      $cache = new sfDynamicsCache();
    }

    return $cache;
  }

  static public function isCacheEnabled()
  {
    return !sfConfig::get('sf_debug');
  }

  static public function isSupercacheEnabled()
  {
    return !sfConfig::get('sf_debug');
  }

  static public function isJavascriptPackerEnabled($package)
  {
    return false;
  }

  static public function isJavascriptMinifierEnabled($package)
  {
    return (!sfConfig::get('sf_debug'));
  }

  static public function isJavascriptGroupingEnabled()
  {
    return (!sfConfig::get('sf_debug'));
  }

  static public function isStylesheetTidyEnabled($package)
  {
    return (!sfConfig::get('sf_debug'));
  }

  static public function isStylesheetGroupingEnabled()
  {
    return (!sfConfig::get('sf_debug'));
  }
}

<?php

class sfDynamics
{
  static protected $manager = null;

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

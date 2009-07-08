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
      if (!in_array('sfDynamics', sfConfig::get('sf_enabled_modules', array())))
      {
        throw new sfConfigurationException('The module "sfDynamics" is not enabled.'.PHP_EOL.PHP_EOL.'Please add it to the enabled_modules list in your applications\' settings.yml.');
      }

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

    if (is_null($renderer))
    {
      $renderer = new sfDynamicsRenderer();
    }

    return $renderer;
  }

  static public function getCache()
  {
    static $cache = null;

    if (is_null($cache))
    {
      $cache = new sfDynamicsCache();
    }

    return $cache;
  }

  static public function getExtensionFromType($type)
  {
    switch ($type)
    {
      case 'javascript': return 'js';
      case 'stylesheet': return 'css';
      default:
        throw new BadMethodCallException(sprintf('Invalid asset type «%s».', $type));
    }
  }

  static public function getTypeFromExtension($extension)
  {
    switch ($extension)
    {
      case 'js': return 'javascript';
      case 'css': return 'stylesheet';
      default:
        throw new BadMethodCallException(sprintf('Invalid asset extension «%s».', $extension));
    }
  }
}

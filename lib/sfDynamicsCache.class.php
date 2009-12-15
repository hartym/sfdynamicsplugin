<?php

/**
 * sfDynamicsCache - Cache manager for sfDynamicsPlugin
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license
 */
class sfDynamicsCache extends sfFileCache
{
  public function initialize($options = array())
  {
    $options = array_merge($options, array('cache_dir' => self::getCacheDir()));

    parent::initialize($options);
  }

  public function isStillUpToDate($package, $type, $key)
  {
    return filemtime($this->getFilePath($key)) >= $package->getModificationTimeFor($type);
  }

  /**
   * Retrieves the base cache directory (absolute)
   *
   * @return string
   */
  static public function getCacheDir()
  {
    return sfConfig::get('sf_cache_dir').DIRECTORY_SEPARATOR.'sfDynamicsPlugin';
  }

  static public function generateKey(sfDynamicsAssetCollectionDefinition $package, $type)
  {
    return '/'.sfConfig::get('sf_environment').(sfConfig::get('sf_debug')?'/debug':'')
           .'/'.$type
           .'/'.md5($package->getCacheKey());
  }

  /**
   * Retrieves the base supercache directory (absolute or relative)
   *
   * @param  boolean $absolute -- if true, gives the filesystem path. If false,
   *                              gives the web path.
   * @return string
   */
  static public function getSuperCacheDir($absolute=false)
  {
    if ($absolute)
    {
      return sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.sfDynamicsConfig::getSuperCacheDir();
    }
    else
    {
      return '/'.sfDynamicsConfig::getSuperCacheDir();
    }
  }

  /**
   * clearSuperCache - clear supercache of minified assets
   *
   * @listen task.cache.clear
   *
   * @param  sfEvent $event
   * @return void
   */
  static public function clearSuperCache(sfEvent $event)
  {
    static $done=false;

    /* symfony send the task.cache.clear event once by app. */
    if (!$done)
    {
      $event->getSubject()->logSection('cache', 'Clearing sfDynamicsPlugin minified assets super cache');

      if (is_dir($cacheDir = self::getSuperCacheDir(true)))
      {
        $event->getSubject()->getFilesystem()->remove(sfFinder::type('file')->ignore_version_control()->discard('.sf')->in($cacheDir));
      }

      $done = true;
    }
  }
}

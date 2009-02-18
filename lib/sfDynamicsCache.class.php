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
    $options = array_merge($options, array('cache_dir'=>self::getCacheDir()));

    parent::initialize($options);
  }

  /**
   * getCacheDir - gives the normal cache path (full filesystem path)
   *
   * @return string
   */
  static public function getCacheDir()
  {
    return sfConfig::get('sf_cache_dir').DIRECTORY_SEPARATOR.'sfDynamicsPlugin';
  }

  /**
   * getSuperCacheDir - give the directory where supercache of minified assets are stored
   *
   * @param  boolean $full if true, gives the filesystem path. If false, gives the web path
   * @return string
   */
  static public function getSuperCacheDir($full=false)
  {
    return ($full?sfConfig::get('sf_web_dir'):'').sfConfig::get('app_sfDynamicsPlugin_supercache_web_path', DIRECTORY_SEPARATOR.'dynamics');
  }

  /**
   * clearSuperCache - clear supercache of minified assets
   *
   * @param  sfEvent $event
   * @return void
   */
  static public function clearSuperCache(sfEvent $event)
  {
    $event->getSubject()->logSection('cache', 'Clearing sfDynamicsPlugin minified assets super cache');

    if (is_dir($cacheDir = self::getSuperCacheDir(true)))
    {
      $event->getSubject()->getFilesystem()->remove(sfFinder::type('file')->ignore_version_control()->discard('.sf')->in($cacheDir));
    }
  }
}

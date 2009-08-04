<?php

/**
 * sfDynamicsRouting - configures the routing for the plugin
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license MIT
 */
class sfDynamicsRouting
{
  /**
   * sfDynamicsRouting::PLUGIN_NAME - this is the namespace for app.yml configuration
   */
  const PLUGIN_NAME = 'sfDynamicsPlugin';

  /**
   * sfDynamicsRouting::ROUTE - prefix for all routes comming from sfDynamicsPlugin
   */
  const ROUTE       = 'sfDynamics';

  /**
   * Available asset types, and their associated extensions.
   */
  static protected $types = array(
    'javascript' => 'js',
    'stylesheet' => 'css'
    );

  /**
   * Stores whether routes use sfRoute objects or not.
   */
  static protected $newStyleRoutes;

  /**
   * checkSymfonyVersion - checks if symfony version used is compatible with the
   * plugin, and detect whether routes are new style (using sfRoute objects) or
   * old style.
   *
   * @return void
   */
  static protected function checkSymfonyVersion()
  {
    if (defined('SYMFONY_VERSION'))
    {
      list($sfVersionMajor, $sfVersionMinor, $sfVersionRelease) = explode('.', SYMFONY_VERSION);

      if (($sfVersionMajor!=1) || (!in_array($sfVersionMinor, array(2,3))))
      {
        throw new sfConfigurationException(self::PLUGIN_NAME.' needs symfony 1.2 to 1.3 to run.');
      }

      self::$newStyleRoutes = (bool)($sfVersionMinor>1);
    }
    else
    {
      throw new sfConfigurationException(self::PLUGIN_NAME.' needs symfony 1.2 to 1.3 to run, but no version were found.');
    }
  }

  /**
   * addRoute - prepend a route to given routing object with abstraction of
   * symfony version
   *
   * @param  sfRouting $r
   * @param  string $routeName
   * @param  string $routeUrl
   * @param  array $routeParameters
   * @return void
   */
  static protected function addRoute(sfRouting $r, $routeName, $routePattern, $routeDefaults, $routeRequirements=array(), $routeOptions=array())
  {
    if (self::$newStyleRoutes)
    {
      $r->prependRoute(self::ROUTE.'_'.$routeName, new sfRoute($routePattern, $routeDefaults, $routeRequirements, $routeOptions));
    }
    else
    {
      $r->prependRoute(self::ROUTE.'_'.$routeName, $routePattern, $routeDefaults, $routeRequirements);
    }
  }

  /**
   * configure - configures the routing when main project will load it
   *
   * @listen routing.load_configuration
   *
   * @param  sfEvent $e
   * @return void
   */
  static public function configure(sfEvent $event)
  {
    self::checkSymfonyVersion();

    $routing = $event->getSubject();
    $prefix = sfConfig::get('app_'.self::PLUGIN_NAME.'_base_route', '/dynamics');

    /*foreach (self::$types as $actionName => $fileExtension)
    {*/
      self::addRoute(
        $routing,
        'asset',
        $prefix.'/:name',
        array(
          'module' => 'sfDynamics',
          'action' => 'asset'
        ),
        array(),
        array('segment_separators' => array('/'))
      );

      foreach (array('language'=>'/l/:language', 'theme'=>'/t/:theme', 'language_theme'=> '/l/:language/t/:theme') as $routeName => $routeEnhancer)
      {
        self::addRoute(
          $routing,
          'asset_'.$routeName,
          $prefix.$routeEnhancer.'/:name.',
          array(
            'module' => 'sfDynamics',
            'action' => 'asset'
          ),
          array(),
          array('segment_separators' => array('/'))
        );
      }
    /*}*/
  }

  /**
   * uri_for - builds a symfony URI from an asset name and its extension
   *
   * @param  string $name
   * @param  string $extension
   * @return string
   */
  static public function uri_for($name, $extension)
  {
    $translator = array_flip(self::$types);

    if (!isset($translator[$extension]))
    {
      throw new sfConfigurationException('Invalid asset type');
    }

    return sprintf('@%s_asset?name=%s.%s', self::ROUTE, $name, $extension);
  }

  /**
   * supercache_for - build supercache filename or given packagelist and extension
   *
   * @param  string $packages
   * @param  string $extension
   * @return string
   */
  static public function supercache_for($packages, $extension)
  {
    $cacheKey = '';

    foreach ($packages as $package)
    {
      $cacheKey .= $package->getCacheKey();
    }

    return sfDynamicsCache::getSuperCacheDir().'/'.md5($cacheKey).'.'.$extension;
  }
}

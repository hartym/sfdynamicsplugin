<?php

class sfDynamicsRouting
{
  const ROUTE = 'sfDynamics';

  static protected $types = array(
    'javascript'=>'js',
    'stylesheet'=>'css'
    );

  static public function configure(sfEvent $e)
  {
    $r = $e->getSubject();
    $prefix = sfConfig::get('app_sfDynamicsPlugin_base_route', '/dynamics');

    foreach (self::$types as $actionName => $fileExtension)
    {
      $r->prependRoute(self::ROUTE.'_'.$actionName, new sfRoute($prefix.'/:name.'.$fileExtension, array('module' => 'sfDynamics', 'action'=>$actionName)));

      foreach (array('language'=>'/l/:language', 'theme'=>'/t/:theme', 'language_theme'=> '/l/:language/t/:theme') as $routeName => $routeEnhancer)
      {
        $r->prependRoute(self::ROUTE.'_'.$actionName.'_'.$routeName, new sfRoute($prefix.$routeEnhancer.'/:name.'.$fileExtension, array('module' => 'sfDynamics', 'action'=>$actionName)));
      }
    }
  }

  static public function uri_for($name, $extension)
  {
    $translator = array_flip(self::$types);

    if (!isset($translator[$extension]))
    {
      throw new sfConfigurationException('Invalid asset type');
    }

    return '@'.self::ROUTE.'_'.$translator[$extension].'?name='.str_replace('.', '-', $name);
  }

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

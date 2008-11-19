<?php

/**
 * sfDynamicsPlugin configuration.
 *
 * @package     sfDynamicsPlugin
 * @subpackage  config
 * @author      Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @version     SVN: $Id: PluginConfiguration.class.php 12628 2008-11-04 14:43:36Z Kris.Wallsmith $
 */
class sfDynamicsPluginConfiguration extends sfPluginConfiguration
{
  const ROUTE = 'sfDynamics';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->dispatcher->connect('routing.load_configuration', array($this, 'configureRouting'));
  }

  public function configureRouting(sfEvent $e)
  {
    $r = $e->getSubject();
    $prefix = sfConfig::get('app_sfDynamicsPlugin_base_route', '/dynamics');

    foreach (array('javascript'=>'js', 'stylesheet'=>'css') as $actionName => $fileExtension)
    {
      $r->prependRoute(self::ROUTE.'_'.$actionName, new sfRoute($prefix.'/:name.'.$fileExtension, array('module' => 'sfDynamics', 'action'=>$actionName)));

      foreach (array('language'=>'/l/:language', 'theme'=>'/t/:theme', 'language_theme'=> '/l/:language/t/:theme') as $routeName => $routeEnhancer)
      {
        $r->prependRoute(self::ROUTE.'_'.$actionName.'_'.$routeName, new sfRoute($prefix.$routeEnhancer.'/:name.'.$fileExtension, array('module' => 'sfDynamics', 'action'=>$actionName)));
      }
    }
  }
}

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

  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();

    if (0)
    {
      $r->prependRoute('sfDynamicsPlugin_javascript', '/sf_dynamics/js/:name', array('module' => 'sfDynamics', 'action'=>'javascript'));
      $r->prependRoute('sfDynamicsPlugin_stylesheet', '/sf_dynamics/css/:name', array('module' => 'sfDynamics', 'action'=>'stylesheet'));
    }
    else
    {
      $r->prependRoute('sfDynamicsPlugin_javascript', new sfRoute('/dynamics/:name.js', array('module' => 'sfDynamics', 'action'=>'javascript')));
      $r->prependRoute('sfDynamicsPlugin_stylesheet', new sfRoute('/dynamics/:name.css', array('module' => 'sfDynamics', 'action'=>'stylesheet')));
    }
  }
}

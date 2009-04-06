<?php
$this->dispatcher->connect('routing.load_configuration', array('sfDynamicsRouting', 'configure'));
$this->dispatcher->connect('task.cache.clear', array('sfDynamicsCache', 'clearSuperCache'));

if (!is_writeable(sfDynamicsCache::getSuperCacheDir(true)))
{
  $_superCacheDir = sfDynamicsCache::getSuperCacheDir(true);

  if (false!==strpos($_superCacheDir, '..'))
  {
    throw new sfConfigurationException('sfDynamicsPlugin supercache directory does not exists, and contains «..» components. Please check your configuration, or that «'.$_superCacheDir.'» is writeable.');
  }

  mkdir($_superCacheDir, 0777, true);
  chmod($_superCacheDir, 0777);
}

require_once dirname(__FILE__).'/../lib/debug/sfWebDebugPanelDynamics.class.php';

$this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelDynamics', 'listenToLoadPanelEvent'));


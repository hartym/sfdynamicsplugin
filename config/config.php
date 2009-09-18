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

  if (!@mkdir($_superCacheDir, 0777, true))
  {
    throw new Exception(sprintf('Could not create dir «%s», check the permissions', $_superCacheDir));
  }
  if (!@chmod($_superCacheDir, 0777))
  {
    throw new Exception(sprintf('Could not change owner dir «%s», check the permissions', $_superCacheDir));
  }
}


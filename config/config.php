<?php
$this->dispatcher->connect('routing.load_configuration', array('sfDynamicsRouting', 'configure'));
$this->dispatcher->connect('task.cache.clear', array('sfDynamicsCache', 'clearSuperCache'));

if (!is_writeable(sfDynamicsCache::getSuperCacheDir(true)))
{
  throw new sfConfigurationException('sfDynamicsPlugin supercache directory does not exists or is not writeable.'."\n\n".'Please check that «'.sfDynamicsCache::getSuperCacheDir(true).'» is writeable.');
}

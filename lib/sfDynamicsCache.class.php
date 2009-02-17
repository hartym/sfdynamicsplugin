<?php

class sfDynamicsCache extends sfFileCache
{
  public function initialize($options = array())
  {
    $options = array_merge($options, array('cache_dir'=>sfConfig::get('sf_cache_dir').'/sfDynamicsPlugin'));

    parent::initialize($options);
  }
}

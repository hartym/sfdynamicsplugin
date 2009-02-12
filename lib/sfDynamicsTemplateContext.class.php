<?php

class sfDynamicsTemplateContext
{
  protected
    $behavior      = null,
    $configuration = null,
    $parameters    = array();

  public function __construct($behavior)
  {
    $this->behavior = $behavior;
    $this->configuration = sfDynamics::getManager()->getConfiguration($behavior);
  }

  public function getPartial($name)
  {
  }

  public function includePartial($name)
  {
  }

  public function offsetGet($key)
  {
    return $this->parameters[$key];
  }

  public function offsetSet($key, $value)
  {
    $this->parameters[$key] = $value;
  }

  public function offsetUnset($key)
  {
    unset($this->parameters[$key]);
  }

  public function offsetExists($key)
  {
    return isset($this->parameters[$key]);
  }
}

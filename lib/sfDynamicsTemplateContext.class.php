<?php

/**
 * sfDynamicsTemplateContext
 *
 * @TODO find out what this class was meant to do...
 *
 * @package   sfDynamicsPlugin
 * @version   SVN: $Id: $
 * @author    Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license
 */
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

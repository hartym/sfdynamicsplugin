<?php

class sfDynamicsDefinitionReference
{
  protected
    $class     = null,
    $bootstrap = null;

  public function __construct($class, $bootstrap)
  {
    $this->class     = $class;
    $this->bootstrap = $bootstrap;
  }

  public function dereference()
  {
    $class = $this->class;

    return new $class(null, $this->bootstrap);
  }

  static public function __set_state($bootstrap)
  {
    return new self($bootstrap['class'], $bootstrap['bootstrap']);
  }
}

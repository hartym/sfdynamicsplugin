<?php

class sfDynamicsDefinitionReference
{
  static protected
    $_configuration = null;

  protected
    $_class     = null,
    $_bootstrap = null,
    $_instance  = null;

  public function __construct($class, $bootstrap)
  {
    $this->_class     = $class;
    $this->_bootstrap = $bootstrap;
  }

  protected function __build()
  {
    if (is_null($this->_instance))
    {
      if (is_null(self::$_configuration))
      {
        self::$_configuration = sfContext::getInstance()->getConfiguration();
      }

      $class = $this->_class;
      $this->_instance = new $class(self::$_configuration, null, $this->_bootstrap);
    }
  }

  static public function __set_state($bootstrap)
  {
    if ((!isset($bootstrap['class']) )|| (!isset($bootstrap['bootstrap'])))
    {
      echo '<pre>';
      print_r($bootstrap);
      echo '</pre>';
      die();
    }

    return new self($bootstrap['class'], $bootstrap['bootstrap']);
  }

  public function __call($method, $parameters)
  {
    $this->__build();

    return call_user_func_array(array($this->_instance, $method), $parameters);
  }

  public function __set($key, $value)
  {
    $this->__build();

    $this->_instance->{$key} = $value;
  }

  public function __get($key)
  {
    $this->__build();

    return $this->_instance->{$key};
  }
}

<?php

abstract class sfDynamicsBaseDefinition
{
  protected $configuration = null;

  public function __construct(sfApplicationConfiguration $configuration, $xml=null, $bootstrap=null)
  {
    $this->configuration = $configuration;

    if (!is_null($bootstrap))
    {
      $this->bootstrap($bootstrap);
    }

    if (!is_null($xml))
    {
      $this->parseXml($xml);
    }
  }

  public function bootstrap($bootstrap)
  {
    foreach ($bootstrap as $variable => $value)
    {
      $this->$variable = $this->dereference($value);
    }
  }

  public function dereference($value)
  {
    if (is_array($value))
    {
      foreach($value as $_key => $_data)
      {
        $value[$_key] = $this->dereference($_data);
      }
    }
    elseif(is_object($value) && is_callable(array($value, 'dereference')))
    {
      $value = $value->dereference();
    }

    return $value;
  }

  public function getBootstrapFor($entries)
  {
    $bootstrap = array();

    foreach ($entries as $key => $entry)
    {
      $bootstrap[$key] = new sfDynamicsDefinitionReference(get_class($entry), $entry->getBootstrapArray());
    }

    return $bootstrap;
  }

  static public function getAsPhp($data)
  {
    return var_export($data, 1);
    return preg_replace('/\s+/m', ' ', var_export($data, 1));
  }

  abstract function getBootstrapArray();
  abstract function parseXml($xml);

}


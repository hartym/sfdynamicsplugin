<?php

abstract class sfDynamicsBaseDefinition
{
  public function __construct($xml=null)
  {
    if (!is_null($xml))
    {
      $this->parseXml($xml);
    }
  }

  public function getConfigPaths($resource)
  {
    try
    {
      return sfContext::getInstance()->getConfiguration()->getConfigPaths($resource);
    }
    catch (Exception $e)
    {
      return array($resource);
    }
  }

  static public function build($instance, $definition, $state)
  {
    foreach ($definition as $variable)
    {
      if (isset($state[$variable]))
      {
        $instance->{'set'.ucfirst($variable)}($state[$variable]);
        unset($state[$variable]);
      }
    }

    if (!empty($state))
    {
      throw new sfConfigurationException(sprintf('Could not create %s instance with bootstrap data: extra fields given (%s)', get_class($instance), implode(', ', array_keys($state))));
    }

    return $instance;
  }

  public function parseXml($xml)
  {
    if (is_string($xml))
    {
      $xml = simplexml_load_string($xml);
    }

    return $xml;
  }
}


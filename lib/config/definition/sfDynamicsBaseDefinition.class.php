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

  public function __call($method, $args)
  {
    switch(substr($method, 0, 3))
    {
      case 'set':
        $property = sfInflector::underscore(substr($method, 3));
        return $this->$property = $args[0];

      case 'get':
        $property = sfInflector::underscore(substr($method, 3));
        return $this->$property;

      default:
        throw new sfException('Undefined method "'.$method.'"');
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
      if (array_key_exists($variable, $state))
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

  protected function parsePath($path)
  {
    return preg_replace_callback('/%([a-z0-9_]+)%/i', array($this, 'getPathConfigValueCallback'), $path);
  }

  public function getPathConfigValueCallback($matches)
  {
    return sfConfig::get($matches[1]);
  }

  static public function getElementName(SimpleXMLElement $element)
  {
    if (method_exists($element, 'getName'))
    {
      return $element->getName();
    }
    else
    {
      $dom = dom_import_simplexml($element);
      return $dom->nodeName;
    }
  }
}


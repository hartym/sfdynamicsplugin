<?php

/**
 * sfDynamicsAssetCollectionDefinition
 *
 * @package    sfDynamicsPlugin
 * @subpackage configuration
 * @version    SVN: $Id: $
 * @author     Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license    MIT License
 */
class sfDynamicsAssetCollectionDefinition extends sfDynamicsBaseDefinition
{
  protected
    $stylesheets = array(),
    $javascripts = array();

  public function hasStylesheets()
  {
    return !empty($this->stylesheets);
  }

  public function getJavascripts()
  {
    return $this->javascripts;
  }

  public function setStylesheets($stylesheets)
  {
    $this->stylesheets = $stylesheets;
  }

  public function hasJavascripts()
  {
    return !empty($this->javascripts);
  }

  public function getStylesheets()
  {
    return $this->stylesheets;
  }

  public function setJavascripts($javascripts)
  {
    $this->javascripts = $javascripts;
  }


  public function parseXml($xml)
  {
    $xml = parent::parseXml($xml);

    if (isset($xml->javascript))
    {
      foreach ($xml->javascript as $index => $javascript)
      {
        $this->javascripts[] = (string)$javascript;
      }
    }

    if (isset($xml->stylesheet))
    {
      foreach ($xml->stylesheet as $index => $stylesheet)
      {
        $this->stylesheets[] = (string)$stylesheet;
      }
    }

    return $xml;
  }

  static public function __set_state($state)
  {
    return self::build(new self(), array('javascripts', 'stylesheets'), $state);
  }
}

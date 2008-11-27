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

  public function getJavascripts()
  {
    return $this->javascripts;
  }

  public function getStylesheets()
  {
    return $this->stylesheets;
  }

  public function setJavascripts($javascripts)
  {
    $this->javascripts = $javascripts;
  }

  public function setStylesheets($stylesheets)
  {
    $this->stylesheets = $stylesheets;
  }

  public function parseXml($xml)
  {
    foreach ($xml->javascript as $index => $javascript)
    {
      $this->javascripts[] = (string)$javascript;
    }

    foreach ($xml->stylesheet as $index => $stylesheet)
    {
      $this->stylesheets[] = (string)$stylesheet;
    }
  }

  static public function __set_state($state)
  {
    return self::build(new self(), array('javascripts', 'stylesheets'), $state);
  }
}

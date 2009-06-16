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

  public function getCacheKey()
  {
    return
      'js => '.implode(';', $this->getJavascripts()).' '.
      'css => '.implode(';', $this->getStylesheets()).' '.
      'options => jspacker:'.(sfDynamicsConfig::isJavascriptPackerEnabled($this)?'yes':'no').
                ' jsminifier:'.(sfDynamicsConfig::isJavascriptMinifierEnabled($this)?'yes':'no').
                ' csstidy:'.(sfDynamicsConfig::isStylesheetTidyEnabled($this)?'yes':'no');
  }

  public function hasStylesheets()
  {
    return !empty($this->stylesheets);
  }

  public function hasJavascripts()
  {
    return !empty($this->javascripts);
  }

  public function parseXml($xml)
  {
    $xml = parent::parseXml($xml);

    if (isset($xml->javascript))
    {
      foreach ($xml->javascript as $index => $javascript)
      {
        $this->javascripts[] = new sfDynamicsJavascriptDefinition($javascript);
      }
    }

    if (isset($xml->stylesheet))
    {
      foreach ($xml->stylesheet as $index => $stylesheet)
      {
        $this->stylesheets[] = new sfDynamicsStylesheetDefinition($stylesheet);
      }
    }

    return $xml;
  }

  static public function __set_state($state)
  {
    return self::build(new self(), array('javascripts', 'stylesheets'), $state);
  }
}

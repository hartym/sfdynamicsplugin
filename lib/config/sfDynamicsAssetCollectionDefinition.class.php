<?php

class sfDynamicsAssetCollectionDefinition extends sfDynamicsBaseDefinition
{
  protected
    $stylesheets = array(),
    $javascripts = array();

  public function getBootstrapArray()
  {
    return array(
      'javascripts' => $this->javascripts,
      'stylesheets' => $this->stylesheets,
    );
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
}

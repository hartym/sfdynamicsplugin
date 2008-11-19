<?php

class sfDynamicsPackageDefinition extends sfDynamicsAssetCollectionDefinition
{
  protected
    $description = '',
    $requires    = array(),
    $conflicts   = array(),
    $i18n        = array(),
    $themes      = array();


  public function parseXml($xml)
  {
    // javascript, stylesheet
    parent::parseXml($xml);

    // description
    if (count($xml->description) > 1)
    {
      throw new sfConfigurationException('A package cannot have more than one description node.');
    }
    foreach ($xml->description as $description)
    {
      $this->description = (string)$description;
    }

    foreach ($xml->require as $require)
    {
      $this->requires[] = (string)$require;
    }

    foreach ($xml->conflict as $conflict)
    {
      $this->conflicts[] = (string)$conflict;
    }

    foreach ($xml->i18n as $i18n)
    {
      if (!strlen($language = (string)$i18n['language']))
      {
        throw new sfConfigurationException('Each I18n tag should have a language attribute.');
      }

      $this->i18n[$language] = new sfDynamicsAssetCollectionDefinition($this->configuration, $i18n);
    }

    if (count($xml->theme))
    {
      $hasDefault = false;

      foreach ($xml->theme as $theme)
      {
        if (!strlen($themeName = (string)$theme['name']))
        {
          throw new sfConfigurationException('Each theme tag should have a name attribute.');
        }

        $this->themes[$themeName] = new sfDynamicsAssetCollectionDefinition($this->configuration, $theme);
      }
    }
  }

  public function getBootstrapArray()
  {
    return array(
      'description' => $this->description,
      'requires'    => $this->requires,
      'conflicts'   => $this->conflicts,
      'i18n'        => $this->getBootstrapFor($this->i18n),
      'themes'      => $this->getBootstrapFor($this->themes),
    );
  }
}


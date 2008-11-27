<?php

class sfDynamicsPackageDefinition extends sfDynamicsAssetCollectionDefinition
{
  protected
    $description = '',
    $requires    = array(),
    $conflicts   = array(),
    $i18n        = array(),
    $themes      = array();

  public function getDependencies()
  {
    return $this->requires;
  }

  public function setDescription($description)
  {
    $this->description = $description;
  }

  public function setRequires($requires)
  {
    $this->requires = $requires;
  }

  public function setConflicts($conflicts)
  {
    $this->conflicts = $conflicts;
  }

  public function setI18n($i18n)
  {
    $this->i18n = $i18n;
  }

  public function setThemes($themes)
  {
    $this->themes = $themes;
  }

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

      $this->i18n[$language] = new sfDynamicsAssetCollectionDefinition($i18n);
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

        $this->themes[$themeName] = new sfDynamicsAssetCollectionDefinition($theme);
      }
    }
  }

  static public function __set_state($state)
  {
    return self::build(new self(), array('javascripts', 'stylesheets', 'description', 'requires', 'conflicts', 'i18n', 'themes'), $state);
  }
}


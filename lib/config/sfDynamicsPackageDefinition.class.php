<?php

class sfDynamicsPackageDefinition extends sfDynamicsAssetCollectionDefinition
{
  protected
    $description = '',
    $paths       = array(),
    $requires    = array(),
    $conflicts   = array(),
    $i18n        = array(),
    $themes      = array();


  public function __construct($xml=null, $paths=array())
  {
    $this->paths = array_merge($paths, array(sfConfig::get('sf_data_dir'), sfConfig::get('sf_plugins_dir').'/sfDynamicsPlugin/data'));

    parent::__construct($xml);
  }

  public function setPaths($paths)
  {
    $this->paths = $paths;
  }

  public function getPaths()
  {
    return array_merge(array(sfConfig::get('sf_app_dir').'/data'), $this->paths);
  }

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
    $xml = parent::parseXml($xml);

    if (isset($xml->description))
    {
      if (count($xml->description) > 1)
      {
        throw new sfConfigurationException('A package cannot have more than one description node.');
      }
      foreach ($xml->description as $description)
      {
        $this->description = (string)$description;
      }
    }

    if (isset($xml->require))
    {
      foreach ($xml->require as $require)
      {
        $this->requires[] = (string)$require;
      }
    }

    if (isset($xml->conflict))
    {
      foreach ($xml->conflict as $conflict)
      {
        $this->conflicts[] = (string)$conflict;
      }
    }

    if (isset($xml->i18n))
    {
      foreach ($xml->i18n as $i18n)
      {
        if (!strlen($language = (string)$i18n['language']))
        {
          throw new sfConfigurationException('Each I18n tag should have a language attribute.');
        }

        $this->i18n[$language] = new sfDynamicsAssetCollectionDefinition($i18n);
      }
    }

    if (isset($xml->theme))
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

    return $xml;
  }

  static public function __set_state($state)
  {
    return self::build(new self(), array('javascripts', 'stylesheets', 'description', 'requires', 'conflicts', 'i18n', 'themes', 'paths'), $state);
  }
}


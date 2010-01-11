<?php

/**
 * sfDynamicsConfigDefinition
 *
 * @package    sfDynamicsPlugin
 * @subpackage configuration
 * @version    SVN: $Id: $
 * @author     Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license    MIT License
 */
class sfDynamicsPluginConfigDefinition extends sfDynamicsConfigDefinition
{
  protected
    $pluginName = null,
    $pluginPath = null;

  public function __construct($xml=null, $pluginName, $pluginPath)
  {
    $this->pluginName = $pluginName;
    $this->pluginPath = $pluginPath;

    parent::__construct($xml);
  }

  /**
   * parseXml
   *
   * @param mixed $xml
   * @return void
   */
  public function parseXml($xml)
  {
    if ($xml->getName()!='dynamics-plugin')
    {
      throw new sfConfigurationException(sprintf('Invalid dynamics-plugin.xml file for plugin "%s". Root note should be a dynamics-plugin tag.', $this->pluginName));
    }

    foreach ($xml->import as $import)
    {
      $resource = 'config/'.(string)$import['resource'];
      $this->doImport($resource);
    }

    foreach ($xml->package as $package)
    {
      $packageName = (string)$package['name'];

      $this->doPackage($this->pluginName.'.'.$packageName, $package, array($this->pluginPath.'/data'));
    }
  }

  static public function __set_state($state)
  {
    return self::build(new self(), array('imports', 'packages'), $state);
  }
}


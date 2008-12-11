<?php

class sfDynamicsConfigHandler extends sfConfigHandler
{
  public function execute($configFiles)
  {
    $result = $this->parseXmls($configFiles);

    return sprintf('<?php return %s;', var_export($result, 1));
  }

  public function parseXmls($configFiles)
  {
    $configFile = array_pop($configFiles);

    if (file_exists($configFile))
    {
      if (!(is_file($configFile)&&is_readable($configFile)))
      {
        throw new sfException('Configuration file '.$configFile.' is present but not readable or not a regular file.');
      }
    }

    $config = new sfDynamicsConfigDefinition(simplexml_load_file($configFile));

    foreach (sfContext::getInstance()->getConfiguration()->getPluginPaths() as $pluginPath)
    {
      $pluginName = basename($pluginPath);
      $pluginConfigFile = $pluginPath.'/config/dynamics-plugin.xml';

      if (file_exists($pluginConfigFile))
      {
        if ((!is_file($pluginConfigFile)) || (!is_readable($pluginConfigFile)))
        {
          throw new sfConfigurationException('Dynamics extension file exists but is not a regular file or is not readable.');
        }

        $config->merge(new sfDynamicsPluginConfigDefinition(simplexml_load_file($pluginConfigFile), $pluginName, $pluginPath));
      }
    }

    return $config;
  }
}


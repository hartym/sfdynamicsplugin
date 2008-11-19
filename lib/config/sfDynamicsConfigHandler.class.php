<?php

class sfDynamicsConfigHandler extends sfConfigHandler
{
  public function execute($configFiles)
  {
    $config = $this->parseXmls($configFiles);

    $code = sprintf('<?php return %s;', var_export($config, 1));

    return $code;
  }

  public function parseXmls($files)
  {
    $file = array_pop($files);

    if (file_exists($file))
    {
      if (!(is_file($file)&&is_readable($file)))
      {
        throw new sfException('Configuration file '.$file.' is present but not readable or not a regular file.');
      }
    }

    $config = new sfDynamicsConfigDefinition(sfContext::getInstance()->getConfiguration(), simplexml_load_file($file));

    return $config->getBootstrapArray();
  }
}


<?php

class sfDynamicsConfigHandler extends sfConfigHandler
{
  public function execute($configFiles)
  {
    return sprintf('<?php return %s;', var_export($this->parseXmls($configFiles), 1));
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

    return new sfDynamicsConfigDefinition(simplexml_load_file($file));
  }
}


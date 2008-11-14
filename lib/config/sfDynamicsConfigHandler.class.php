<?php

class sfDynamicsConfigHandler extends sfYamlConfigHandler
{
  public function execute($configFiles)
  {
    // retrieve yaml data
    $config = $this->parseYamls($configFiles);

    $code = sprintf('<?php return %s;', var_export($config, 1));

    return $code;
  }
}

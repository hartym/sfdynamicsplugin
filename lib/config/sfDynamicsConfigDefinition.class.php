<?php

class sfDynamicsConfigDefinition extends sfDynamicsBaseDefinition
{
  protected
    $imports  = array(),
    $packages = array();

  public function parseXml($xml)
  {
    if ($xml->getName()!='dynamics')
    {
      throw new sfConfigurationException('Invalid config');
    }

    foreach ($xml->import as $import)
    {
      $resource = 'config/'.(string)$import['resource'];
      $this->doImport($resource);
    }

    foreach ($xml->package as $package)
    {
      $packageName = (string)$package['name'];

      $this->doPackage($packageName, $package);
    }
  }

  public function doPackage($packageName, $xml)
  {
    $this->packages[$packageName] = new sfDynamicsPackageDefinition($this->configuration, $xml);
  }

  public function doImport($resource)
  {
    if (isset($this->imports[$resource]))
    {
      throw new sfConfigurationException($this->imports[$resource] ? 'Resource «'.$resource.'» is already imported.' : 'Resource «'.$resource.'» has a recursive import clause.');
    }

    $_files = $this->configuration->getConfigPaths($resource);

    if (count($_files))
    {
      $_file = array_pop($_files);

      $_xml = simplexml_load_file($_file);

      $this->imports[$resource] = false;
      $_config = new self($this->configuration, $_xml);
      $this->merge($_config);
      $this->imports[$resource] = true;
    }
  }

  public function merge($config)
  {
    $this->imports  = array_merge($this->imports, $config->getImports());
    $this->packages = array_merge($this->packages, $config->getPackages());
  }

  public function getImports()
  {
    return $this->imports;
  }

  public function getPackages()
  {
    return $this->packages;
  }

  public function getBootstrapArray()
  {
    return array(
      'imports' => $this->imports,
      'packages' => $this->getBootstrapFor($this->packages),
    );
  }
}


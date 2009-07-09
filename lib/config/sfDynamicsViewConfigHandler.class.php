<?php

/**
 * sfDynamicsViewConfigHandler
 *
 * @package    sfDynamicsPlugin
 * @subpackage config
 * @version    SVN: $Id: $
 * @author     Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license    MIT License
 */
class sfDynamicsViewConfigHandler extends sfViewConfigHandler
{
  /**
   * Extend the sfViewConfigHandler's addHtmlAsset to allow using the dynamics section.
   *
   * @param  string $viewName  can be "all" or "indexSuccess" for example
   * @return string
   */
  protected function addHtmlAsset($viewName = '')
  {
    $data = parent::addHtmlAsset($viewName);

    $packages = $this->mergeConfigValue('dynamics', $viewName);

    if (!is_array($packages))
    {
      throw new sfConfigurationException(sprintf('"dynamics" directive in view.yml must contain an array of packages.%sExample:  dynamics: [package1, package2]', PHP_EOL.PHP_EOL));
    }

    if (count($packages))
    {

      $packagesPhp = '';
      foreach ($packages as $package)
      {
        if (isset($packagesPhp[0]))
        {
          $packagesPhp .= ', ';
        }

        $packagesPhp .= sprintf('\'%s\'', $package);
      }

      return $data.sprintf('  sfDynamics::load(%s);%s', $packagesPhp, PHP_EOL);
    }
    else
    {
      return $data;
    }
  }
}

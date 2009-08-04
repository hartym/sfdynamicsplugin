<?php

class sfDynamicsActions extends sfActions
{
  public function executeAsset($request)
  {
    try
    {
      $name = $this->getRequest()->getParameter('name');
      $this->forward404Unless(sfDynamicsPackageDefinition::checkIsValidPackageName($name));

      $extensionPosition = strrpos($name, '.');
      $assetExtension = substr($name, $extensionPosition+1);
      $assetType = sfDynamics::getTypeFromExtension($assetExtension);
      $name = substr($name, 0, $extensionPosition);

      $this->package = sfDynamics::getManager()->getPackage($name);

      $this->{'pre'.ucfirst($assetType)}();

      return $this->renderText(sfDynamics::getRenderer()->getAsset($name, $this->package, $assetType, $assetExtension));
    }
    catch (Exception $e)
    {
      throw new sfError404Exception($e->getMessage());
    }
  }

  protected function preJavascript()
  {
    $this->forward404Unless(count($this->package->getJavascripts()));
    $this->forward404Unless(count($this->package->getPaths()));

    $this->getResponse()->setContentType('text/javascript');

  }

  protected function preStylesheet()
  {
    $this->forward404Unless(count($this->package->getStylesheets()));
    $this->forward404Unless(count($this->package->getPaths()));

    $this->getResponse()->setContentType('text/css');
  }
}

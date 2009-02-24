<?php

class sfDynamicsActions extends sfActions
{
  public function preExecute()
  {
    $this->path = sfConfig::get('sf_plugins_dir').'/sfDynamicsPlugin/data'; // default, should change
    $this->manager = sfDynamics::getManager();

    try
    {
      $this->name = str_replace('-', '.', $this->getRequest()->getParameter('name'));
      $this->forward404Unless(preg_match('/[a-z0-9.]+$/', $this->name));

      $this->package = $this->manager->getPackage($this->name);
    }
    catch (Exception $e)
    {
      throw new sfError404Exception($e->getMessage());
    }
  }

  /**
   * executeJavascript
   *
   * @param mixed $request
   * @return void
   */
  public function executeJavascript($request)
  {
    $this->forward404Unless(count($this->package->getJavascripts()));
    $this->forward404Unless(count($this->package->getPaths()));

    $this->getResponse()->setContentType('text/javascript');

    $renderer = sfDynamics::getRenderer();

    return $this->renderText($renderer->getAsset($this->name, $this->package, 'javascript', 'js'));
  }

  /**
   * executeStylesheet
   *
   * @param mixed $request
   * @return void
   */
  public function executeStylesheet($request)
  {
    $this->forward404Unless(count($stylesheets = $this->package->getStylesheets()));
    $this->forward404Unless(count($paths = $this->package->getPaths()));

    $this->getResponse()->setContentType('text/css');

    $renderer = sfDynamics::getRenderer();
    return $this->renderText($renderer->getAsset($this->name, $this->package, 'stylesheet', 'css'));
  }
}

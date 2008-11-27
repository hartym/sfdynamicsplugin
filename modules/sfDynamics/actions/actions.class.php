<?php

class sfDynamicsActions extends sfActions
{
  public function preExecute()
  {
    $this->path = sfConfig::get('sf_plugins_dir').'/sfDynamicsPlugin/data';
    $this->manager = sfDynamics::getManager();

    try
    {
      $this->name = $this->getRequest()->getParameter('name');
      $this->package = $this->manager->getPackage($this->name);
    }
    catch(Exception $e)
    {
      throw new sfError404Exception($e->getMessage());
    }
  }

  public function executeJavascript($request)
  {
    $this->forward404Unless(count($javascripts = $this->package->getJavascripts()));

    $this->getResponse()->setContentType('text/javascript');
    $js = '';

    foreach ($javascripts as $javascript)
    {
      $file = $this->path.'/js/'.$javascript.'.js';

      if (!file_exists($file) || !is_readable($file) || !is_file($file))
      {
        throw new sfError404Exception('Unreadable asset file «'.$file.'»');
      }

      $js .= file_get_contents($file)."\n";
    }

    if (!sfConfig::get('sf_debug'))
    {
      $packer = new JavaScriptPacker($js, 'Normal', true, false);
      $js = $packer->pack();
    }

    return $this->renderText($js);
  }

  public function executeStylesheet($request)
  {
    $this->forward404Unless(count($stylesheets = $this->package->getStylesheets()));

    $this->getResponse()->setContentType('text/css');
    $css = '';

    foreach ($stylesheets as $stylesheet)
    {
      $file = $this->path.'/css/'.$stylesheet.'.css';

      if (!file_exists($file) || !is_readable($file) || !is_file($file))
      {
        throw new sfError404Exception('Unreadable asset file «'.$file.'»');
      }

      $css .= file_get_contents($file)."\n";
    }

    if (!sfConfig::get('sf_debug'))
    {
      $css = preg_replace('/\s+/m', ' ', $css);
    }

    return $this->renderText($css);
  }
}

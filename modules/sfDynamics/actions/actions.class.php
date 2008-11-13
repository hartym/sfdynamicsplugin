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
      $this->configuration = $this->manager->getConfiguration($this->name);
    }
    catch(Exception $e)
    {
      throw new sfError404Exception($e->getMessage());
    }
  }

  public function executeJavascript($request)
  {
    $this->getResponse()->setContentType('text/javascript');

    $js = '';

    if (!isset($this->configuration['javascripts']) || !is_array($this->configuration['javascripts']))
    {
      throw new sfError404Exception();
    }

    foreach ($this->configuration['javascripts'] as $javascript)
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
    $this->getResponse()->setContentType('text/css');

    $css = '';

    if (!isset($this->configuration['stylesheets']) || !is_array($this->configuration['stylesheets']))
    {
      throw new sfError404Exception();
    }

    foreach ($this->configuration['stylesheets'] as $stylesheet)
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

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

  public function executeJavascript($request)
  {
    $this->forward404Unless(count($javascripts = $this->package->getJavascripts()));
    $this->forward404Unless(count($paths = $this->package->getPaths()));

    $this->getResponse()->setContentType('text/javascript');

    $result = $this->getConcatenatedAssets('js', $paths, $javascripts);

    if (!sfConfig::get('sf_debug'))
    {
      $result = JSMin::minify($result);
    }

    return $this->renderText($result);
  }

  public function executeStylesheet($request)
  {
    $this->forward404Unless(count($stylesheets = $this->package->getStylesheets()));
    $this->forward404Unless(count($paths = $this->package->getPaths()));

    $this->getResponse()->setContentType('text/css');

    $result = $this->getConcatenatedAssets('css', $paths, $stylesheets);

    if (!sfConfig::get('sf_debug'))
    {
      $result = preg_replace('/\s+/m', ' ', $result);
    }

    return $this->renderText($result);
  }

  protected function getConcatenatedAssets($type, $paths, $assets)
  {
    $result = '';
    $attempts = array();

    foreach ($assets as $asset)
    {
      foreach ($paths as $path)
      {
        $file = $path.'/'.$type.'/'.$asset.'.'.$type;

        if (file_exists($file) && is_readable($file) && is_file($file))
        {
          break;
        }

        $attempts[] = $file;
        $file = null;
      }

      if (is_null($file))
      {
        throw new sfError404Exception('Unreadable asset file for package '.$this->name.'. Attempts in order: '.implode(', ', $attempts));
      }

      $result .= file_get_contents($file)."\n";
    }

    return $result;
  }

}

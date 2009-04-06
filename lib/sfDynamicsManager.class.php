<?php
/**
 * sfDynamicsPlugin - Main engine
 *
 * @author Romain Dorgueil <romain.dorgueil@symfony-project.com>
 */
class sfDynamicsManager
{
  /**
   * Loaded behaviors objects
   */
  protected $packages = array();

  /**
   * Assets
   */
  protected $javascripts = array();
  protected $stylesheets = array();

  /**
   * Context references
   */
  protected $context    = null;
  protected $controller = null;
  protected $request    = null;
  protected $response   = null;

  /**
   * Constructor
   */
  public function __construct($context)
  {
    $this->context    = $context;
    $this->response   = $context->getResponse();
    $this->request    = $context->getRequest();
    $this->controller = $context->getController();

    $this->configuration = include($context->getConfiguration()->getConfigCache()->checkConfig('config/dynamics.xml'));

    $context->getEventDispatcher()->connect('response.filter_content', array($this, 'filterContent'));
  }

  /**
   * isLoaded
   *
   * @param mixed $packageName
   * @return void
   */
  public function isLoaded($packageName)
  {
    return isset($this->packages[$packageName]) && $this->packages[$packageName];
  }

  /**
   * getPackage
   *
   * @param mixed $packageName
   * @return void
   */
  public function getPackage($packageName)
  {
    return $this->configuration->getPackage($packageName);
  }

  public function getPackages()
  {
    return $this->packages;
  }

  /**
   * Loads a behavior
   *
   * @param string Behavior name
   */
  public function load($packageName)
  {
    if (!$this->isLoaded($packageName))
    {
      $this->packages[$packageName] = false;

      try
      {
        $package = $this->getPackage($packageName);

        foreach($package->getDependencies() as $dependency)
        {
          $this->load($dependency);
        }

        // load assets
        if ($this->request instanceof sfWebRequest && !$this->request->isXmlHttpRequest())
        {
          $package->hasJavascripts() && $this->addAssets($packageName, 'javascript');
          $package->hasStylesheets() && $this->addAssets($packageName, 'stylesheet');
        }

        unset($this->packages[$packageName]); // This is needed to preserve assets order.
        $this->packages[$packageName] = $package;
      }
      catch (Exception $e)
      {
        // could not load package
        unset($this->packages[$packageName]);
        throw $e;
      }
    }
  }

  /**
   * adds a list of assets of given type to the list to add when filtering the response
   */
  public function addAssets($assets, $type)
  {
    if (!is_array($assets))
    {
      $assets = array($assets);
    }
    $property = $type.'s';

    foreach ($assets as $asset)
    {
      if (!in_array($asset, $this->$property))
      {
        $this->{$property}[] = $asset;
      }
    }
  }

  public function generateAssetsHtml()
  {
    $renderer = sfDynamics::getRenderer();
    $html = '';

    /* generate useable package array */
    $packages = array();
    foreach(array_keys($this->packages) as $packageName)
    {
      $packages[$packageName] = $this->getPackage($packageName);
    }

    foreach (array('javascript'=>'js', 'stylesheet'=>'css') as $type => $ext)
    {
      $assets = $this->{$type.'s'};

      if (sfDynamicsConfig::isGroupingEnabledFor($type) && sfDynamicsConfig::isSupercacheEnabled())
      {
        $url = sfDynamicsRouting::supercache_for($packages, $ext);
        $renderer->generateSupercache($url, $packages, $assets, $type);
        $html .= '  '.$this->getTag($url, $type)."\n";
      }
      else
      {
        foreach ($assets as $asset)
        {
          $url = $this->controller->genUrl(sfDynamicsRouting::uri_for($asset, $ext));
          $html .= '  '.$this->getTag($url, $type)."\n";
        }
      }
    }
    return $html;
  }

  public function getTag($url, $type)
  {
    switch ($type)
    {
      case 'javascript':
        return '<script type="text/javascript" src="'.$url.'"></script>';
      case 'stylesheet':
        return '<link rel="stylesheet" type="text/css" media="screen" href="'.$url.'" />';
      default:
        throw new BadMethodCallException('Invalid asset type.');
    }
  }

  public function filterContent(sfEvent $event, $content)
  {
    $response = $event->getSubject();

    if (false !== ($pos = strpos($content, '</head>')))
    {
      $html = $this->generateAssetsHtml();

      if ($html)
      {
        $content = substr($content, 0, $pos)."\n".$html.substr($content, $pos);
      }
    }
    return $content;
  }
}

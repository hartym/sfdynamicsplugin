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
   * Retrieve a package from configuration.
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
          $package->hasJavascripts() && $this->addAssetsFromPackage($package, 'javascript');
          $package->hasStylesheets() && $this->addAssetsFromPackage($package, 'stylesheet');
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
   *
   * @param  array $assets  an array of sfDynamicsAssetDefinition
   * @param  string $type   either stylesheet or javascript
   * @return void
   */
  protected function addAssets(array $assets, $type)
  {
    $property = $type.'s';

    foreach ($assets as $asset)
    {
      $vary = $asset->getVary();

      if (!isset($this->{$property}[$vary]))
      {
        $this->{$property}[$vary] = array();
      }

      if (!in_array($asset, $this->$property))
      {
        $this->{$property}[$vary][] = $asset;
      }
    }
  }

  protected function addAssetsFromPackage(sfDynamicsPackageDefinition $package, $type)
  {
    $getter = 'get'.ucfirst($type).'s';

    $this->addAssets($package->$getter(), $type);
  }

  public function generateAssetsHtml()
  {
    $renderer = sfDynamics::getRenderer();
    $htmls = array();

    // duplicate package
    $packages = $this->packages;

    foreach (array('javascript'=>'js', 'stylesheet'=>'css') as $type => $ext)
    {
      $namespacedAssets = $this->{$type.'s'};

      foreach ($namespacedAssets as $namespace => $assets)
      {
        // initialize
        isset($htmls[$namespace]) or $htmls[$namespace] = '';
        $html = '';

        // either supercache mode ...
        if (sfDynamicsConfig::isGroupingEnabledFor($type) && sfDynamicsConfig::isSupercacheEnabled())
        {
          $url = sfDynamicsRouting::supercache_for($packages, $ext);
          $renderer->generateSupercache($url, $packages, $assets, $type);
          $html .= '  '.$this->getTag($url, $type, $namespace)."\n";
        }
        // ... or package mode
        else
        {
          foreach ($assets as $asset)
          {
            $url = $this->controller->genUrl(sfDynamicsRouting::uri_for($asset, $ext));
            $html .= '  '.$this->getTag($url, $type, $namespace)."\n";
          }

        }

        $htmls[$namespace] .= $html;
      }
    }

    return $htmls;
  }

  /**
   * Render an html tag for an asset
   *
   * @throws BadMethodCallException if trying to render an invalid asset type.
   * @param  string $url
   * @param  string $type
   * @param  string $vary
   * @return string
   */
  public function getTag($url, $type, $vary="none")
  {
    switch ($type)
    {
      case 'javascript':
        return '<script type="text/javascript" src="'.$url.'"></script>';

      case 'stylesheet':
        // @todo refactor in stylesheet definition
        in_array($vary, array('screen', 'print', 'audio', 'all')) or $vary = 'all';
        return sprintf('<link rel="stylesheet" type="text/css" media="%s" href="%s" />', $vary, $url);

      default:
        throw new BadMethodCallException('Invalid asset type.');
    }
  }

  /**
   * Listen to content filtering event and add our asset loading HTML.
   *
   * @param  sfEvent $event
   * @param  string $content
   * @return string
   */
  public function filterContent(sfEvent $event, $content)
  {
    // event subject is a sfResponse instance
    $response = $event->getSubject();

    if (false !== ($pos = strpos($content, '</head>')))
    {
      $htmls = $this->generateAssetsHtml();

      if (count($htmls))
      {
        foreach ($htmls as $namespace => $html)
        {
          $content = substr($content, 0, $pos)."\n".$html.substr($content, $pos);
        }
      }
    }

    return $content;
  }
}

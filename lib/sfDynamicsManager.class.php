<?php
/**
 * sfDynamicsPlugin - Main engine
 *
 * Under refactoring. Some code is obsolete.
 *
 * @author Romain Dorgueil <romain.dorgueil@dakrazy.net>
 */
class sfDynamicsManager implements ArrayAccess
{
  /**
   * Loaded behaviors objects
   */
  protected $behaviors = array();
  protected $packages = array();

  /**
   * Assets
   */
  protected $javascripts = array();
  protected $stylesheets = array();

  /**
   * Bootstrap markup and html for scripts.
   *
   * @todo html should be added with javascript code
   */
  protected $htmlMarkup          = '';
  protected $javascriptBootstrap = '';

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
    return isset($this->packages[$packageName]) && (true===$this->packages[$packageName]);
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

      $package = $this->getPackage($packageName);

      foreach($package->getDependencies() as $dependency)
      {
        $this->load($dependency);
      }

      // load assets
      if ($this->request instanceof sfWebRequest && !$this->request->isXmlHttpRequest())
      {
        $package->hasJavascripts() && $this->addJavascripts($packageName);
        $package->hasStylesheets() && $this->addStylesheets($packageName);
      }

      unset($this->packages[$packageName]);
      $this->packages[$packageName] = true;
    }
  }

  /**
   * adds a list of javascript assets to the response
   */
  public function addJavascripts($javascripts)
  {
    if (!is_array($javascripts))
    {
      $javascripts = array($javascripts);
    }

    foreach ($javascripts as $javascript)
    {
      if (!in_array($javascript, $this->javascripts))
      {
        $this->javascripts[] = $javascript;
      }
    }
  }

  /**
   * adds a list of stylesheet assets to the response
   */
  public function addStylesheets($stylesheets)
  {
    if (!is_array($stylesheets))
    {
      $stylesheets = array($stylesheets);
    }

    foreach ($stylesheets as $stylesheet)
    {
      if (!in_array($stylesheet, $this->stylesheets))
      {
        $this->stylesheets[] = $stylesheet;
      }
    }
  }

  public function generateAssetsHtml()
  {
    $html = '';
    if (sfDynamics::isJavascriptGroupingEnabled())
    {
      $packages = array();

      foreach(array_keys($this->packages) as $packageName)
      {
        $packages[$packageName] = $this->getPackage($packageName);
      }

      $url = sfDynamicsRouting::supercache_for($packages, 'js');
      $this->generateJavascriptSupercache($url, $packages, $this->javascripts);
      $html .= '  <script type="text/javascript" src="'.$url.'"></script>'."\n";
    }
    else
    {
      foreach ($this->javascripts as $javascript)
      {
        $html .= '  <script type="text/javascript" src="'.$this->controller->genUrl(sfDynamicsRouting::uri_for($javascript, 'js')).'"></script>'."\n";
      }
    }

    if (sfDynamics::isStylesheetGroupingEnabled())
    {
      $packages = array();

      foreach(array_keys($this->packages) as $packageName)
      {
        $packages[$packageName] = $this->getPackage($packageName);
      }

      $url = sfDynamicsRouting::supercache_for($packages, 'css');
      $this->generateStylesheetSupercache($url, $packages, $this->stylesheets);
      $html .= '  <link rel="stylesheet" type="text/css" media="screen" href="'.$url.'" />'."\n";
    }
    else
    {
      foreach ($this->stylesheets as $stylesheet)
      {
        $html .= '  <link rel="stylesheet" type="text/css" media="screen" href="'.$this->controller->genUrl(sfDynamicsRouting::uri_for($stylesheet, 'css')).'" />'."\n";
      }
    }

    return $html;
  }

  public function generateJavascriptSupercache($url, $packages, $javascripts)
  {
    $filename = sfConfig::get('sf_web_dir').'/'.$url;
    if ((!file_exists($filename))||(!sfDynamics::isSupercacheEnabled()))
    {
      $renderer = sfDynamics::getRenderer();
      $src = '';

      foreach ($packages as $name => $package)
      {
        $renderedSrc = trim($renderer->getJavascript($name, $package));
        if ($renderedSrc)
        {
          $src .= '/* '.$name.' */ '.$renderedSrc."\n";
        }
      }

      @file_put_contents($filename, $src);
      if (!file_exists($filename))
      {
        throw new sfException('Supercache could not be written: '.$filename);
      }
    }
  }

  public function generateStylesheetSupercache($url, $packages, $stylesheets)
  {
    $filename = sfConfig::get('sf_web_dir').'/'.$url;
    if ((!file_exists($filename))||(!sfDynamics::isSupercacheEnabled()))
    {
      $renderer = sfDynamics::getRenderer();
      $src = '';
      foreach ($packages as $name => $package)
      {
        $renderedSrc = trim($renderer->getStylesheet($name, $package));
        if ($renderedSrc)
        {
          $src .= '/* '.$name.' */ '.$renderedSrc."\n";
        }
      }

      @file_put_contents($filename, $src);
      if (!file_exists($filename))
      {
        throw new sfException('Supercache could not be written: '.$filename);
      }
    }
  }

  public function filterContent(sfEvent $event, $content)
  {
    $response = $event->getSubject();

    if (false !== ($pos = strpos($content, '</head>')))
    {
      $this->context->getConfiguration()->loadHelpers(array('Tag', 'Asset'));

      $html = $this->generateAssetsHtml();

      if ($html)
      {
        $content = substr($content, 0, $pos)."\n".$html.substr($content, $pos);
      }
    }
    return $content;
  }

  public function getJavascript()
  {
    $behaviorsJs = '';

    foreach ($this->behaviors as $behavior)
    {
      $behaviorsJs .= $behavior->getJavascript();
    }

    return $this->javascriptBootstrap.$behaviorsJs;
  }

  public function getHtmlMarkup()
  {
    $behaviorsMarkup = '';

    foreach ($this->behaviors as $behavior)
    {
      $behaviorsMarkup .= $behavior->getMarkup();
    }

    return $this->htmlMarkup.$behaviorsMarkup;
  }

  static public function getMarkup($isXhr=false)
  {
    $instance = self::getInstance();

    $script = trim($instance->getJavascript());

    if (isset($script[0]))
    {
      $script = self::getJavascriptTag($isXhr?$script:'$(document).ready(function(){'.$instance->getJavascript().'});');
    }

    return ($isXhr?'':$instance->getHtmlMarkup()).$script;
  }

  static public function includeMarkup($isXhr=false)
  {
    echo self::getMarkup($isXhr);
  }

  /**
   * ArrayAccess: isset
   */
  public function offsetExists($offset)
  {
    return isset($this->configuration[$offset]);
  }

  /**
   * ArrayAccess: getter
   */
  public function offsetGet($offset)
  {
    return $this->get($offset);
  }

  /**
   * ArrayAccess: setter
   */
  public function offsetSet($offset, $value)
  {
    throw new LogicException('Cannot use array access of javascript behavior manager in write mode.');
  }

  /**
   * ArrayAccess: unset
   */
  public function offsetUnset($offset)
  {
    throw new LogicException('Cannot use array access of javascript behavior manager in write mode.');
  }

  static public function getJavascriptTag($js)
  {
    return '<script type="text/javascript">
      //<![CDATA[
      '.$js.'
      //]]>
      </script>';
  }

  public function tag($js)
  {
    return self::getJavascriptTag($js);
  }
}

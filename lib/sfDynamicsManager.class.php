<?php
/**
 * sfDynamicsPlugin - Behavior manager class
 *
 * To enable magic, include the following in your layout, just before the </body> tag:
 * sfJavascriptBehaviorManager::includeMarkup();
 *
 * @author Romain Dorgueil <romain.dorgueil@sensio.com>
 */
class sfDynamicsManager implements ArrayAccess
{
  /**
   * Loaded behaviors objects
   */
  protected $behaviors = array();
  protected $packages = array();

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

    if ($this->response instanceof sfWebResponse && $this->controller instanceof sfWebController)
    {
      foreach ($javascripts as $jsName)
      {
        $this->response->addJavascript($this->controller->genUrl(sfDynamicsRouting::uri_for($jsName, 'js')));
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

    if ($this->response instanceof sfWebResponse && $this->controller instanceof sfWebController)
    {
      foreach ($stylesheets as $cssName)
      {
        $this->response->addStylesheet($this->controller->genUrl(sfDynamicsRouting::uri_for($cssName, 'css')));
      }
    }
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

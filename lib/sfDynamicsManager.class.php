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

    if (isset($this->configuration['__autoload__']))
    {
      foreach($this->configuration['__autoload__'] as $autoloadable)
      {
        $this->load($autoloadable);
      }

      unset($this->configuration['__autoload__']);
    }
  }

  /**
   * Returns global or behavior specific configuration
   *
   * @throws InvalidArgumentException if specified behavior does not have configuration
   *
   * @param  mixed $behavior If null, global configuration will be returned
   * @return void
   */
  public function getConfiguration($behavior=null)
  {
    if (is_null($behavior))
    {
      return $this->configuration;
    }
    else
    {
      if (!isset($this->configuration[$behavior]))
      {
        throw new InvalidArgumentException('Configuration for sfDynamics behavior «'.$behavior.'» is not available.');
      }
      return $this->configuration[$behavior];
    }
  }

  /**
   * Loads a behavior
   *
   * @param string Behavior name
   */
  public function load($behavior)
  {
    if (!isset($this->behaviors[$behavior]))
    {
      $configuration = $this->getConfiguration($behavior);

      // load dependencies
      if (isset($configuration['depends_on']))
      {
        if (!is_array($configuration['depends_on']))
        {
          throw new InvalidArgumentException('«depends_on» clause in sfDynamics behavior configuration file should be an array.');
        }

        foreach ($configuration['depends_on'] as $dependance)
        {
          $this->load($dependance);
        }
      }

      // load assets
      if ($this->request instanceof sfWebRequest)
      {
        if (!$this->request->isXmlHttpRequest())
        {
          if ((!isset($configuration['compilation'])) || $configuration['compilation'])
          {
            foreach (array('javascripts', 'stylesheets') as $assetType)
            {
              if (isset($configuration[$assetType]) && count($configuration[$assetType]))
              {
                $method = 'add'.ucfirst($assetType);
                $this->$method($behavior);
              }
            }
          }
        }
      }

      // add automatic markup
      if (isset($configuration['markup']) && isset($configuration['markup']['_auto']))
      {
        $this->htmlMarkup .= $configuration['markup']['_auto'];
      }

      // add automatic scripts
      if (isset($configuration['scripts']) && isset($configuration['scripts']['_auto']))
      {
        $this->javascriptBootstrap .= $configuration['scripts']['_auto'];

      }

      // initialize @todo maybe useless
      if (isset($configuration['class']))
      {
        $classname = $configuration['class'];
        $this->behaviors[$behavior] = new $classname();
      }
      else
      {
        $this->behaviors[$behavior] = new sfJavascriptGenericBehavior(isset($configuration['scripts']) ? $configuration['scripts'] : array());
      }
    }
  }

    /**
     * Retrieve a behavior object
     *
     * @param string $behavior
     *
     * @return mixed
     */
    public function get($behavior)
    {
      if (!isset($this->behaviors[$behavior]))
      {
        $this->load($behavior);
      }

      return $this->behaviors[$behavior];
    }

    /**
     * adds a list of javascript assets to the response
     */
    public function addJavascripts()
    {
      if ($this->response instanceof sfWebResponse && $this->controller instanceof sfWebController)
      {
        foreach (func_get_args() as $jsName)
        {
          $this->response->addJavascript($this->controller->genUrl('@sfDynamicsPlugin_javascript?name='.$jsName));
        }
      }
    }

    /**
     * adds a list of stylesheet assets to the response
     */
    public function addStylesheets()
    {
      if ($this->response instanceof sfWebResponse && $this->controller instanceof sfWebController)
      {
        foreach (func_get_args() as $cssName)
        {
          $this->response->addStylesheet($this->controller->genUrl('@sfDynamicsPlugin_stylesheet?name='.$cssName));
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

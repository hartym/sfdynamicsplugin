<?php

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');
$t = new lime_test(1);

class ConfigHandlerMock extends sfDynamicsViewConfigHandler
{
  public function setConfiguration($config)
  {
    $this->yamlConfig = self::mergeConfig($config);
  }

  public function addHtmlAsset($viewName = '')
  {
    return parent::addHtmlAsset($viewName);
  }
}

$handler = new ConfigHandlerMock();

// addHtmlAsset() basic asset addition
$t->diag('addHtmlAsset() basic asset addition');

$handler->setConfiguration(array(
  'myView' => array(
    'dynamics' => array('foobar'),
  ),
));
$content = <<<EOF

  sfDynamics::load('foobar');

EOF;
$t->is(fix_linebreaks($handler->addHtmlAsset('myView')), fix_linebreaks($content), 'addHtmlAsset() adds stylesheets to the response');


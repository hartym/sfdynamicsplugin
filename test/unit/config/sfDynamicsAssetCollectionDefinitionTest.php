<?php

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsBaseDefinition.class.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsAssetCollectionDefinition.class.php');

$testCount = 4;
$jsArray   = array('testjs');
$cssArray  = array('testcss');

$t = new lime_test($testCount, new lime_output_color());

try
{
  $i = sfDynamicsAssetCollectionDefinition::__set_state(array('javascripts'=>$jsArray, 'stylesheets'=>$cssArray));
  $t->isa_ok($i, 'sfDynamicsAssetCollectionDefinition', '__set_state works and returns an instance of right class');
}
catch (Exception $e)
{
  $t->fail('__set_state failed');
}

$t->is($i->getJavascripts(), $jsArray, 'Javascripts getter');
$t->is($i->getStylesheets(), $cssArray, 'Stylesheets getter');

try
{
  $i = sfDynamicsAssetCollectionDefinition::__set_state(array('wrong'=>$jsArray));
  $t->fail('__set_state should fail if wrong initialization data is sen.');
}
catch (sfConfigurationException $e)
{
  $t->ok(1, '__set_state failed because of wrong parameters given');
}
catch (Exception $e)
{
  $t->fail('__set_state failed, but with wrong exception type');
}

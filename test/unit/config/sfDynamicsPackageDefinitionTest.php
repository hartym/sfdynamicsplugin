<?php

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsBaseDefinition.class.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsAssetCollectionDefinition.class.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsPackageDefinition.class.php');

$testCount = 1;
$jsArray   = array('testjs');
$cssArray  = array('testcss');

$t = new lime_test($testCount, new lime_output_color());

$testPackageXml = <<<EOF
<package name="test.package">
  <javascript>myJs</javascript>
  <stylesheet>myCss</stylesheet>
</package>
EOF;

try
{
  $package = new sfDynamicsPackageDefinition($testPackageXml);

  t->isa_ok($package, 'sfDynamicsPackageDefinition', 'Instance creation from XML data works')
}
catch (Exception $e)
{
  $t->fail('Creation from XML failed');
}

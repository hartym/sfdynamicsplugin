<?php

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsBaseDefinition.class.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsConfigDefinition.class.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsAssetCollectionDefinition.class.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsPackageDefinition.class.php');

$testCount = 4;

$t = new lime_test($testCount, new lime_output_color());

$testXml1 = <<<EOF
<dynamics>
  <package name="test.package">
    <javascript>myJs</javascript>
    <stylesheet>myCss</stylesheet>
  </package>
</dynamics>
EOF;

$testXml2 = <<<EOF
<dynamics>
  <package name="test.package">
    <javascript>myJsV2</javascript>
    <stylesheet>myCssV2</stylesheet>
  </package>
</dynamics>
EOF;

try
{
  $conf = new sfDynamicsConfigDefinition();

  $t->isa_ok($conf, 'sfDynamicsConfigDefinition', 'Creation of empty config definition.');
}
catch (Exception $e)
{
  $t->fail('Creation of empty config definition.');
}

$conf1 = new sfDynamicsConfigDefinition(simplexml_load_string($testXml1));
$conf2 = new sfDynamicsConfigDefinition(simplexml_load_string($testXml2));

try
{
  $conf->merge($conf1);
  $t->pass('Merging first config.');
}
catch (Exception $e)
{
  $t->fail('Merging first config.');
}

try
{
  $conf->merge($conf1);
  $t->pass('Merging first config a second time do not throw anything.');
}
catch (Exception $e)
{
  $t->fail('Merging first config a second time do not throw anything.');
}

try
{
  $conf->merge($conf2);
  $t->fail('Merging incompatible package throws a sfDynamicsConfigurationException.');
}
catch (sfDynamicsConfigurationException $e)
{
  $t->pass('Merging incompatible package throws a sfDynamicsConfigurationException.');
}
catch (Exception $e)
{
  $t->fail(sprintf('Merging incompatible package throws a sfDynamicsConfigurationException (got a %s exception).', get_class($e)));
}


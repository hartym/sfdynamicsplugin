<?php
require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfDynamicsBaseDefinition.class.php');

$testCount = 2;
$_test = 'foo/bar';

class sfDynamicsBaseDefinitionMock extends sfDynamicsBaseDefinition
{
  protected $test = null;

  public function __set_state($state)
  {
    return self::build(new self(), array('test'), $state);
  }

  public function setTest($value)
  {
    $this->test = $value;
  }

  public function getTest()
  {
    return $this->test;
  }

  public function parseXml($xml)
  {
    throw new LogicException(__CLASS__.'::parseXml() is not yet implemented');
  }
}

$t = new lime_test($testCount, new lime_output_color());

try
{
  $i = sfDynamicsBaseDefinitionMock::__set_state(array('test'=>$_test));
  $t->isa_ok($i, 'sfDynamicsBaseDefinitionMock', '__set_state works and returns an instance of right class');
}
catch (Exception $e)
{
  $t->fail('__set_state failed');
}

$t->is($i->getTest(), $_test, 'Dummy getter');



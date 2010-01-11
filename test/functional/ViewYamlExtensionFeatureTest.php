<?php

include(dirname(__FILE__).'/../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->
  get('/featureView/applicationScope')->

  with('request')->begin()->
    isParameter('module', 'featureView')->
    isParameter('action', 'applicationScope')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
  end()
;

$browser->test()->is(sfDynamics::getManager()->isLoaded('test'), false, 'Testing packages loaded by application\'s view.yml.');

$browser->
  get('/featureView/moduleAllScope')->

  with('request')->begin()->
    isParameter('module', 'featureView')->
    isParameter('action', 'moduleAllScope')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
  end()
;

$browser->test()->is(sfDynamics::getManager()->isLoaded('test1'), true, 'Testing packages loaded by module\'s view.yml `all` section.');

$browser->
  get('/featureView/moduleActionScope')->

  with('request')->begin()->
    isParameter('module', 'featureView')->
    isParameter('action', 'moduleActionScope')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
  end()
;

$browser->test()->is(sfDynamics::getManager()->isLoaded('test2'), true, 'Testing packages loaded by module\'s view.yml section given the view name.');


<?php

class exampleAction extends sfAction
{
  public function execute($request)
  {
    sfDynamics::load('jquery');
  }
}

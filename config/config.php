<?php

$this->dispatcher->connect('routing.load_configuration', array('sfDynamics', 'listenToRoutingLoadConfigurationEvent'));

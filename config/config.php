<?php
$this->dispatcher->connect('routing.load_configuration', array('sfDynamicsRouting', 'configure'));
$this->dispatcher->connect('task.cache.clear', array('sfDynamicsCache', 'clearSuperCache'));


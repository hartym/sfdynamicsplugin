<?php

class dynamicsMootoolsConfigTask extends sfBaseTask
{
	
  private $packages = array();
  	
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('core', sfCommandArgument::REQUIRED, 'path of core/scripts.json'),
       new sfCommandArgument('more', sfCommandArgument::REQUIRED, 'path of more/scripts.json'),
    ));

    $this->addOptions(array(
      new sfCommandOption('core-prefix', null, sfCommandOption::PARAMETER_REQUIRED, 'Core prefix', 'moocore'),
      new sfCommandOption('more-prefix', null, sfCommandOption::PARAMETER_REQUIRED, 'More prefix', 'moomore'),
      new sfCommandOption('core-path', null, sfCommandOption::PARAMETER_REQUIRED, 'Core path', 'mootools-core'),
      new sfCommandOption('more-path', null, sfCommandOption::PARAMETER_REQUIRED, 'More path', 'mootools-more'),
      // add your own options here
    ));

    $this->namespace        = 'dynamics';
    $this->name             = 'mootools-config';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [dynamics:mootools-config|INFO] generate dynamics-mootools.xml

Call it with:

  [php symfony dynamics:mootools-config|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
	$core =  json_decode(file_get_contents($arguments['core']));
	$more =  json_decode(file_get_contents($arguments['more']));
	
	file_put_contents(sfConfig::get('sf_root_dir').'/plugin/sfDynamicsPlugin/config/dynamics-mootools.xml', <<<EOF
<?xml version="1.0" ?>
<dynamics>
   <package name="mootools">
     <require>{$options['core-prefix']}.core</require>	
     <require>{$options['more-prefix']}.more</require>	
	 </package>
	{$this->build($this->objectToArray($core), $options['core-prefix'], $options['core-path'])}
	{$this->build($this->objectToArray($more), $options['more-prefix'], $options['more-path'])}
</dynamics>			
EOF
);


  }
	
  private function build($script, $prefix, $path)
  {
  	$str = '';
	
  	$categories = array();
  	foreach($script as $category => $packages)
  	{
  		$cat = strtolower($prefix.'.'.$category);
  		$categories[$cat] = array();
  		$this->packages[$cat] = $cat;
  		
  		foreach($packages as $package => $props)
  		{
  			$name = strtolower($prefix.'.'.$category.'.'.$package);
  			$this->packages[$package] = $name;
  			$categories[$cat][] = $package; 
  		}
  		
  		$str .= $this->package($cat, array(
  			'deps' => $categories[$cat],
  			'desc' => 'Contains all '.$cat.' submodules'
  		));
  	}

  	$str .= $this->package($prefix, array(
  		'deps' => array_keys($categories),
  		'desc' => 'Contains all '.$prefix.' modules'
  	));
  	
    foreach($script as $category => $packages)
  	{
  		foreach($packages as $package => $props)
  		{
  			$name = strtolower($prefix.'.'.$category.'.'.$package);
  			$str.=$this->package($name, $props, $path.'/'.$category.'/'.$package);
  		}
  	}  	
  	
  	return  $str;
  } 
  
  private function package($name, $props, $javascript = null)
  {
  	ob_start();
  	echo <<<EOF
  			
  	<package name="{$name}">
  		<description>{$props['desc']}</description>
EOF;


	if(!is_null($javascript))
	{
  		echo <<<EOF
  		
  		<javascript>{$javascript}</javascript>
EOF;

	}

	 
	
	if($props['deps'][0] == $package) array_shift($props['deps']);

	
	
	foreach($props['deps'] as $deps)
	{
  		echo <<<EOF

		<require>{$this->packages[$deps]}</require>	
EOF;
	}
			
  	echo <<<EOF
	
	</package>
	
EOF;

  	return  ob_get_clean();
  }
  
  private function objectToArray($obj)
  {
  	if(is_object($obj))
  	{
  		$obj = get_object_vars($obj);
  	}
  	
  	if(is_array($obj))
  	{
	  	foreach($obj as $key => $val)
	  	{
	  		$obj[$key] = $this->objectToArray($val);	
	  	}
  	}
  	
  	return $obj;
  }
  
}

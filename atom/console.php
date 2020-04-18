<?php

	set_time_limit(0);
	
	// TODO: refactor with symfony style
	foreach($_SERVER['argv'] as $key => $argv){
		if ($argv == '--env=pro' || $argv == '--env=dev'){
			$_SERVER['env'] = substr($argv, 6, 3);
			unset($_SERVER['argv'][$key]);
		}
	}
	if (!isset($_SERVER['env'])){
		throw new \Exception('Environment not set', 400);
	}
	include_once __DIR__.'/init.php';
	use Symfony\Component\Console\Application;

	$console = new Application();
	foreach($app['helper']->glob(__DIR__.'/app/controllers/console/*.php') as $controller){
		$controller = str_replace(__DIR__, '', pathinfo($controller, PATHINFO_DIRNAME)).'/'.pathinfo($controller, PATHINFO_FILENAME);
		$command = str_replace('/', '\\', $controller);
		$console->add(new $command);
	}
	$console->run();
<?php

	function startTimer(){
		return $GLOBALS['timer'] = microtime(true);
	}
	function stopTimer($die = false){
		$timer = microtime(true) - $GLOBALS['timer'];
		if ($die){
			die((string)$timer);
		}
		return $timer;
	}
	require_once __DIR__.'/atom/init.php';
	$app->run();
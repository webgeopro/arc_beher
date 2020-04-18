<?php

	namespace app\models\log;
	use app\models\generic\generic;

	/**
	 * Log
	 */
	class log extends generic{
		public function add($action){
			return (new static)->set(array(
				'action'	=> $action,
				'date'		=> new \MongoDate(),
				'user'		=> $this->app['user']->get('_id'),
				'ip'		=> $this->app['request']->getClientIp(),
				'url'		=> $this->app['request']->getPathInfo()
			))->save();
		}
	}
<?php

	namespace app\controllers\web;
	use app\controllers\web\generic,
		app\models\log\log as model;
		
	class log extends generic {
		public static function model(){
			return new model();
		}
	}
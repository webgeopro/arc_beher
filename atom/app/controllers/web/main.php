<?php

	namespace app\controllers\web;
	use Silex\Application,
		Symfony\Component\HttpFoundation\Request,
		Symfony\Component\HttpFoundation\Response,
		Symfony\Component\HttpFoundation\Cookie,
		app\models\user\user as u,
		app\models\helper;
		
	class main {
		public static function null(Request $request, Application $app){
			return true;
		}
		
		public static function index(Request $request, Application $app){
			if ($request->cookies->get('birthday')) {
				$request->attributes->set('_layout', 'frontend/page.html');
			} else {
				$request->attributes->set('_layout', 'frontend/index.html');
			}
			return true;
		}
		
		public static function birthday(Request $request, Application $app){
			$birthday = strtotime($request->get('year').'-'.$request->get('month').'-'.$request->get('day').' 00:00:00');
			$validDate = strtotime('now -18 years');
			if ($birthday >= $validDate || $birthday === false) {
				$request->attributes->set('_layout', 'frontend/index.html');
				return true;
			}
			$request->attributes->set('_layout', 'frontend/page.html');
			$app['page']->response()->headers->setCookie(new Cookie('birthday', $birthday, time() + ($request->get('remember') == 'on' ? 31536000 : 86400), '/', null, false, false));
			return $app['page'];
		}
		
		public static function error404(Request $request, Application $app){
			return $app->abort(404, $app['translator']->trans('http.404'));
		}
	}
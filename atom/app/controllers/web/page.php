<?php

	namespace app\controllers\web;
	use Silex\Application,
		Symfony\Component\HttpFoundation\Request,
		Symfony\Component\HttpFoundation\Response,
		app\controllers\web\generic,
		app\models\helper,
		app\models\page\page as model;
		
	class page extends generic {
		public static function model(){
			return new model();
		}
		
		public static function read(Request $request, Application $app){
			if ($request->get('url')){
				$fields = static::prepareFields($request->get('fields', array()));
				$condition = array(
					'url'		=> '/'.ltrim($request->get('url'), '/'),
					'enabled'	=> true
				);
				$pages = static::model()->load($fields, $condition, array(), 1);
				if (!$pages->count()){
					return main::error404($request, $app);
				}
				$app['page']->set(array(
					'total'	=> $pages->getTotal(),
					'data'	=> $pages->all()
				));
				$app['page']->seo()->set(array(
					'title'			=> $app['page']->get('data')->getFirst()->get('seo_title'),
					'description'	=> $app['page']->get('data')->getFirst()->get('seo_description'),
					'image'			=> $app['page']->get('data')->getFirst()->get('seo_image', new helper\iterator())->getFirst()->get('route')
				));
				return $app['page'];
			}
			return main::error404($request, $app);
		}
		
		public static function seo(Request $request, Response $response, Application $app){
			$app['page']->seo()->set(array(
				'url'			=> $request->getPathInfo(),
				'type'			=> $app['config']->get('seo')->get('type'),
				'title'			=> $app['page']->seo()->get('title', $app['config']->get('seo')->get('title')),
				'description'	=> $app['page']->seo()->get('description', $app['config']->get('seo')->get('description')),
				'image'			=> $app['page']->seo()->get('image', $app['config']->get('seo')->get('image'))
			));
			return null;
		}
	}
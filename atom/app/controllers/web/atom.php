<?php

	namespace app\controllers\web;
	use Silex\Application,
		Symfony\Component\HttpFoundation\Request,
		Symfony\Component\Yaml\Yaml,
		app\controllers\web\generic,
		app\models\helper;
		
	class atom extends generic {
		public static function entities(Request $request, Application $app){
			$filter = (new helper\proto)->set($request->get('condition', array()));
			$fields = (new helper\proto)->set((array)$request->get('fields', array('routes', 'schemes')));
			$response = array();
			
			$template = $app['config']->get('paths')->get('atom').'/app/models/%s.yml';
			$pattern = sprintf($template, '*');
			if ($filter->get('name')){
				if (is_object($filter->get('name')) && $filter->get('name')->has('$in')){
					$pattern = array();
					foreach($filter->get('name')->get('$in') as $entity){
						$pattern[] = sprintf($template, $entity);
					}
				} else {
					$pattern = sprintf($template, $filter->get('name'));
				}
			}
			$entities = (new helper\helper)->glob($pattern);
			foreach($entities as $scheme){
				$modelName = '\app\models\\'.pathinfo($scheme, PATHINFO_FILENAME).'\\'.pathinfo($scheme, PATHINFO_FILENAME);
				$model = new $modelName;
				$data = array('title' => $model->getEntityTitle());

				$routes = $model->getRoutes()->get();
				foreach($routes as $routeName => $routeProperties){
					if (!$app['user']->hasAccess($routeName)){
						unset($routes[$routeName]);
					}
				}
				if (count($routes) == 0){
					continue;
				}
				if ($fields->hasValue('routes')){
					$data['routes'] = $routes;
				}
				
				if ($fields->hasValue('schemes')){
					$scheme = $model->getScheme()->get();
					foreach($scheme as $field => $properties){
						foreach($properties as $property => $value){
							if (is_string($value) && strstr($value, '<?')){
								$scheme[$field][$property] = eval('return '.substr($value, 2).';');
							}
						}
					}
					$data['scheme'] = $scheme;
				}
				
				$response[$model->getEntityName()] = $data;
			}
			return $app['page']->set($response);
		}
		
		public static function locales(Request $request, Application $app){
			$filter = (new helper\proto)->set($request->get('condition', array()));
			return $app['page']->set($app['translator']->getCatalogue($filter->get('locale'))->all()['messages']);
		}
		
		public static function properties(Request $request, Application $app){
			if (!$app['user']->isAdmin()){
				return $app->abort(403, $app['translator']->trans('http.403'));
			}
			$file  = $app['config']->get('paths')->get('atom').'/properties.yml';
			return $app['page']->set(Yaml::parse(file_get_contents($file)));
		}
		
		public static function filemanager(Request $request, Application $app){
			if (!$app['user']->isAdmin()){
				return $app->abort(403, $app['translator']->trans('http.403'));
			}
			return require_once $app['config']->get('paths')->get('root').'/themes/backend/filemanager/'.$request->get('query');
		}
	}
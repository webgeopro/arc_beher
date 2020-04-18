<?php

	use Symfony\Component\HttpFoundation\Request,
		Symfony\Component\HttpFoundation\Response;

	$app->before(function(Request $request, Silex\Application $application, $layout = false, $template = false, $before = false, $after = false, $controller = false) use($app){
		if ($request->getMethod() == 'OPTIONS'){
			$response = $app['page']->response();
			$response->headers->set('Access-Control-Allow-Origin', '*');
			$response->headers->set('Access-Control-Allow-Methods', $request->headers->get('Access-Control-Request-Method'));
			$response->headers->set('Access-Control-Allow-Headers', $request->headers->get('Access-Control-Request-Headers'));
			return $response;
		}
		$app['page']->response()->headers->set('Access-Control-Allow-Origin', '*');
		
		$route = $app['routes']->get($request->get('_route'));

		$request->attributes->set('_layout', $layout);
		if (strstr($app['request']->headers->get('accept', 'text/html'), 'text/html') || $app['request']->headers->get('accept') == '*/*'){
			if($route->hasOption('_layout')){
				$layout = $route->getOption('_layout');
			}
			if ($layout === false && $route->hasDefault('_layout')){
				$layout = $route->getDefault('_layout');
			}
			$request->attributes->set('_layout', $layout);

			if($route->hasOption('_template')){
				$template = $route->getOption('_template');
			}
			if ($template === false && $route->hasDefault('_template')){
				$template = $route->getDefault('_template');
			}
			if ($template){
				$request->attributes->set('_template', $template);
			}
		}

		if($route->hasOption('_before')){
			$before = $route->getOption('_before');
		}
		if ($before === false && $route->hasDefault('_before')){
			$before = $route->getDefault('_before');
		}
		if ($before){
			foreach((array)$before as $key => $ctrl){
				$before[$key] = 'app\\controllers\\'.$ctrl;
			}
			$route->setOption('_before_middlewares', $before);
			$request->attributes->set('_before_middlewares', $before);
		}

		if($route->hasOption('_after')){
			$after = $route->getOption('_after');
		}
		if ($after === false && $route->hasDefault('_after')){
			$after = $route->getDefault('_after');
		}
		if ($after){
			foreach((array)$after as $key => $ctrl){
				$after[$key] = 'app\\controllers\\'.$ctrl;
			}
			$route->setOption('_after_middlewares', $after);
			$request->attributes->set('_after_middlewares', $after);
		}

		if ($route->hasOption('_controller')){
			$controller = $route->getOption('_controller');
		}
		if ($controller === false && $route->hasDefault('_controller')){
			$controller = $route->getDefault('_controller');
		}
		if ($controller){
			$request->attributes->set('_controller', 'app\\controllers\\'.$controller);
		}
	});

	$app->after(function(Request $request, Response $response) use($app){
		$response = $app['page']->response();
		switch($app['request']->headers->get('accept')){
			case 'application/xml':
				$response->setContent($app['serializer']->serialize($app['page']->all(), 'xml'));
				$response->headers->set('Content-Type', 'application/xml');
			break;

			case 'application/json':
				$response->setContent($app->json($app['page']->all())->getContent());
				$response->headers->set('Content-Type', 'application/json');
			break;

			default:
				if ($layout = $request->attributes->get('_layout')){
					$response->setContent($app['view']->render($layout));
				}
			break;
		}
		return $response;
	});

	$app->error(function(\Exception $error, $code) use($app){
		$app['page']->set(array(
			'error' => array(
				'code'		=> $code ? $code : $error->getCode(),
				'message'	=> $error->getMessage(),
				'trace'		=> $error->getTrace()
			)
		));
		$app['page']->response()->setContent($error->getCode().' / '.$error->getMessage());
		$app['page']->response()->setStatusCode($code);
		return $app['page'];
	});
<?php

	$loader = require_once __DIR__.'/components/autoload.php';
	$loader->add('app', __DIR__);

	$app = new Silex\Application();
	$app['env'] = (@$_SERVER['env'] == 'dev' ? 'dev' : 'pro');
	$app['debug'] = ($app['env'] == 'dev');
	error_reporting($app['debug'] ? E_ALL : 0);
	ini_set('display_errors', $app['debug'] ? 'on' : 'off');
	$app['config'] = (new app\models\helper\proto())->set(require_once __DIR__.'/config_'.$app['env'].'.php');
	mb_internal_encoding($app['config']->get('encoding'));

	use Symfony\Component\Serializer,
		Symfony\Component\Config\FileLocator,
		Symfony\Component\Routing\Loader\YamlFileLoader,
		Symfony\Component\Routing\Generator\UrlGenerator,
		Symfony\Component\Translation\Loader\YamlFileLoader as tyfl,
		Symfony\Component\Filesystem\Filesystem,
		Doctrine\MongoDB\Connection,
		Doctrine\MongoDB\Configuration,
		Doctrine\Common\EventManager;

	$app->register(new Silex\Provider\SessionServiceProvider());
	$app->register(new Silex\Provider\ValidatorServiceProvider());
	$app->register(new Silex\Provider\TranslationServiceProvider());

	$app['session.storage.handler'] = null;
	$app['db'] = $app->share(function() use($app){
		$connection = new Connection($app['config']->get('mongo')->get('server'), (array)$app['config']->get('mongo')->get('options')->all(), new Configuration(), new EventManager());
		return $connection->selectDatabase($app['config']->get('mongo')->get('database'));
	});
	$app['fs'] = $app->share(function() use($app){
		return new Filesystem();
	});
	$app['routes'] = $app->share($app->extend('routes', function($routes, $app){
		$loader = new YamlFileLoader(new FileLocator($app['config']->get('paths')->get('atom').'/app/routes'));
		$collection = $loader->load('boot.yml');
		$routes->addCollection($collection);
		return $routes;
	}));
	$app['serializer'] = $app->share(function() use($app){
		$encoders = array(new Serializer\Encoder\XmlEncoder(), new Serializer\Encoder\JsonEncoder());
		$normalizers = array(new Serializer\Normalizer\GetSetMethodNormalizer());
		return new Serializer\Serializer($normalizers, $encoders);
	});
	$app['url'] = $app->share(function() use($app){
		$app->flush();
		return new UrlGenerator($app['routes'], $app['request_context']);
	});
	$app['translator'] = $app->share($app->extend('translator', function($translator, $app){
		$translator->addLoader('yaml', new tyfl());
		try{
			foreach($app['request']->getLanguages() as $lang){
				$lang = strtok($lang, '-_');
				if ($app['fs']->exists(__DIR__.'/app/locales/'.$lang.'.yml') && !isset($locale)){
					$locale = $lang;
					$translator->setLocale($locale);
					$translator->addResource('yaml', __DIR__.'/app/locales/'.$locale.'.yml', $locale);
					continue;
				}
			}
		} catch(\Exception $e){}
		$translator->addResource('yaml', __DIR__.'/app/locales/en.yml', 'en');
		return $translator;
	}));
	$app['page'] = $app->share(function(){
		return new app\models\page\page();
	});
	$app['view'] = $app->share(function() use($app){
		$view = new app\models\view\view();
		$view->addGlobal('app', $app);
		return $view;
	});
	$app['helper'] = $app->share(function(){
		return new app\models\helper\helper();
	});
	$app['user'] = $app->share(function() use($app){
		return new app\models\user\user();
	});
	$app['log'] = $app->share(function() use($app){
		return new app\models\log\log();
	});
	$app['mailer'] = $app->share(function() use($app){
		$transport = Swift_SmtpTransport::newInstance()
			->setHost($app['config']->get('mailer')->get('smtp')->get('host'))
			->setPort($app['config']->get('mailer')->get('smtp')->get('port'))
			->setEncryption($app['config']->get('mailer')->get('smtp')->get('mode'))
			->setUsername($app['config']->get('mailer')->get('smtp')->get('user'))
			->setPassword($app['config']->get('mailer')->get('smtp')->get('pass'));
		return Swift_Mailer::newInstance($transport);
	});

	require_once __DIR__.'/handler.php';
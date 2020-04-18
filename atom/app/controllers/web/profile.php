<?php

	namespace app\controllers\web;
	use Silex\Application,
		Symfony\Component\HttpFoundation\Request,
		Symfony\Component\HttpFoundation\File\File as File,
		app\controllers\web\generic,
		app\models\helper,
		app\models\setting\setting,
		Gregwar\Image\Image,
		app\models\profile\profile as model;
		
	class profile extends generic {
		public static function model(){
			return new model();
		}
		
		public static function show(Request $request, Application $app){
			$profile = (new model)->loadById($request->get('_id'));
			
			if (!$profile->get('_id')) {
				$request->attributes->set('_layout', 'frontend/page.html');
				$app['page']->response()->setStatusCode(404);
				return true;
			}
			
			$about = $profile->get('about');
			$about = explode("\n", $about);
			$about = implode('</p><p>', $about);
			$profile->set('about', $about);
			
			$caption = $profile->get('caption');
			$caption = explode(' ', $caption);
			$caption = implode('<br />', $caption);
			$profile->set('caption', $caption);
			
			$ingred = $profile->get('ingred');
			$ingred = explode("\n", $ingred);
			$ingred = implode('</li><li>', $ingred);
			$profile->set('ingred', $ingred);
			
			$ingred = $profile->get('ingred');
			$ingred = explode("\n", $ingred);
			$ingred = implode('</li><li>', $ingred);
			$profile->set('ingred', $ingred);
			
			$decor = $profile->get('decor');
			$decor = explode("\n", $decor);
			$decor = implode('</li><li>', $decor);
			$profile->set('decor', $decor);
			
			return $app['page']->set('profile', $profile);
		}
		
		public static function preview(Request $request, Application $app){
			$item = static::model()->set($request->request->all());
			if(count($request->files->all())){
				$item->setFile($request->files->all());
			}
			
			if (is_string($request->get('image'))) {
				$item->setFile('image', static::downloadImage($request->get('image')));
			}
			
			$image = Image::open($app['config']->get('paths')->get('root').$item->get('image')->getFirst()->get('route'));
			$image->zoomCrop(600, 630, 'white', 'center', 'center');
			return $app['page']->set(array(
				'preview'	=> $image->inline()
			));
		}
		
		public static function create(Request $request, Application $app){
			$success = new helper\iterator();
			$error = new helper\iterator();
			
			$number = (new model())->load(array('number'), array(), array('number' => -1), 1)->getFirst()->get('number', 0) + 1;
			$request->request->set('number', $number);
			$request->request->set('winner', false);
			$request->request->set('enabled', false);
			$request->request->set('approved', false);
			$request->request->set('shared', false);
			
			$item = static::model()->set($request->request->all());
			if(count($request->files->all())){
				$item->setFile($request->files->all());
			}
			if (is_string($request->get('photofile'))) {
				$item->setFile('photofile', static::downloadImage($request->get('photofile')));
			}
			if (is_string($request->get('cocktailphoto'))) {
				$item->setFile('cocktailphoto', static::downloadImage($request->get('cocktailphoto')));
			}
			if (is_string($request->get('musephoto'))) {
				$item->setFile('musephoto', static::downloadImage($request->get('musephoto')));
			}
			
			$collage = Image::create(1200, 630, 'white');
			
			$userImage = Image::open($app['config']->get('paths')->get('root').$item->get('photofile')->getFirst()->get('route'));
			$userImage->zoomCrop(600, 630, 'white', 'center', 'center');
			
			$cocktailImage = Image::open($app['config']->get('paths')->get('root').$item->get('cocktailphoto')->getFirst()->get('route'));
			$cocktailImage->zoomCrop(600, 630, 'white', 'center', 'center');
			
			$layer = Image::open($app['config']->get('paths')->get('root').'/themes/share/layer.png');
			
			$collage->merge($userImage, 0, 0);
			$collage->merge($cocktailImage, 600, 0);
			$collage->merge($layer);
			
			$collage->write($app['config']->get('paths')->get('root').'/themes/share/intro.otf', mb_strtoupper('Участник №').$number, 90, 500, 10, 0, '#ccc', 'left');
			$collage->write($app['config']->get('paths')->get('root').'/themes/share/tinos.ttf', mb_strtoupper($item->get('name').' '.$item->get('surname')), 90, 540, 28, 0, 'white', 'left');
			$collage->write($app['config']->get('paths')->get('root').'/themes/share/intro.otf', mb_strtoupper($item->get('caption')), 1110, 540, 14, 0, 'ccc', 'right');
			
			$collageFile = tempnam('/tmp', 'collage-').'.png';
			$collage->save($collageFile, 'png');
			$item->setFile('collage', new File($collageFile));
			
			$result = $item->save();
			if ($result === true) {
				$sendTo = (new setting())->loadOne([], 'key', 'alertEmail');
				$body = sprintf('
						<h3>Новая анкета</h3>
						<p><b>ФИО</b>: %s %s</p>
						<p><b>Email</b>: <a href="mailto:%s">%s</a></p>
						<p><b>Телефон</b>: %s</p>
						<p><b>Ссылка</b>: <a href="%s">%s</a></p>
					',
					$item->get('surname'), $item->get('name'),
					$item->get('email'), $item->get('email'),
					$item->get('phone'),
					'http://'.$_SERVER['HTTP_HOST'].'/atom/#/profile/edit/'.$item->get('_id'), 'http://'.$_SERVER['HTTP_HOST'].'/atom/#/profile/edit/'.$item->get('_id')
				);
				$message = \Swift_Message::newInstance()
					->setFrom($app['config']->get('mailer')->get('from')->get())
					->setTo($sendTo->get('value'))
					->setSubject('Бехеровка / Новая анкета')
					->setBody($body, 'text/html');
				try {
					$app['mailer']->send($message);
				} catch (\Swift_RfcComplianceException $e) {
					
				}
				
				$app['log']->add(sprintf('Создание элемента — %s %s (%s://%s/atom/#/%s/edit/%s)', strtolower($item->getEntityTitle()), '«'.$item->get('title', $item->get('uid', (string)$item->get('_id'))).'»', $request->isSecure() ? 'https' : 'http', $request->getHost(), $item->getEntityName(), (string)$item->get('_id')));
				static::handleActionResult($item, $result, $success, $error);
				$response = array(
					'success'		=> array((string)$item->get('_id') => array('_id' => $item->get('_id'))),
					'shareUrl'		=> '/p/'.$item->get('_id').'/',
					'shareImage'	=> $item->get('collage')->getFirst()->get('route')
				);
				return $app['page']->set($response);
			}
			static::handleActionResult($item, $result, $success, $error);
			return static::makeResponse($app, $success, $error);
		}
		
		public static function stats(Request $request, Application $app){
	        $query = array(
				array('$group' => array(
					'_id'		=> '$region_id',
					'count'		=> array('$sum' => 1)
				)),
				array('$lookup' => array(
					'from'			=> 'profile',
					'localField'	=> '_id',
					'foreignField'	=> 'region_id',
					'as'			=> 'users'
				)),
				array('$project' => array(
					'_id'					=> false,
					'region_id'				=> '$_id',
					'count'					=> 1,
					'users.surname'			=> 1,
					'users.name'			=> 1,
					'users.bar'				=> 1,
					'users.city'			=> 1,
					'users.region'			=> 1,
					'users._id'				=> 1,
				))
			);
			$options = array(
	        	'allowDiskUse'	=> true
	        );
			$data = $app['db']->selectCollection('profile')->aggregate($query, $options);
			$response = array('total' => 0);
			foreach($data as $region) {
				$response[$region['region_id']] = $region;
				$response[$region['region_id']]['region'] = $region['users'][0]['region'];
				unset($response[(string)$region['region_id']]['region_id']);
				foreach($response[$region['region_id']]['users'] as $key => $user) {
					unset($response[$region['region_id']]['users'][$key]['region']);
				}
				$response['total'] += $region['count'];
			}
			return $app['page']->set($response);
		}
		
		private static function downloadImage($url) {
			$ch = curl_init($url);
			$filename = tempnam('/tmp', 'download-');
			$fp = fopen($filename, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
			return new File($filename);
		}
	}
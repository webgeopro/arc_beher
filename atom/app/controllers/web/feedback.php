<?php

	namespace app\controllers\web;
	use Silex\Application,
		Symfony\Component\HttpFoundation\Request,
		app\controllers\web\generic,
		app\models\setting\setting,
		app\models\helper,
		app\models\feedback\feedback as model;
		
	class feedback extends generic {
		public static function model(){
			return new model();
		}
		
		public static function create(Request $request, Application $app){
			$success = new helper\iterator();
			$error = new helper\iterator();
			
			$item = static::model()->set($request->request->all());
			if(count($request->files->all())){
				$item->setFile($request->files->all());
			}
			$result = $item->save();
			if ($result === true) {
				$sendTo = (new setting())->loadOne([], 'key', 'feedbackEmail');
				$sendTo = explode(';', $sendTo->get('value'));
				$body = sprintf('
					<h3>Заявка на обратную связь</h3>
					<p><b>Имя</b>: %s</p>
					<p><b>Email</b>: <a href="mailto:%s">%s</a></p>
					<p><b>Телефон</b>: %s</p>
					<p><b>Сообщение</b>: %s</p>
				', $item->get('title'), $item->get('email'), $item->get('email'), $item->get('phone'), strip_tags($item->get('message')));
				$message = \Swift_Message::newInstance()
					->setFrom($app['config']->get('mailer')->get('from')->get())
					->setTo($sendTo)
					->setSubject('Бехеровка / Заявка на обратную связь')
					->setBody($body, 'text/html');
				try {
					$app['mailer']->send($message);
				} catch (\Swift_RfcComplianceException $e) {
					
				}
				
				$app['log']->add(sprintf('Создание элемента — %s %s (%s://%s/atom/#/%s/edit/%s)', strtolower($item->getEntityTitle()), '«'.$item->get('title', $item->get('uid', (string)$item->get('_id'))).'»', $request->isSecure() ? 'https' : 'http', $request->getHost(), $item->getEntityName(), (string)$item->get('_id')));
			}
			static::handleActionResult($item, $result, $success, $error);
			return static::makeResponse($app, $success, $error);
		}
	}
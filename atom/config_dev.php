<?php

	return array(
		'encoding'	=> 'UTF-8',
		'mongo' => array(
			'server'	=> 'mongodb://localhost:27017',
			'database'	=> 'beherovka_dev',
			'options'	=> array(
				'connect'	=> true
			)
		),
		'paths' => array(
			'atom'		=> __DIR__,
			'root'		=> realpath(__DIR__.'/../'),
			'upload'	=> realpath(__DIR__.'/../upload')
		),
		'routes' => array(
			'frontend'	=> '/',
			'backend' 	=> '/atom/',
			'upload'	=> '/upload'
		),
		'hash' => array(
			'salt'	=> 'XJEnXG3Xi0n3eKXNe4xpYf',
			'cost'	=> 4
		),
		'mailer'	=> array(
			'to'	=> '',
			'from'	=> array('@yandex.ru' => 'Beherovka Mailer'),
			'smtp'	=> array(
				'host'	=> 'smtp.yandex.ru',
				'port'	=> 465,
				'mode'	=> 'ssl',
				'user'	=> '@yandex.ru',
				'pass'	=> 'pass'
			)
		),
		'seo'	=> array(
			'type'			=> 'website',
			'title'			=> 'Atom Project',
			'description'	=> 'Atom platform for everyone!',
			'image'			=> '/upload/share-default-image.png'
		)
	);
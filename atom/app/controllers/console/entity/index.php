<?php

	namespace app\controllers\console\entity;
	use Symfony\Component\Console\Command\Command,
		Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputInterface,
		Symfony\Component\Console\Output\OutputInterface,
		Symfony\Component\Console\Question\Question,
		Symfony\Component\Console\Question\ConfirmationQuestion,
		Symfony\Component\Filesystem\Exception\IOExceptionInterface,
		app\models\helper;
		
	class index extends Command{
		private $app;
		protected function configure(){
			global $app;
			$this->app = $app;
			$this
				->setName("entity:index")
				->setDescription("Rebuild datebase indexes");
		}

		protected function execute(InputInterface $input, OutputInterface $output){
			$this->app['db']->selectCollection('search')->remove(array());
			$output->writeln('<options=bold>Search content just truncated!</options=bold>');
			
			$entitiesSchemes = (new helper\helper)->glob($this->app['config']->get('paths')->get('atom').'/app/models/*.yml');
			foreach($entitiesSchemes as $scheme){
				$modelName = '\app\models\\'.pathinfo($scheme, PATHINFO_FILENAME).'\\'.pathinfo($scheme, PATHINFO_FILENAME);
				$model = new $modelName;
				$scheme = $model->getScheme();
				if (is_null($scheme)){
					continue;
				}
				$this->app['db']->selectCollection($model->getEntityName())->deleteIndexes();
				$search = array();
				foreach($scheme->get() as $fieldName => $fieldProperties){
					if (isset($fieldProperties['index']) && ($fieldProperties['index'] == 1 || $fieldProperties['index'] == -1)){
						$this->app['db']->selectCollection($model->getEntityName())->ensureIndex(array($fieldName => $fieldProperties['index']), array('background' => true));
					}
				}
				$this->app['db']->selectCollection('search')->ensureIndex(array('$**' => 'text'), array('default_language' => 'russian'));
			}
			$output->writeln('<options=bold>Database indexes just setted!</options=bold>');
			
			$pattern = $this->app['config']->get('paths')->get('atom').'/app/models/*.yml';
			$entities = (new helper\helper)->glob($pattern);
			foreach($entities as $scheme){
				$modelName = '\app\models\\'.pathinfo($scheme, PATHINFO_FILENAME).'\\'.pathinfo($scheme, PATHINFO_FILENAME);
				$model = new $modelName;
				$fields = array();
				foreach($model->getScheme()->all() as $field => $properties){
					if (@$properties['search']){
						$fields[] = $field;
					}
				}
				if (count($fields) == 0){
					continue;
				}
				$elements = $model->load(array(), ($model->getScheme()->has('enabled') ? array('enabled' => true) : array()));
				foreach($elements as $key => $element){
					$element->updateSearchContent();
					$output->writeln($element->getEntityName().' / '.$element->get('_id'));
					$elements->pop($key);
				}
			}
			$output->writeln('<options=bold>Search content just fully updated!</options=bold>');
		}
	}
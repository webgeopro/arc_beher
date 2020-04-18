<?php

	namespace app\controllers\console\entity;
	use Symfony\Component\Console\Command\Command,
		Symfony\Component\Console\Input\InputArgument,
		Symfony\Component\Console\Input\InputInterface,
		Symfony\Component\Console\Output\OutputInterface,
		Symfony\Component\Console\Question\Question,
		Symfony\Component\Console\Question\ConfirmationQuestion,
		Symfony\Component\Filesystem\Exception\IOExceptionInterface,
		app\models\helper,
		app\models\search\search as model;
		
	class search extends Command{
		private $app;
		protected function configure(){
			global $app;
			$this->app = $app;
			$this
				->setName("entity:search")
				->setDescription("Search content");
		}
		protected function execute(InputInterface $input, OutputInterface $output){
			$helper = $this->getHelper('question');
			do {
				$question = new Question('Search for: ');
				$query = $helper->ask($input, $output, $question);
			} while(!$query);
			$search = (new model)->load(array(), array(
				'$search'	=> $query
			));
			foreach($search as $key => $element){
				$output->writeln(($key + 1).'	|	'.$element->get('ref_entity').' / '.$element->get('ref_id'));
			}
			$output->writeln('<options=bold>Found '.$search->count().' elements!</options=bold>');
		}
	}
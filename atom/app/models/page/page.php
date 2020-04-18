<?php

	namespace app\models\page;
	use Symfony\Component\HttpFoundation\Response,
		app\models\helper,
		app\models\block\block,
		app\models\generic\generic;

	/**
	 * Pages
	 */
	class page extends generic{
		private
		/**
		 * Response object
		 */
		$response = null,
		
		/**
		 * SEO object
		 */
		$seo = null;



		public function __construct(){
			$this->response = new Response();
			$this->seo = new helper\proto();
			return parent::__construct();
		}
		
		/**
		 * Return Response object
		 * 
		 * @return object
		 */
		public function response(){
			return $this->response;
		}
		
		/**
		 * Return SEO object
		 * 
		 * @return object
		 */
		public function seo(){
			return $this->seo;
		}
		
		/**
		 * Return Response object content
		 * 
		 * @return string
		 */
		public function __toString(){
			return $this->response()->getContent();
		}
		
		/**
		 * Load child pages by id
		 *
		 * @param string $id MongoId
		 * @return iterator
		 */
		public function loadChilds($id = null){
			return $this->load(array(), array(
				'pid'	=> ($id === 0 ? 0 : $this->mongoid($id))
			));
		}

		/**
		 * Load parent pages by url
		 *
		 * @return array of page objects
		 */
		public function loadCrumbs($url = null){
			$urlParts = $this->urlParts($url)->get('all');
			$crumbs = array();
			foreach($urlParts as $key => $part){
				$prev = end($this->crumbs) ? end($this->crumbs) : '/';
				$crumbs[$key] = $prev.$part.'/';
			}
			return $this->load(array(), array(
				'url'	=> array('$in' => $crumbs)
			));
		}

		/**
		 * Save page
		 *
		 * @return true|array Return True if success or Array of alerts if error
		 */
		public function save(){
			$url = '/'.trim($this->get('url'), '/').'/';
			$url = (strlen($url) == 2 ? '/' : $url);
			$this->set(array(
				'url'		=> $url,
				'pid'		=> ($this->get('pid') ? $this->mongoid($this->get('pid')) : 0),
				'level'		=> $this->urlParts($url)->get('level')
			));
			return parent::save();
		}

		/**
		 * Remove page
		 *
		 * @return true|array Return True if success or Array of alerts if error
		 */
		public function remove($recursive = true){
			if ($recursive){
				$childs = (new static)->loadChilds($this->get('_id'));
				foreach($childs as $child){
					$child->remove($recursive);
				}
			}
			return parent::remove();
		}

		/**
		 * URL parts
		 *
		 * @return object proto
		 */
		public function urlParts($url = null){
			$parts = explode('/', trim((is_null($url) ? $this->get('url') : $url), '/'));
			return (new helper\proto())->set(array(
				'all'	=> $parts,
				'count'	=> count($parts),
				'level'	=> count($parts) - 1 > 0 ? count($parts) - 1 : 0,
				'first'	=> $parts[0],
				'last'	=> end($parts)
			));
		}

		/**
		 * Render page template
		 *
		 * $param array $data Array of data vars
		 * @param integer $code HTTP status code
		 * @return this
		 */
		public function render($data = array()){
			$content = '';
			if (is_null($this->app['request']->attributes->get('_template', null))){
				if (count((array)$data)){
					$content = $this->app['serializer']->serialize($data, 'json');
				} else {
					$content = $this->response()->getContent();
				}
			} else {
                $content = $this->app['view']->render($this->app['request']->attributes->get('_template'), $data);
            }
			$this->response()->setContent($content);
			return $content;
		}
	}
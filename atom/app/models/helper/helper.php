<?php

	namespace app\models\helper;

	class helper{
		public function __construct(){
			global $app;
			$this->app = $app;
		}

		/**
		 * Recursive scan directories by pattern
		 *
		 * @param string $patterns Simple pattern
		 * @param conts $flags Glob function flags
		 * @return array
		 */
		public function glob($patterns, $flags = 0){
			$files = array();
			foreach((array)$patterns as $pattern){
				$files = array_merge($files, glob($pattern, $flags));
				$dirs = glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT);
				if (empty($dirs)) continue;
				foreach ($dirs as $dir){
					$files = array_merge($files, $this->glob($dir.'/'.basename($pattern), $flags));
				}
			}
			return $files;
		}

		/**
		 * Password generator
		 *
		 * @param integer $length Length of password, max 32
		 * @return string
		 */
		public function pwgen($length = 6){
			return substr(md5(uniqid()), 0, ($length <= 32 ? $length : 32));
		}

		/**
		 * Russian word forms
		 *
		 * @param integer $number Number used for word
		 * @param array $words Array of word in 3 cases
		 * @return string
		 */
		public function wordForm($number, $words){
			$cases = array (2, 0, 1, 1, 1, 2);
			return $words[ ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)] ];
		}
	}
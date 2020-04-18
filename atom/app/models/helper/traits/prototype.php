<?php

	namespace app\models\helper\traits;
	use app\models\helper;
	use Symfony\Component\HttpFoundation\File\File;

	trait prototype{
		private
		/**
		 * @var Data params
		 */
		$data = array();



		/**
		 * Set data
		 *
		 * @param array|string $data Data params
		 * @param string $value Value for only one param
		 * @return this
		 */
		public function set($data, $value = null){
			if (is_array($data)){
				$this->data = array_replace($this->data, $data);
			} else if ($data) {
				$this->data[$data] = $value;
			}
			return $this;
		}

		/**
		 * Unset data
		 *
		 * @param string $option Data param
		 * @return this
		 */
		public function pop($option = null){
			if (is_null($option) == false){
				unset($this->data[$option]);
			} else {
				$this->data = array();
			}
			return $this;
		}

		/**
		 * Get data
		 *
		 * @param string $option Data param
		 * @param string $default Default value for data option
		 * @return mixed
		 */
		public function get($option = null, $default = null){
			if (is_null($option) == false){
				if ($this->has($option)){
					if (is_array($this->data[$option])){
						reset($this->data[$option]);
						if (key($this->data[$option]) === 0){
							$iterator = (new helper\iterator())->setTotal(count($this->data[$option]));
							foreach($this->data[$option] as $key => $value){
								$iterator->add($value);
							}
							return $iterator;
						}
						return (new helper\proto())->set($this->data[$option]);
					} else {
						return $this->data[$option];
					}
				}
				return $default;
			}
			return $this->data;
		}

		/**
		 * Get all data
		 *
		 * @return array
		 */
		public function all(){
			return $this->data;
		}

		/**
		 * Get all data as array
		 *
		 * @return array
		 */
		public function toArray(){
			$array = array();
			foreach($this->all() as $key => $value){
				if (is_object($value)){
					switch(get_class($value)){
						case 'app\models\helper\proto':
							$array[$key] = $value->all();
						break;
						case 'app\models\helper\iterator':
							foreach($value as $iKey => $iValue){
								$array[$key][$iKey] = $iValue;
							}
						break;
						default:
							$array[$key] = $value;
						break;
					}
				} else {
					$array[$key] = $value;
				}
			}
			return $array;
		}

		/**
		 * Check data option
		 *
		 * @param string $option Data param
		 * @return boolean
		 */
		public function has($option){
			if (isset($this->data[$option])){
				return true;
			}
			return false;
		}

		/**
		 * Check data value
		 *
		 * @param string $value Data value
		 * @return boolean
		 */
		public function hasValue($value){
			if (in_array($value, $this->data)){
				return true;
			}
			return false;
		}

		/**
		 * Has data _id
		 *
		 * @param boolean $checkType Check _id for MongoId object
		 * @return boolean
		 */
		public function hasId($checkType = true){
			if ($this->get('_id')){
				if ($checkType && get_class($this->get('_id')) !== 'MongoId'){
					return false;
				}
				return true;
			}
			return false;
		}
	}
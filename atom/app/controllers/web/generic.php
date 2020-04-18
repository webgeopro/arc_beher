<?php

	namespace app\controllers\web;
	use Silex\Application,
		Symfony\Component\HttpFoundation\Request,
		Symfony\Component\Validator\Constraints as Assert,
		Symfony\Component\Validator\Constraints\Collection as Asserts,
		app\models\helper;

	class generic {
		public static function create(Request $request, Application $app){
			$success = new helper\iterator();
			$error = new helper\iterator();
			$item = static::model()->set($request->request->all());
			if(count($request->files->all())){
				$item->setFile($request->files->all());
			}
			$result = $item->save();
			$app['log']->add(sprintf('Создание элемента — %s %s (%s://%s/atom/#/%s/edit/%s)', strtolower($item->getEntityTitle()), '«'.$item->get('title', $item->get('uid', (string)$item->get('_id'))).'»', $request->isSecure() ? 'https' : 'http', $request->getHost(), $item->getEntityName(), (string)$item->get('_id')));
			static::handleActionResult($item, $result, $success, $error);
			return static::makeResponse($app, $success, $error);
		}

		public static function read(Request $request, Application $app){
			if ($request->get('_id')){
				$request->query->set('condition', array('_id' => static::model()->mongoid($request->get('_id'))));
			}
			$fields = static::prepareFields($request->get('fields', array()));
			$condition = static::prepareCondition($request->get('condition', array()));
			$sort = static::prepareSort($request->get('sort', array()));
			$limit = (int)$request->get('limit', 10);
			$skip = ($request->get('skip') ? (int)$request->get('skip', 0) : (int)$request->get('page', 0) * $limit);
			$items = static::model()->load($fields, $condition, $sort, $limit, $skip);
			return $app['page']->set(array(
				'total'	=> $items->getTotal(),
				'data'	=> $items->all()
			))->response()->setStatusCode($items->count() ? 200 : 204);
		}

		public static function update(Request $request, Application $app){
			$success = new helper\iterator();
			$error = new helper\iterator();
			if ($request->get('_id')){
				$item = static::model()->loadById($request->get('_id'))->set($request->request->all());
				if(count($request->files->all())){
					$item->setFile($request->files->all());
				}
				$result = $item->save();
				$app['log']->add(sprintf('Изменение элемента — %s %s (%s://%s/atom/#/%s/edit/%s)', strtolower($item->getEntityTitle()), '«'.$item->get('title', $item->get('uid', (string)$item->get('_id'))).'»', $request->isSecure() ? 'https' : 'http', $request->getHost(), $item->getEntityName(), (string)$item->get('_id')));
				static::handleActionResult($item, $result, $success, $error);
			}

			return static::makeResponse($app, $success, $error);
		}

		public static function delete(Request $request, Application $app){
			$success = new helper\iterator();
			$error = new helper\iterator();
			if ($request->get('_id')){
				$item = static::model()->loadById($request->get('_id'));
				$result = $item->remove();
				$app['log']->add(sprintf('Удаление элемента — %s %s (%s://%s/atom/#/%s/edit/%s)', strtolower($item->getEntityTitle()), '«'.$item->get('title', $item->get('uid', (string)$item->get('_id'))).'»', $request->isSecure() ? 'https' : 'http', $request->getHost(), $item->getEntityName(), (string)$item->get('_id')));
				static::handleActionResult($item, $result, $success, $error);
			}

			return static::makeResponse($app, $success, $error);
		}
		
		public static function export(Request $request, Application $app){
			$model = static::model();
			$scheme = $model->getScheme();
			$date = date('d.m.Y');
			$rows = new helper\iterator();
			$alphabet = array();
			for ($char = 'A'; $char !== 'AZZ'; $char++) {
				$alphabet[] = $char;
			}
			$xls = new \PHPExcel();
			$xls->getProperties()
				->setCreator('Atom')
				->setTitle('Export_'.ucfirst($model->getEntityName()).'_'.$date)
				->setSubject($model->getEntityTitle());
			$xls->setActiveSheetIndex(0);
			$xls->getDefaultStyle()->getFont()->setName('Arial');
			$xls->getDefaultStyle()->getFont()->setSize(10);
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle($model->getEntityTitle());

			$row = array('_id');
			foreach($scheme->all() as $fieldName => $field){
				$field = (new helper\proto())->set($field);
				if ($field->get('visible', true) === false || in_array($field->get('type'), array('password', 'acl', null))){
					continue;
				}
				$row[] = $field->get('title');
			}
			$rows->add($row);
			
			$fields = static::prepareFields($request->get('fields', array()));
			$condition = static::prepareCondition($request->get('condition', array()));
			$sort = static::prepareSort($request->get('sort', array()));
			$items = $model->load($fields, $condition, $sort);
			foreach($items as $key => $item){
				$row = array((string)$item->get('_id'));
				foreach($scheme->all() as $fieldName => $field){
					$field = (new helper\proto())->set($field);
					if ($field->get('visible', true) === false || in_array($field->get('type'), array('password', 'acl', null))){
						continue;
					}
					switch($field->get('type')){
						case 'integer':
						case 'numeric':
							if ($item->get($fieldName) instanceof \Traversable){
								$value = implode(', ', $item->get($fieldName)->all());
							} else {
								$value = $item->get($fieldName) * 1;
							}
						break;
						case 'date':
						case 'datetime':
							if (is_null($item->get($fieldName)) || $item->get($fieldName)->sec * 1 === 0){
								$value = '-';
							} else {
								$value = date('Y-m-d H:i:s (T)', $item->get($fieldName)->sec);
							}
						break;
						case 'boolean':
							$value = ($item->get($fieldName) ? '+' : '-');
						break;
						case 'select':
							$value = $scheme->get($fieldName)->get('values')->get($item->get($fieldName));
						break;
						case 'entity':
							$value = '';
							$data = $item->getRelated($fieldName);
							foreach($data as $object){
								$value .= $object->get($field->get('entity')->get('field'))."\n";
							}
							$value = trim($value);
						break;
						case "image":
							if (is_null($item->get($fieldName))){
								$value = null;
								continue;
							}
							$image = method_exists($item->get($fieldName), 'getFirst') ? $item->get($fieldName)->getFirst() : $item->get($fieldName);
							$file = $app['config']->get('paths')->get('root').$image->get('route');
							if (!file_exists($file)){
								$value = null;
								continue;
							}
							if ($image->get('width') >= 207){
								$width = 207;
								$height = round($width * $image->get('height') / $image->get('width'));
							} else {
								$height = ($image->get('height') >= 207 ? 207 : $image->get('height'));
								$width = round($height * $image->get('width') / $image->get('height'));
							}
							$value = new \PHPExcel_Worksheet_Drawing();
							$value->setName($fieldName);
							$value->setDescription($fieldName);
							$value->setPath($file);
							$value->setWidthAndHeight($width, $height);
						break;
						case 'html':
							$value = strip_tags($item->get($fieldName));
						break;
						default:
							if ($item->get($fieldName) instanceof \Traversable || $item->get($fieldName) instanceof helper\proto){
								$value = implode(', ', $item->get($fieldName)->all());
							} else {
								$value = $item->get($fieldName);
							}
						break;
					}
					$row[] = $value;
				}
				$rows->add($row);
				$items->pop($key);
			}
			
			foreach($rows as $rowKey => $row){
				foreach($row as $colKey => $value){
					$cell = $alphabet[$colKey].($rowKey + 1);
					if ($rowKey == 0){
						$sheet->getStyle($cell)->getFont()->setBold(true);
						$sheet->getColumnDimension($alphabet[$colKey])->setWidth(30);
						$sheet->freezePane('A2');
						$sheet->getColumnDimension('A')->setVisible(false);
					}
					if (is_object($value) && get_class($value) == 'PHPExcel_Worksheet_Drawing'){
						$value->setCoordinates($cell);
						$value->setWorksheet($sheet);
						$sheet->getRowDimension($rowKey + 1)->setRowHeight(ceil($value->getHeight() * 0.77));
					} else {
						$sheet->setCellValue($cell, $value);
					};
					$sheet->getStyle($cell)->getAlignment()->setWrapText(true);
					$sheet->getStyle($cell)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
				}
			}
			
			ob_start();
			$file = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			$file->save('php://output');
			$app['page']->response()->setContent(ob_get_clean());
			$app['page']->response()->headers->replace(array(
				'Content-Type'			=> 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'Content-Disposition'	=> 'attachment;filename="'.$xls->getProperties()->getTitle().'.xlsx"'
			));
			return $app['page'];
		}

		public static function prepareFields($fields){
			return (array)$fields;
		}

		public static function prepareCondition($condition, $pKey = null){
			$scheme = static::model()->getScheme();
			foreach($condition as $key => $value){
				if (is_array($value)){
					if (isset($value['$regex'])){
						$value = new \MongoRegex('/'.$value['$regex'].'/i');
					} else {
						$value = static::prepareCondition($value, $key);
					}
				} else {
					if ($value === (string)static::model()->mongoid($value)){
						$value = static::model()->mongoid($value);
					}
					switch($scheme->get(($pKey ? $pKey : $key), new helper\proto)->get('type')){
						case 'integer':
						case 'numeric':
							if (is_numeric($value)){
								$value = $value * 1;
							}
						break;

						case 'boolean':
							if ($value === 'true'){
								$value = true;
							} elseif ($value === 'false'){
								$value = false;
							}
						break;

						case 'date':
						case 'datetime':
						case 'time':
							if (strtotime($value)){
								$value = new \MongoDate(strtotime($value));
							}
						break;
					}
				}
				$condition[$key] = $value;
			}
			return $condition;
		}

		public static function prepareSort($sort){
			foreach($sort as $key => $value){
				$sort[$key] = $value * 1;
			}
			return $sort;
		}

		public static function handleActionResult($item, $result, helper\iterator &$success, helper\iterator &$error){
			if ($result === true){
				$success->add(array((string)$item->get('_id') => array('_id' => $item->get('_id'))));
			} elseif ($result === false){
				$error->add(array((string)$item->get('_id') => array('_id' => $item->get('_id'))));
			} else {
				$error->set((string)$item->get('_id', 'null'), static::remakeAlerts($result));
			}
		}

		public static function remakeAlerts($asserts){
			$alerts = new helper\iterator;
			foreach($asserts as $alert){
				$alerts->add(array(
					'field'		=> $alert->getPropertyPath(),
					'message'	=> $alert->getMessage()
				));
			}
			return $alerts->all();
		}

		public static function makeResponse(Application &$app, helper\iterator $success, helper\iterator $error){
			if ($success->count() && $error->count()){
				$statusCode = 207;
				$response = array(
					'success'	=> $success->all(),
					'error'	=> $error->all()
				);
			} elseif ($success->count()){
				$statusCode = 200;
				$response = array(
					'success'	=> $success->all()
				);
			} elseif ($error->count()){
				$statusCode = 400;
				$response = array(
					'error'	=> $error->all()
				);
			} else {
				$statusCode = 400;
				$response = array();
			}
			return $app['page']->set($response)->response()->setStatusCode($statusCode);
		}
	}
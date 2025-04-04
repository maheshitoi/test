<?php
namespace Core\Infrastructure\Persistence;

use CodeIgniter\Model;

/**
 * @internal
 * @property Model $model
 */
trait DMLPersistence {
	public function globalJoin() {
		$this->model->builder();
	}
	public function save($data = null) {
		if (gettype($data) == 'array') {
			if (count($data) !== count($data, COUNT_RECURSIVE)) {
				$insertBa = array();
				$updateBa = array();
				foreach ($data as $key => $value) {
					if (isset($value[$this->model->primaryKey]) && !empty($value[$this->model->primaryKey])) {
						array_push($updateBa, $value);
					} else {
						array_push($insertBa, $value);
					}
					if (isset($value['action'])) {
						if ($value['action'] == 3 || $value['action'] == '3') {
							$this->deleteOfId($value['id']);
						}
					}
					if (method_exists($this->model, 'beforeSave')) {
						$this->model->beforeSave(array('data' => $value));
					}
				}
				if (!empty($insertBa)) {
					$this->model instanceof Model && $this->model->allowCallbacks(true)->insertBatch($insertBa);
				}if (!empty($updateBa)) {
					$this->model instanceof Model && $this->model->allowCallbacks(true)->updateBatch($updateBa, $this->model->primaryKey);
				}
				return true;
			}
			return $this->model->allowCallbacks(true)->save($data);

		}

		return $this->model instanceof Model && $this->model->allowCallbacks(true)->save($data);
	}

	public function insert($data = null) {
		if (gettype($data) == 'array') {
			if (count($data) !== count($data, COUNT_RECURSIVE)) {return $this->model->allowCallbacks(true)->insertBatch($data);}
		}
		return $this->model->allowCallbacks(true)->insert($data);
	}
	public function findAllPagination($ftbl, $isActive = true) {
		$this->globalJoin();
		if (!$isActive) {
			$this->model->onlyDeleted();
		}
		return $this->paginationQuery($ftbl, $this->defaultMapCol ?? []);
	}

	public function updateById($id, $data) {
		return $this->model->set($data)->update($id);
	}
	public function update($cond, $data, $escData = false) {
		//['church_id IN' => "(" . $data['church'] . ")"]
		$this->model->where($cond);
		if (!empty($data)) {
			$this->model->set($data);
		}
		if ($escData && is_array($escData)) {
			foreach ($escData as $key => $value) {
				$this->model->set($key, $value, false);
			}
		}
		return $this->model->update();
	}

	public function deleteOfId($id) {
		return $this->model instanceof Model && $this->model->allowCallbacks(true)->delete($id);
	}

	public function deleteWhere($cond) {
		return $this->model instanceof Model && $this->model->allowCallbacks(true)->where($cond)->delete();
	}

	public function findById($id, $delAction = null) {
		$this->globalJoin();
		return $this->model->asArray()->allowCallbacks(true)->withDeleted()->find($id);
	}
	public function findAll() {
		$this->globalJoin();
		return $this->model->asArray()->orderBy('created_at', 'DESC')->allowCallbacks(true)->withDeleted()->findAll();
	}

	public function findAllBystatus() {
		return $this->model->asArray()->where('status', true)->allowCallbacks(true)->withDeleted()->findAll();
	}
	public function findAllByWhere($cond, $limit = 0, $whereIn = [], $delAction = null) {
		$this->globalJoin();
		$this->conditionApply($cond, $whereIn);
		$this->model->asArray()->allowCallbacks(true);
		if (isset($this->model->useSoftDeletes) && $this->model->useSoftDeletes) {
			if ($delAction === false) {
				$this->model->withDeleted();
			} else if ($delAction === true) {
				$this->model->onlyDeleted();
			}
		}
		return $this->model->findAll($limit) ?? [];
	}
	public function findAllByWherePage($cond, $limit = 0, $page = 0) {
		$this->globalJoin();
		$this->conditionApply($cond);
		return $this->model->asArray()->allowCallbacks(true)->findAll($limit, $page) ?? [];
	}

	protected function conditionApply($cond, $whereIn = []) {
		if (count($whereIn)) {
			$whereIn = $this->mapCondition($whereIn);
			foreach ($whereIn as $wk => $wv) {
				$this->model->whereIn($wk, $wv);
			}
		}
		$cond = $this->mapCondition($cond);
		$this->model->where($cond);
	}

	public function avgMax($select, $match = 'SUM', $cond = [], $type = 'ACTIVE', $join = false) //ACTIVE ,INACTIVE ,BOTH

	{
		$matchClass = 'selectSum';
		switch ($match) {
		case 'COUNT':
			$matchClass = 'selectCount';
			break;
		case 'AVG':
			$matchClass = 'selectAvg';
			break;
		case 'MIN':
			$matchClass = 'selectMin';
			break;
		case 'MAX':
			$matchClass = 'selectMax';
			break;
		}
		if ($join) {
			$this->globalJoin();
		}
		if (count($cond)) {
			$cond = $this->mapCondition($cond);
			$this->model->where($cond, false);
		}
		if ($this->model->useSoftDeletes) {
			if ($type == 'ACTIVE') {
				$this->model->where(['deleted_at is null ' => null]);
			} else if ($type == 'INACTIVE') {
				$this->model->onlyDeleted();
			} else {
				$this->model->withDeleted();
			}
		}
		$d = $this->model->{$matchClass}($select)->asArray()->get()->getRow();
		return $d->{$select};
	}

	public function countAll($withDeleted = false, $cond = []) {
		if (count($cond)) {
			$cond = $this->mapCondition($cond);
			$this->model->where($cond, false);
		}
		if (!$withDeleted) {
			$this->model->where(['deleted_at is null ' => null]);
		}
		return $this->model->countAllResults();
	}

	private function mapCondition(&$cond, $addtionalKeys = [], $mInstance = 'model') {
		//example ['col' => 'val'] into ['s.col' => 'val']
		$reservKey = ['status', 'created_at', 'deleted_at', 'updated_at'];
		$reserverdCol = [];
		foreach ($reservKey as $kV) {
			$reserverdCol[$kV] = $this->{$mInstance}->table . ".$kV";
		}
		$condKey = array_merge($this->overWriteConditionKey ?? [], $addtionalKeys, $reserverdCol);
		$cond = $this->getMappedColKey($cond, $condKey);
		return $cond;
	}

	protected function getMappedColKey($key, $arrayMap = []) {
		$keys = is_array($key) ? $key : [$key];
		//two option = [{colName : 'ex',value : 'sd'}] or ['colName' => 'vale']
		foreach ($keys as $k => $v) {
			$col = isset($v->colName) ? $v->colName : ((int) $k ? $v : $k);
			$val = $this->singleColMap($col, $arrayMap);
			if (isset($v->colName)) {
				$keys[$k]->colName = $val;
			} else if (is_numeric($k)) {
				// single string
				$keys = $val;
			} else {
				//map old value with new key
				$keys[$val] = $keys[$col];
				if ($val != $col) {
					unset($keys[$col]);
				}
			}
		}
		return $keys;
	}

	protected function singleColMap($colName, $arrayMap = [], $mInstance = 'model') {
		$split = explode(' ', $colName);
		$col = $split[0];
		if (is_array($arrayMap) && array_key_exists($col, $arrayMap)) {
			$split[0] = $arrayMap[$col];
			return implode(" ", $split);
		}
		return implode(" ", $split);
	}

	protected function condTerms() {

	}
	private function setBeweenDate($col, $startDate, $endDate, $modInstance = 'model') {
		$this->{$modInstance}->where('DATE(' . $col . ') BETWEEN ' . '"' . date('Y-m-d', strtotime($startDate)) . '"' . ' AND ' . '"' . date('Y-m-d', strtotime($endDate)) . '"');
	}

	private function groupTerms($ftbl, $commonCol, $terms = ['searchTerms']) {
		if (isset($ftbl->queryParams) && count($ftbl->queryParams)) {
			foreach ($ftbl->queryParams as $key => $v) {
				if (in_array($v->colName, $terms)) {
					if ($v->value) {
						$this->model->groupStart();
						$i = 0;
						foreach ($commonCol as $ck => $cv) {
							if ($i == 0) {
								$this->model->like($cv, $v->value);
							} else {
								$this->model->orLike($cv, $v->value);
							}
							$i++;
						}
						$this->model->groupEnd();
					}
					unset($ftbl->queryParams[$key]);
				}
			}
		}
	}

	protected function setWhere($query, $mapKey = [], $mInstance = 'model') {
		$condArray = (array) $query;
		$reservKey = ['status', 'created_at', 'deleted_at', 'updated_at'];
		$reserverdCol = [];
		foreach ($reservKey as $kV) {
			$reserverdCol[$kV] = $this->{$mInstance}->table . ".$kV";
		}
		$mapKey = array_merge($this->overWriteConditionKey ?? [], $mapKey, $reserverdCol);

		if (is_array($condArray) && count($condArray)) {
			foreach ($condArray as $key => $v) {
				$v = (object) $v;
				$v->colName = $this->singleColMap($v->colName, $mapKey, $mInstance);
				$exp = explode('.', $v->colName);
				if (!isset($exp[1]) && (isset($v->operation) && !in_array($v->operation, ['havingLike', 'having']))) {
					$v->colName = $this->model->table . '.' . $v->colName;
				}
				$val = $v->value ?? '';
				// check and convert array
				if (!empty($val) && is_string($val) && strpos($val, ',') !== false) {
					$val = explode(',', $val);
				} else if (is_array($val) && !count($val) == 0) {
					$isArray = true;
					if (!isset($val[1])) {
						$val = explode(',', $val[0]);
						if ((int) $val <= 0) {
							$val = [];
						}
					}
					$val = array_filter($val, function ($a) {return ($a != 0 && $a != -1);});
					if (empty($val) || (is_array($val) && count($val) == 0)) {
						// echo 'cotinue called';
						continue;
					}
				}

				// echo $v->colName.' => '.$val instanceof Traversable;

				$colName = $v->colName;
				if ($val == 'null' || $val == null) {
					$val = null;
					if (!str_contains(strtolower($colName), 'null')) {
						$colName = $colName . ' is null ';
					}
				}
				$escape = true;
				//check escap
				if (str_contains(strtolower($colName), 'null')) {
					$escape = false;
				}
				if (isset($v->matchMode)) {
					$v->operation = !empty($v->matchMode) ? $v->matchMode : 'contains';
				}

				switch ($v->operation ?? 'AND') {
				case 'OR':
					$this->{$mInstance}->orWhere($colName, $val);
					break;
				case 'havingLike':
					$this->{$mInstance}->havingLike($colName, $val);
					break;
				case 'having':
					$this->{$mInstance}->having($colName, $val, false);
					break;
				case 'contains':
					$this->{$mInstance}->like($colName, $v->value);
					break;
				case '<':
					$this->{$mInstance}->where($colName . ' < ', $v->value);
					break;
				case '>':
					$this->{$mInstance}->where($colName . ' > ', $v->value);
					break;
				case '!=':
					$this->{$mInstance}->where($colName . ' != ', $v->value);
					break;

				default:
					if (is_array($val) && !count($val) == 0) {
						$this->{$mInstance}->whereIn($colName, $val);
					} else {
						$this->{$mInstance}->where($colName, $val, $escape);
					}
					break;

				}

			}
		}

	}
	public function paginationQuery($ftbl, $mapKey = [], $mInstance = 'model') {
		if ($ftbl) {
			if (isset($ftbl->queryParams) && count($ftbl->queryParams)) {
				$this->setWhere($ftbl->queryParams, $mapKey, $mInstance);
			}
			if (isset($ftbl->whereField) && count($ftbl->whereField)) {
				$this->setWhere($ftbl->whereField, $mapKey, $mInstance);
			}

			//echo $this->{$mInstance}->builder()->getCompiledSelect();
			$result['totalRecord'] = $this->{$mInstance}->countAllResults(false);
			if (isset($ftbl->sort)) {
				if (count($ftbl->sort)) {
					$sort = $ftbl->sort;
					foreach ($sort as $key => $v) {
						$v = (object) $v;
						$v->colName = array_key_exists($v->colName, $mapKey) ? $mapKey[$v->colName] : $v->colName;
						$this->{$mInstance}->orderBy($v->colName, $v->sortOrder, false);
					}
				}
			} else {
				$this->{$mInstance}->orderBy($this->{$mInstance}->table . '.created_at', 'desc');
			}
			//echo $this->{$mInstance}->getCompiledSelect();
			$result['data'] = $this->{$mInstance}->asArray()->allowCallbacks(true)->findAll($ftbl->rows, ($ftbl->rows * $ftbl->page));
			return $result;
		}
	}
}

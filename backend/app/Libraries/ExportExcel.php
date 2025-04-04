<?php
namespace Core\Libraries;

class ExportExcel {
	private $response;
	private $frontLine = '';
	public function __construct() {
		$this->response = \Config\Services::response();
	}

	function setFrontLine($d) {
		$this->frontLine = $d;
	}

	public function export($data, $cols) {
		ini_set('memory_limit', '812M');
		set_time_limit(0);
		$colArray = [];
		$AryOfCol = [];
		foreach ($cols as $k => $vofc) {
			if (isset($cols[$k])) {
				if (is_array($cols[$k]) && $k != 'condition') {
					$AryOfCol[$k] = 0;
					$colArray = array_merge($colArray ?? [], array_column($cols[$k], 'title'));
				}

			}
		}
		$filename = "export_data_" . date('Ymd') . ".xls";
		$sep = "\t";
		$newLine = "\n";
		$header = implode($sep, $colArray);
		$body = $newLine;
		foreach ($data as $dk => $dv) {
			$counts = array_map(function ($v) {return isset($v[0]) ? count($v) : 1;}, $dv);
			$key = array_flip($counts)[max($counts)];
			$max = max($counts);
			for ($i = 0; $i < $max; $i++) {
				$schema_insert = "";
				foreach ($AryOfCol as $k => $ran) {
					foreach ($cols[$k] as $kof => $vofCols) {
						$vof = isset($dv[$k][$i]) ? ($dv[$k][$i][$vofCols['colName']] ?? '') : ($i == 0 ? $dv[$k][$vofCols['colName']] ?? '' : '');
						if (isset($vofCols['type']) && $vofCols['type'] == 'BOOLEAN' && $vof != '') {
							$vof = $vof ? 'Yes' : 'No';
						}
						if (isset($vofCols['data']) && !empty($vofCols['data'])) {
							foreach ($vofCols['data'] as $dak => $dav) {
								$keyName = $vofCols['selectPrimaryKey'] ?? 'id';
								if ($vof == $dav[$keyName]) {
									$vName = $vofCols['selectKeyName'] ?? 'name';
									$vof = $dav[$vName];
								}
							}
						}
						$schema_insert .= "$vof" . $sep;

					}
				}
				$schema_insert = str_replace($sep . "$", "", $schema_insert);
				$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
				//$schema_insert .= $sep;
				$body .= $schema_insert . $newLine;
			}
			// end of line
		}
		$this->response->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')->noCache();
		$header = !empty($this->frontLine) ? $this->frontLine . $newLine . $header : $header;
		return $this->response->download($filename, $header . $body, true);
	}

	public function mapColName($map, $data) {
		$result;
		foreach ($map as $k => $v) {
			if (isset($data[$v['colName']])) {
				if (isset($v['data'])) {
					foreach ($v['data'] as $dk => $dv) {
						if ($data[$v['colName']] == $dv['id']) {
							$result[$v['title']] = $dv['name'];
						}
					}
					//print_r($v['data']);
				} else {
					$result[$v['title']] = $data[$v['colName']];
				}

			}
		}
		return $result;
	}

	private function excelFeed($data, $count = '1') {
		$sep = "\t";
		$fillArr = array_fill(0, $count, "" . $sep);
		$i = 0;

		return implode(",", $fillArr);
	}

	private function array_values_recursive($ary) {
		$lst = array();
		foreach (array_keys($ary) as $k) {
			$v = $ary[$k];
			if (is_scalar($v)) {
				$lst[] = $v;
			} elseif (is_array($v)) {
				$lst = array_merge($lst,
					$this->array_values_recursive($v)
				);
			}
		}
		return $lst;
	}

}

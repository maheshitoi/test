<?php
use Core\Libraries\BackgroundProcess;

function addTask($url, $msg, $user_id=null, $ref_id=null, $type = 1, $do_exe = true) {
	$data = array(
		'created_by' => $user_id,
		'description' => $msg,
		'status' => 3, //Added
		'ref_id' => $ref_id,
		'type' => $type ?? 1,
		'comment_text' => BASEURL . 'api/v1/' . $url,
	);
	$db = \Config\Database::connect('master');
	$db->table('background_task')->insert($data);
	$tid = $db->insertID();
	if ($do_exe) {
		return startTask($tid);
	}
	return $tid;
}
function getProcessTask($ref_id, $type) {
	$db = \Config\Database::connect('master');
	$builder = $db->table('background_task');
	$builder->where(['ref_id' => $ref_id, 'type' => $type]);
	$builder->where('status > ', 1);
	return $builder->get()->getResultArray() ?? [];
}

function startTask($id) {
	$db = \Config\Database::connect('master');
	$tName = 'background_task';
	$builder = $db->table($tName);
	$data = $builder->where(['id' => $id])->get()->getRow();
	if (empty($data)) {
		return ['message' => 'Id Not found', 'result' => false];
	}
	// add background process
	$url = $data->comment_text . '/' . $id;
	$upData = runTask($url);
	$upData['status'] = 2; //executing
	//create file
	$res = $db->table($tName)->update($upData, ['id' => $data->id]);
	return ['message' => 'executing', 'result' => true];
}

function runTask($url) {
	$paths = new Config\Paths();
	$uid = time();
	$path = ROOTPATH . 'page_control/' . $uid . '.log';
	// $path =  "page_control/log/" . $uid . ".log";
	fileWrite($path, '');
	if (stripos($url, "api/v1") == false) {
		$url = BASEURL . 'api/v1/' . $url;
	}
	if (!isset($_SERVER['HTTPS'])) {
		$url = str_ireplace("https:", "http:", $url);
	}
	// if (ENVIRONMENT == 'development') {
	//     $url = str_ireplace("https:", "http:", $url);
	// }
	$cmd = "curl -k -o " . $path . " " . $url;
	$backLib = new BackgroundProcess($cmd);
	return ['cmd_text' => $backLib->getExcutedCmd(), 'pid' => $backLib->getProcessId(), 'uid' => $uid];
}

function completeTaks($id, $result, $status = 1) {
	$db = \Config\Database::connect('master');
	$tName = 'background_task';
	$upData['status'] = $status;
	$upData['result_data'] = json_encode($result);
	$upData['end_time'] = date("Y-m-d h:i:s");
	return $db->table($tName)->update($upData, ['id' => $id]);
}

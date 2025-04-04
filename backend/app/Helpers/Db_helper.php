<?php
function startDbTrans($profile = '', $unlimited = false) {
	$db = \Config\Database::connect('default');
	if ($unlimited) {
		$db->query("SET SESSION innodb_lock_wait_timeout = 100000000");
	}
	if (ENVIRONMENT != 'production') {
		$db->transException(true)->transStart();
	} else {
		$db->transBegin();
	}

}
function applyDbChanges($profile = '') {
	$db = \Config\Database::connect('default');
	if (ENVIRONMENT != 'production') {
		$status = true;
		try {
			$db->transComplete();
		} catch (DatabaseException $e) {
			//$e;
			$status = false;
		}

	} else {
		$status = $db->transStatus();
		if ($status === false) {
			$db->transRollback();
		} else {
			$db->transCommit();
		}
	}
	return $status;
}

function updateApprove($id, $user_id) {
	$db = \Config\Database::connect('master');
	return $db->table('modify_request')->update(['status' => 1, 'action_by' => $user_id, 'action_date' => date('Y-m-d')], ['id' => $id]);
}
function checkRequestActive($id) {
	if (empty($id)) {
		return $id;
	}
	$db = \Config\Database::connect('master');
	$res = $db->table('modify_request')->select('status,created_by')->where(['id' => $id])->get()->getRow();
	return !empty($res) && (int) $res->status == 2 ? $res->created_by : 'Request Already Actioned by Some One';
}
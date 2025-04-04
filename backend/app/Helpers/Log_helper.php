<?php
function addLog($status, $msg, $user_id, $ref_id, $module_id = null, $action_id = null)
{
    $data = array(
        'user_id'   => $user_id,
        'message'   => $msg,
        'module_id' => $module_id ?? '',
        'action_id' => $action_id ?? '',
        'ref_id'    => $ref_id,
        'status'    => $status,
    );
    $db      = \Config\Database::connect('master');
    $builder = $db->table('app_logs');
    return $builder->insert($data);
}

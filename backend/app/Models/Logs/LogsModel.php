<?php
namespace Core\Models\Logs;

use CodeIgniter\Model;

class LogsModel extends Model
{
    protected $db;
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function addLog($status, $msg, $user_id, $ref_id, $module_id = null, $action_id = null)
    {
        $data = array(
            'user_id'   => $user_id,
            'message'   => $msg,
            'module_id' => $module_id ?? '',
            'action_id' => $action_id ?? '',
            'ref_id'    => $ref_id,
            'status'    => $status,
        );
        $builder = $this->db->table('app_logs');
        return $builder->insert($data);
    }
    /**
     * get_unread_notifications_count function.
     *
     * @access public
     * @param mixed $user_id
     * @return notifications count
     */
    public function get_unread_notifications_count($user_id)
    {

        $this->db->from('notifications');
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 0);
        return $this->db->count_all_results();
    }

    public function get_notificationUser($id)
    {
        $builder = $this->db->table('app_event_notify');
        // $this->db->from('app_event_notify');
        //$this->db->join('app_event_notify','');
        $builder->where('event_id', $id);
        return $builder->get()->getResultArray();
    }

    public function saveNotification($event_id, $cond = array('zone' => 1), $def = array('refName' => ''))
    {
        $notifyUserRole  = $this->get_notificationUser($event_id);
        $builder         = $this->db->table('user_role')->select('user_id');
        $condArrayRoleId = array();
        if (!empty($notifyUserRole)) {
            foreach ($notifyUserRole as $value) {
                array_push($condArrayRoleId, $value['role_id']);
            }
            foreach ($cond as $condK => $condV) {
                $builder->whereIn($condK, array($condV, 0));
            }
            $builder->whereIn('role_id', $condArrayRoleId);
            $users   = $builder->get()->getResultArray();
            $users   = array_column($users, 'user_id');
            $users   = array_unique($users);
            $builder = $this->db->table('app_notify_message');
            $builder->join('module_action', 'module_action.id = event_id', 'inner');
            $builder->where('event_id', $event_id);
            $mesg = $builder->get()->getResultArray();
        }
        //print_r($mesg);
        // foreach ($usrs as $uK => $uV) {
        //     $uV['message'] =
        // }
        print_r($users);
        // $this->db->from('app_event_notify');
        // //$this->db->join('app_event_notify','');
        // $this->db->where('event_id', $id);
        // $this->db->where('status', 0);
        // return $this->db->count_all_results();
    }

    public function new_notifications($user_id, $msg, $refName, $refId)
    {
        $data = array(
            'user_id' => $user_id,
            'message' => $msg,
            'module'  => $module,
            'action'  => $action,
            'ref_id'  => $refId,
            'status'  => 0,
        );

        $this->db->from('notifications');
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 0);
        return $this->db->count_all_results();
    }

    /**
     * mark_notification_as_read function.
     *
     * @access public
     * @param int $notification_id
     * @param int $user_id
     * @return bool true on success, false on failure
     */
    public function mark_notification_as_read($notification_id, $user_id)
    {

        $data = array(
            'status'    => 1,
            'viewed_on' => date('Y-m-j H:i:s'),
        );
        if ($notification_id === 'all') {
            $this->db->where('user_id', $user_id);
            $this->db->where('status', 0);
        } else {
            $this->db->where('id', $notification_id);
        }
        return $this->db->update('notifications', $data);
    }

}

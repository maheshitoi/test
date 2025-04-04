<?php
namespace Core\Infrastructure\Persistence\User;

use Core\Domain\Exception\RecordNotFoundException;
use Core\Domain\User\UserLogin;
use Core\Domain\User\UserLoginRepository;
use Core\Infrastructure\Persistence\DMLPersistence;
use Core\Models\UserLoginModel;

class SQLUserLoginRepository implements UserLoginRepository {
	use DMLPersistence;

	/** @var AppModel */
	protected $model;

	public function __construct(UserLoginModel $model) {
		$this->model = $model;
	}
	public function findAllUserPagination($ftbl, $isActive = true)
    {
        $this->model->select("user_login.*,group_concat(r.roleName separator ',') as roleName");
        $this->model->join('user_role as ur', 'ur.user_id = user_login.user_id', 'left')->join('role as r', 'r.id = ur.role_id', 'left');
        if (!$isActive) {
            $this->model->onlyDeleted();
        } else {
            $this->model->where($this->model->table . '.deleted_at', null);
        }
        $this->model->groupBy('user_login.user_name');
        return $this->paginationQuery($ftbl, ['user_id' => 'user_login.user_id','mobile_no' => 'user_login.mobile_no','email_id' => 'user_login.email_id']);
    }

	public function findPaginatedData(string $keyword = '') {
		if ($keyword) {
			$this->model
				->builder()
				->groupStart()
				->like('first_name', $keyword)
				->orLike('last_name', $keyword)
				->orLike('mobileno', $keyword)
				->orLike('user_name', $keyword)
				->groupEnd();
		}
		return $this->model->paginate(config('App')->paginationPerPage);
	}

	public function findUserOfId(int $id) {
		$User = $this->model->find($id);
		if (!$User instanceof UserLogin) {
			throw new RecordNotFoundException('User');
		}
		return $User;
	}

	function findUserUniqueByField($field, $compareCol, $username, $compareColValue = null) {
		$select = 'user_id,user_name,user_login.email_id,user_login.mobile_no,staff_fk_id,sponsor_fk_id';
		$this->model->select($select)->where('user_name', $username)->orWhere('user_login.' . $field, $username);
		if (($compareCol == 'staff_fk_id' || $compareCol == 'staff_emp_id') && !empty($compareColValue)) {
			// $isStaff_emp = (int) $compareColValue ? true : false;
			// print_r($compareColValue);
			// if ($isStaff_emp) {
			$this->model->select($select . ',s.staff_emp_id');
			$this->model->join('staff as s', 's.id = staff_fk_id', 'inner');
			// $compareCol = 'staff_emp_id';
			// }
		}
		// echo $this->model->builder()->getCompiledSelect();
		$user = $this->model->asArray()->withDeleted()->first();
		if (empty($user) || !count($user)) {
			return true;
		}

		if (!empty($compareColValue)) {
			return $user[$compareCol] == $compareColValue;
		}
		return false;
	}

	public function findUserUnique(string $field, $id) {
		$User = $this->model
			->select($field)
			->where($field, $id)->asArray()
			->withDeleted()->first();
		return $User;
	}

	public function loginCheck($username, $password) {
		$this->model->builder()->where(['user_name' => $username, 'password' => $password]);
		$result = $this->model->asArray()->first();
		if (!empty($result)) {
			$this->model->set(['last_login_date' => date('Y-m-d H:i:s')])->update($result['user_id']);
			unset($result['password']);
		}
		return $result;
	}

	public function addNewUser($data) {
		$username = $data->getUser_name();
		if (!empty($username)) {
			$this->model
				->builder()
				->where('user_name', $username);
			$res = $this->model->asArray()->first();
			if (isset($res['user_id'])) {
				$this->model->update($res['user_id'], $data);
				return $res['user_id'];
			} else {
				return $this->model->allowCallbacks(true)->insert($data);
			}
		}
	}
}

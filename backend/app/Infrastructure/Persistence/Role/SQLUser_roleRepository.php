<?php
namespace Core\Infrastructure\Persistence\Role;

use Core\Domain\Role\User_roleRepository;
use Core\Infrastructure\Persistence\DMLPersistence;
use Core\Models\Role\User_roleModel;

class SQLUser_roleRepository implements User_roleRepository {
	use DMLPersistence;

	/** @var AppModel */
	protected $model;

	public function __construct() {
		$this->model = new User_roleModel();
	}

	public function findByRoleId(int $id) {
		return $this->model->builder()
			->select('r.roleName,user_role.*')
			->join('role as r', 'r.id = user_role.role_id')
			->where('user_role.user_id', $id)
			->get()->getResultArray();
	}

	public function findPermissionByUser(int $id) {
		return $this->model->builder()
			->select('user_role.*,r.roleName,r.desgination,an.actionName,m.moduleName,rP.*')
			->join('role as r', 'r.id = user_role.role_id', 'inner')
			->join('role_permission as rP', 'user_role.role_id = rP.role_id', 'inner')
			->join('module_action as ma', 'ma.id = rP.moduleActionId', 'inner')
			->join('module as m', 'm.id = ma.module_id', 'inner')
			->join('action_name as an', 'an.id = ma.action_id', 'inner')
			->where('user_role.user_id', $id)
			->get()->getResultArray();
	}

	function updateRole($cond) {
		$this->model->where($cond)->delete();
		return $this->model->insert($cond);
	}
	public function deleteOfId($id): bool {
		return $this->model->delete(['user_id' => $id]);
	}

}
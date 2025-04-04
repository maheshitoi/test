<?php
namespace Core\Infrastructure\Persistence\Role;

use Core\Domain\Role\RoleRepository;
use Core\Infrastructure\Persistence\DMLPersistence;
use Core\Models\Role\RoleModel;

class SQLRoleRepository implements RoleRepository
{
    use DMLPersistence;

    /** @var AppModel */
    protected $model;

    public function __construct()
    {
        $this->model = new RoleModel();
    }
    public function findPermissionById($roleId)
    {
        return $this->model->builder()
            ->join('role_permission', 'role.id = role_permission.role_id', 'inner')
            ->join('module_action', 'module_action.module_id = role_permission.moduleActionId')
            ->whereIn('role.id', $roleId)
            ->get()->getResultArray();
    }
}

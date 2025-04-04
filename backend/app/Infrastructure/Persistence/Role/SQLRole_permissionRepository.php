<?php
namespace Core\Infrastructure\Persistence\Role;

use Core\Domain\Role\Role_permissionRepository;
use Core\Infrastructure\Persistence\DMLPersistence;
use Core\Models\Role\Role_permissionModel;

class SQLRole_permissionRepository implements Role_permissionRepository
{
    use DMLPersistence;

    /** @var AppModel */
    protected $model;

    public function __construct()
    {
        $this->model = new Role_permissionModel();
    }

    public function getPermission($id)
    {
        return $this->model->builder()
            ->join('module_action as am', 'am.id = moduleActionId', 'inner')
            ->join('action_name as an', 'an.id = am.action_id', 'inner')
            ->join('module as m', 'm.id = am.module_id')
        //->join('', 'module.id = module_action.module_id', 'inner')
            ->whereIn('role_id', $id)
            ->get()->getResultArray();
    }
    public function deleteOfId($id)
    {
        return $this->model->delete(['role_id' => $id]);
    }
}

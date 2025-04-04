<?php
namespace Core\Controllers;

use Core\Controllers\BaseController;
use Core\Controllers\DMLController;
use Core\Infrastructure\Persistence\Role\SQLRoleRepository;
use Core\Infrastructure\Persistence\Role\SQLRole_permissionRepository;
use Core\Infrastructure\Persistence\Role\SQLUser_roleRepository;
use Core\Models\Logs\LogsModel;

class RoleController extends BaseController
{

    use DMLController;
    private $repository;
    private $role_repository;
    private $permission_repository;
    private $logModel;
    public function __construct()
    {
        $this->initializeFunction();
        $this->repository            = new SQLUser_roleRepository();
        $this->role_repository       = new SQLRoleRepository();
        $this->permission_repository = new SQLRole_permissionRepository();
        $this->logModel              = new LogsModel();
    }

    public function index()
    {

        //$this->UserListing();
    }

    public function PermissionByRole($id, $isFull = false)
    {
        $roleId = explode(',', $id);
        if ($isFull) {
            $data = $this->permission_repository->getPermission($roleId);
        } else {
            $data = $this->role_repository->findPermissionById($roleId);
        }
        return $this->message(200, $data);

    }

    public function updatePermission($id)
    {
        $req_data = $this->getDataFromUrl('json');
        if (empty($req_data)) {
            return $this->message(400, 'not processing');
        }
        $roleData = $req_data['role'] ?? [];
        $this->permission_repository->deleteOfId($id);
        $data = $this->permission_repository->insert($roleData);
        return $this->message(200, $data);

    }

    public function getPermissionByUser($id)
    {
        $this->logModel->saveNotification(1);
        return;
        $data = $this->repository->findPermissionByUser($id);
        //$roleData     = $this->role_repository->findByRoleId($id);
        //$data['role'] = $roleData;
        return $this->message(200, $data);
    }

}

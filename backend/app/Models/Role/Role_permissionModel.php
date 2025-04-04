<?php
namespace Core\Models\Role;

use CodeIgniter\Model;

class Role_permissionModel extends Model
{
    protected $table          = 'role_permission';
    protected $primaryKey     = 'role_id';
    protected $returnType     = 'Core\Domain\Role\Role_permission';
    protected $useSoftDeletes = false;
    protected $allowedFields  = ['moduleActionId', 'role_id'];
    protected $useTimestamps  = false;
}

<?php
namespace Core\Models\Role;
use CodeIgniter\Model;

    class RoleModel extends Model
    {
        protected $table      = 'role';
    protected $primaryKey = 'id';
    protected $returnType = 'Core\Domain\Role\Role';
    protected $useSoftDeletes = true;
         protected $allowedFields = [ 'roleName','status','desgination',
         ];
         protected $useTimestamps = true;
    }
?>
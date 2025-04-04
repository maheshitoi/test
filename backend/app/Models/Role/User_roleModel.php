<?php
namespace Core\Models\Role;

use CodeIgniter\Model;

class User_roleModel extends Model
{
    protected $table          = 'user_role';
    protected $primaryKey     = 'user_id';
    protected $returnType     = 'Core\Domain\Role\User_role';
    protected $useSoftDeletes = false;
    protected $allowedFields  = ['user_id', 'role_id', 'alliance_partner','organization','event_manager','vendor',
    ];
    protected $protectFields = true;
    protected $useTimestamps = false;
}

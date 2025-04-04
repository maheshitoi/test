<?php
namespace Core\Models;

use Core\Domain\UserRole\UserRole;
use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table          = 'user_role';
    protected $primaryKey     = 'user_id';
    protected $returnType     = UserRole::class;
    protected $allowedFields  = ['user_id', 'role_id', 'alliance_partner','organization','event_manager',];
    protected $protectFields  = true;
    protected $useTimestamps  = false;
    protected $useSoftDeletes = false;
}
<?php
namespace Core\Domain\Role;

use CodeIgniter\Entity;

class User_role extends Entity
{
    protected $attributes = ['user_id' => null, 'role_id' => null,  'alliance_partner'=>null,'organization'=>null,'event_manager'=>null,'vendor'=>null,
    ];
}

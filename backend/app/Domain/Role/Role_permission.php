<?php
namespace Core\Domain\Role;

use CodeIgniter\Entity;

class Role_permission extends Entity
{
    protected $attributes = ['moduleActionId' => null, 'role_id' => null,
    ];
}

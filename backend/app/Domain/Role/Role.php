<?php
namespace Core\Domain\Role;

use CodeIgniter\Entity;
    class Role extends Entity
    {
         protected $attributes = [ 'roleName'=> null,'status'=> null,'desgination'=> null,
         ];
    }
?>
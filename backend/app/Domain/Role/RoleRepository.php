<?php
namespace Core\Domain\Role;

use Core\Domain\DMLRepository;

interface RoleRepository extends DMLRepository
{
	public function findPermissionById( $id);
}?>
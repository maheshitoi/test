<?php
namespace Core\Domain\Role;

use Core\Domain\DMLRepository;

interface Role_permissionRepository extends DMLRepository
{
	public function getPermission($id);
}?>
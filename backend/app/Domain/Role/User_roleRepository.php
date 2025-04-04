<?php
namespace Core\Domain\Role;

use Core\Domain\DMLRepository;

interface User_roleRepository extends DMLRepository
{
	public function findByRoleId(int $id);
	public function findPermissionByUser(int $id);
}?>
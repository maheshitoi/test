<?php
namespace Core\Domain\User;

use Core\Domain\DMLRepository;

interface UserLoginRepository extends DMLRepository
{
    public function findPaginatedData(string $keyword = '');
    public function findUserUnique(string $field, $id);
    public function loginCheck($username, $password);
    public function addNewUser($data);
}

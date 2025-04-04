<?php
namespace Core\Domain\User;
use CodeIgniter\Entity;

class User extends Entity
{
    public $first_name;
    public $last_name;
    public $avatar;
    public $gender;
    public $date_of_birth;
    public $updated_at;
    public $created_at;
    public $deleted_at;
    public $user_id;
    public $id;
}

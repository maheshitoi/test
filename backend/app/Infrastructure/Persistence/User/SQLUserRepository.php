<?php
namespace Core\Infrastructure\Persistence\User;

use Core\Domain\Exception\RecordNotFoundException;
use Core\Domain\User\User;
use Core\Domain\User\UserRepository;
use Core\Infrastructure\Persistence\DMLPersistence;
use Core\Models\UserModel;

class SQLUserRepository implements UserRepository
{
    use DMLPersistence;

    /** @var AppModel */
    protected $model;

    public function __construct(UserModel $model)
    {
        $this->model = $model;
    }

    public function findPaginatedData(string $keyword = '')
    {
        if ($keyword) {
            $this->model
                ->builder()
                ->groupStart()
                ->like('first_name', $keyword)
                ->orLike('last_name', $keyword)
                ->orLike('mobileno', $keyword)
                ->orLike('user_name', $keyword)
                ->groupEnd();
        }
        return $this->model->paginate(config('App')->paginationPerPage);
    }


    public function findUserOfId(int $id)
    {
        $User = $this->model->find($id);
        //echo $User->password;
        //echo $User->first_name;
        //$executedQuery = $this->db->last_query();
        //print_r($executedQuery
        if (!$User instanceof User) {
            throw new RecordNotFoundException('User');
        }

        return $User;
    }

    public function findUserUnique(string $field, string $id)
    {
        $User = $this->model->builder()
            ->select($field)
            ->where($field, $id)
            ->get()->getResultArray();
        return $User;
    }

    public function findUserByEmail(string $email)
    {
        $User = $this->model->builder()
            ->where('email_id', $email)
            ->asArray()->find();
        return $User;
    }

    public function deleteOfId($id) : bool
    {
        $delete = $this->model->delete(['user_id' => $id]);
        return true;
    }
}

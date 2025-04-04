<?php
namespace Core\Domain;

interface DMLRepository
{
    public function save($data);
    public function findById($id);
    public function findAll();
    public function insert($data = null);
    public function findAllBystatus();
    public function deleteOfId($id);
    public function deleteWhere(array $cond);
    public function findAllByWhere(array $cond);
    public function update($cond, $data, $escData = false);
    public function updateById($cond, $data);
    public function countAll($withDeleted, array $cond);
    public function findAllPagination($ftbl, $isActive = true);
}

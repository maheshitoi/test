<?php 
namespace Core\Domain\Utility;

use Core\Domain\DMLRepository;

interface RateMasterRepository extends DMLRepository
{
	public function findAllBYlimit($limit);
	public function findTodayRate();
}
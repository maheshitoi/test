<?php
namespace Core\Domain\Utility;

use CodeIgniter\Entity;

class RateMaster extends Entity
{
    public $id;
    public $gold_rate;
    public $silver_rate;
    public $plat_rate;
    public $old_gold_rate;
    public $old_silver_rate;
    public $old_plat_rate;
    public $diamond_rate;
    public $old_diamond_rate;
}

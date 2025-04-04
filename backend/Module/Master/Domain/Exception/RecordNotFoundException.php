<?php 
namespace App\Domain\Exception;

use DomainException;

class RecordNotFoundException extends DomainException
{
	public $message=' Record Not Found';
	public $code;
	public function __construct($model=null,$code=404)
	{
		if($model)
			$this->message=$model.$this->message;

		$this->code=$code;

	}

} 

?>

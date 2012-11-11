<?php

namespace Fio;

/**
 * Account
 *
 * @copyright Copyright (c) 2012, Pavel Plzák
 * @author Pavel Plzák
 * @licence MIT
 * @version 0.1
 */
class Account 
{

	/** @var int */
	public $id;
			
	/** @var string */
	public $bankId;
	
	/** @var string */
	public $iban;
	
	/** @var string */
	public $bic;

	
	public function __construct(\stdClass $data) 
	{
		$this->id = $data->accountId;
		$this->bankId = !empty($data->bankId) ? $data->bankId : NULL;
		$this->iban = $data->iban;
		$this->bic = $data->bic;
	}
	
}

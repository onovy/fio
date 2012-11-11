<?php

namespace Fio;

/**
 * Transaction
 *
 * @copyright Copyright (c) 2012, Pavel Plzák
 * @author Pavel Plzák
 * @licence MIT
 * @version 0.1
 */
class Transaction 
{

	/** @var int */
	public $id;
	
	/** @var \DateTime */
	public $date;
	
	/** @var double */
	public $amount;
	
	/** @var string */
	public $currency;
	
	/** @var string */
	public $account;
	
	/** @var string */
	public $accountName;
	
	/** @var string */
	public $accountBankCode;
	
	/** @var string */
	public $accountBankName;
	
	/** @var string */
	public $constantSymbol;
	
	/** @var string */
	public $variableSymbol;
	
	/** @var string */
	public $specificSymbol;
	
	/** @var string */
	public $usersIdentification;
	
	/** @var string */
	public $messageForRecipient;
	
	/** @var string */
	public $paymentType;
	
	/** @var string */
	public $performedBy;

	/** @var string */
	public $comment;
	
	/** @var int */
	public $instructionId;




	public function __construct($transaction) 
	{
		$this->id = $transaction->column22->value;
		$this->date = new \DateTime($transaction->column0->value);
		$this->amount = $transaction->column1->value;
		$this->currency = $transaction->column14->value;
		$this->account = !empty($transaction->column2->value) ? $transaction->column2->value : NULL;
		$this->accountBankCode = !empty($transaction->column3->value) ? $transaction->column3->value : NULL;
		$this->accountName = !empty($transaction->column10->value) ? $transaction->column10->value : NULL;
		$this->accountBankName = !empty($transaction->column12->value) ? $transaction->column12->value : NULL;
		$this->constantSymbol = !empty($transaction->column4->value) ? $transaction->column4->value : NULL;
		$this->variableSymbol = !empty($transaction->column5->value) ? $transaction->column5->value : NULL;
		$this->specificSymbol = !empty($transaction->column6->value) ? $transaction->column6->value : NULL;
		$this->usersIdentification = !empty($transaction->column7->value) ? $transaction->column7->value : NULL;
		$this->messageForRecipient = !empty($transaction->column16->value) ? $transaction->column16->value : NULL;
		$this->paymentType = !empty($transaction->column8->value) ? $transaction->column8->value : NULL;
		$this->performedBy = !empty($transaction->column9->value) ? $transaction->column9->value : NULL;
		$this->comment = !empty($transaction->column25->value) ? $transaction->column25->value : NULL;
		$this->instructionId = !empty($transaction->column17->value) ? $transaction->column17->value : NULL; 
	}
	
}

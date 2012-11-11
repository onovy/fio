<?php

namespace Fio;

/**
 * TransactionList
 *
 * @copyright Copyright (c) 2012, Pavel Plzák
 * @author Pavel Plzák
 * @licence MIT
 * @version 0.1
 */
class TransactionList 
{
	
	/** @var Account */
	public $account;
	
	/** @var \DateTime */
	public $dateFrom;
	
	/** @var \DateTime */
	public $dateTo;
	
	/** @var double */
	public $openingBalance;
	
	/** @var double */
	public $closingBalance;
	
	/** @var int */
	public $firstTransactionId;
	
	/** @var int */
	public $lastTransactionId;
	
	/** @var int */
	public $lastDownloadedTransactionId;

	/** @var array */
	public $transactions;

	
	
	
	private function __construct() 
	{}
	
	
	
	
	/**
	 * Adds transaction to collection
	 * @param Transaction $transaction
	 * @return void
	 */
	public function addTransaction(\Fio\Transaction $transaction)
	{
		$this->transactions[] = $transaction;
	}
	
	
	/** 
	 * TransactionList factory method
	 * @param \stdClass $data
	 * @param \DateTime $from
	 * @param \DateTime $to
	 * @return TransactionList
	 */
	public static function create(\stdClass $data, \DateTime $from = NULL, \DateTime $to = NULL)
	{
		$list = new self;
		
		$list->account = new Account($data->info);
		
		$list->dateFrom = $from;
		$list->dateTo = $to;
		$list->openingBalance = $data->info->openingBalance;
		$list->closingBalance = $data->info->closingBalance;
		$list->firstTransactionId = $data->info->idFrom;
		$list->lastTransactionId = $data->info->idTo;
		$list->lastDownloadedTransactionId = $data->info->idLastDownload;
		

		if(!empty($data->transactionList->transaction)) {
			foreach($data->transactionList->transaction as $transaction) {
				$list->addTransaction(new Transaction($transaction));
			}
		}
		
		return $list;
	}
	
	
}

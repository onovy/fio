<?php

namespace Fio;

/**
 * A wrapper for Fio bank's API to simplify access 
 * to user's transactions and account information
 *
 * @copyright Copyright (c) 2012, Pavel Plzák
 * @author Pavel Plzák
 * @licence MIT
 * @version 0.1
 */
class Fio
{
	
	/** @var string */
	private $token;
	
	/** @var string */
	private $baseUrl = 'https://www.fio.cz/ib_api/rest';
	
	
	
	public function getBaseUrl() 
	{
		return $this->baseUrl;
	}

	public function setBaseUrl($baseUrl) 
	{
		$this->baseUrl = $baseUrl;
	}

		
	
	
	public function __construct($token) 
	{
		$this->token = $token;
	}
	
	
	
	/**
	 * Returns transactions in given period 
	 * @param DateTime $from
	 * @param DateTime $to
	 * @return TransactionList
	 */
	public function getTransactionsByPeriod(\DateTime $from, \DateTime $to)
	{
		$url = $this->buildRequestUrl('get', 'periods', array($from->format('Y-m-d'), $to->format('Y-m-d')));
		$result = $this->callRequest($url, true);

		return TransactionList::create($result->accountStatement, $from, $to);
	}
	
	
	
	/**
	 * Returns official transaction summary
	 * @param int $year summary of which year
	 * @param int $summaryNumber summary number in given year
	 * @return TransactionList
	 */
	public function getTransactionsBySummary($year, $summaryNumber)
	{
		$url = $this->buildRequestUrl('get', 'by-id', array($year, $summaryNumber));
		$result = $this->callRequest($url, true);

		return TransactionList::create($result->accountStatement);
	}
	
	
	
	/** 
	 * Returns last transactions since set up marker
	 * @return TransactionList
	 */
	public function getTransactionsByMarker()
	{
		$url = $this->buildRequestUrl('get', 'last', null);
		$result = $this->callRequest($url, true);

		return TransactionList::create($result->accountStatement);
	}

	
	
	/** 
	 * Sets marker on last successfully downloaded id of transaction
	 * @param int $transactionId
	 * @return void
	 */
	public function setIdMarker($transactionId)
	{
		$url = $this->buildRequestUrl('set', 'set-last-id', array($transactionId));
		$this->callRequest($url, false);
	}
	
	
	
	/** 
	 * Sets date of last unsuccessful attempt to download transactions
	 * @param \DateTime $date
	 * @return void
	 */
	public function setUnsuccessfullDownloadAttempt(\DateTime $date)
	{
		$url = $this->buildRequestUrl('set', 'set-last-date', array($date->format('Y-m-d')));
		$this->callRequest($url, false);
	}

	


	
	
	
	/**
	 * Builds URL for download
	 * @param string $type type of request get|set
	 * @param string $action
	 * @param array $params request params
	 */
	private function buildRequestUrl($type, $action, array $params = NULL)
	{
		if(!in_array($type, array('get', 'set'))) {
			throw new \InvalidArgumentException('Argument $type must be "get" or "set".');
		}
		
		return $this->baseUrl.'/'.$action.'/'.$this->token.(!is_null($params) ? '/'.implode('/', $params) : '').'/'.($type === 'get' ? 'transactions.json' : '');
	}

	
	/** 
	 * Calls request url and returns result
	 * @param string $url
	 * @return mixed
	 */ 
	private function callRequest($url, $needResult)
	{
		do {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($ch);
			curl_close($ch);

			$json = json_decode($result);

			if ($result === FALSE || ($needResult && $json === null)) {
				// Try again, but wait a moment
				sleep(15);
			}
		} while ($result === FALSE || ($needResult && $json === null));

		return $json;
	}
}
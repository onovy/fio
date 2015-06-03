#!/usr/bin/php
<?php

include 'fio/Fio.php';
include 'fio/Account.php';
include 'fio/Transaction.php';
include 'fio/TransactionList.php';

include 'config.php';

function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT) {
    $diff = strlen($input) - mb_strlen($input, 'UTF-8');
    return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
}

// create instance of fio api wrapper
$fio = new \Fio\Fio($token);

// load state
if (file_exists('state.txt')) {
	$state = unserialize(file_get_contents('state.txt'));
} else {
	$state = new stdClass();
	$state->lastDate = new DateTime('1900-01-01');
	$state->lastId = 0;
	$state->balance = 0;
}

if (isset($argv[1])) {
	$from = new DateTime($argv[1]);
	$to = clone $from;
	$to->modify('+1 month');
	$to->modify('-1 day');

	echo 'From: ' . $from->format('Y-m-d') . PHP_EOL;
	echo 'To: ' . $to->format('Y-m-d') . PHP_EOL;

	$result = $fio->getTransactionsByPeriod($from, $to);
} else {
	echo 'LastID: ' . $state->lastId . PHP_EOL;
//	$fio->setIdMarker($state->lastId);
//	sleep(15);
	$result = $fio->getTransactionsByMarker();
}

// Opening balance
if ($state->balance != $result->openingBalance) {
	echo 'Pocatecni zustatek nesedi! ' . PHP_EOL;
	echo 'Ulozeny: ' . $state->balance . PHP_EOL;
	echo 'V transakci: ' . $result->openingBalance . PHP_EOL;
	exit;
}

if (!isset($result->transactions)) {
	echo 'Nothing new' . PHP_EOL;
} else {
	foreach ($result->transactions as $transaction) {
		// Date check
		$diff = $state->lastDate->diff($transaction->date);
		if ($diff->invert) {
			echo 'Datum couva!' . PHP_EOL;
			echo 'Ulozene: ' . $state->lastDate->format('Y-m-d') . PHP_EOL;
			echo 'V transakci: ' . $transaction->date->format('Y-m-d') . PHP_EOL;
			exit;
		}
		$state->lastDate = $transaction->date;

		// ID check
		if ($state->lastId >= $transaction->id) {
			echo 'ID couva!' . PHP_EOL;
			echo 'Ulozene: ' . $state->lastId . PHP_EOL;
			echo 'V transakci: ' . $transaction->id . PHP_EOL;
			exit;
		}
		$state->lastId = $transaction->id;

		// Balance
		$state->balance += $transaction->amount;
		$state->balance = round($state->balance, 2);

		// Save state
		file_put_contents('state.txt', serialize($state));

		// Save transactions
		$file = $transaction->date->format('Y') . '.csv';
		if (!file_exists($file)) {
			$f = fopen($file, 'w');
			fputcsv($f, array(
				'id',
				'date',
				'amount',
				'currency',
				'account',
				'accountBankCode',
				'accountName',
				'accountBankName',
				'constantSymbol',
				'variableSymbol',
				'specificSymbol',
				'usersIdentification',
				'messageForRecipient',
				'paymentType',
				'performedBy',
				'comment',
				'instructionId',
			));
		} else {
			$f = fopen($file, 'a');
		}
		fputcsv($f, array(
				$transaction->id,
				$transaction->date->format('Y-m-d'),
				$transaction->amount,
				$transaction->currency,
				$transaction->account,
				$transaction->accountBankCode,
				$transaction->accountName,
				$transaction->accountBankName,
				$transaction->constantSymbol,
				$transaction->variableSymbol,
				$transaction->specificSymbol,
				$transaction->usersIdentification,
				$transaction->messageForRecipient,
				$transaction->paymentType,
				$transaction->performedBy,
				$transaction->comment,
				$transaction->instructionId,
		));
		echo
			$transaction->date->format('Y-m-d') .
			' : ' .
			mb_str_pad(number_format($transaction->amount, 2, ',', ' '), 10, ' ', STR_PAD_LEFT) .
			' : ' .
			mb_str_pad($transaction->paymentType, 30, ' ') .
			' : ' .
			$transaction->comment .
			PHP_EOL;
		fclose($f);
	}
}

// Closing balance
if ($state->balance != $result->closingBalance) {
	echo 'Konecny zustatek nesedi! ' . PHP_EOL;
	echo 'Ulozeny: ' . $state->balance . PHP_EOL;
	echo 'V transakci: ' . $result->closingBalance . PHP_EOL;
	exit;
}

echo 'Balance: ' . $state->balance . PHP_EOL;

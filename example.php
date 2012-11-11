<?php

// token generated via internet banking application
$token = 'GD4LS8NED06zWkAZEZ12kv0E83KxC5c2X3CJQEY8j5bJ4kU96H';

// create instance of fio api wrapper
$fio = new \Fio\Fio($token);

// get transactions in given perid
$from = new \DateTime('2012-11-01');
$to = new \DateTime;
$result = $fio->getTransactionsByPeriod($from, $to);

// set marker and then get new transactions since this marker
$fio->setIdMarker(12621646494);
// !!! there must be at least 1 second delay after setting a marker, otherwise API returns empty accountStatement in "get" methods
$result = $fio->getTransactionsByMarker();

// get transactions from official 2nd summary of 2012
$year = 2012;
$summaryNumber = 2;
$result = $fio->getTransactionsBySummary($year, $summaryNumber);



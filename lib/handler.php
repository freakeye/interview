<?php

// подключение файлов настройки и библиотек
// ;
require_once $_SERVER['DOCUMENT_ROOT'] . '/test_job/lib/AppSettings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/test_job/lib/PaymentBaseClass.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/test_job/lib/PaymentForCash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/test_job/lib/PaymentCashless.php';

header('Content-type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

//
// начало сценария
//
if (isset($_REQUEST['submit']))
{
	bcscale(CPaymentBaseClass::ACCURACY);				// точность
	switch ($_REQUEST['submit']) {
		case 'maxLastWeek':
			$answer = CPaymentBaseClass::maxLastWeek();
			break;
		case 'avgByPayment':
			$meth = SqlXss_sec_string($_REQUEST['payMethod']);
			if ($meth === 'cashless') {
				$answer = CPaymentCashless::avgByPayment();
			}
			else {
				$answer = CPaymentForCash::avgByPayment();
			}
			break;
		default:										// 'Оплатить'
			if (($_REQUEST['payValue'] > 0) && isset($_REQUEST['payID'])) {
				$answer = paymentHandler($_REQUEST);
			}
			else
				$answer = 'Некорректные данные';
			break;
	}
	echo '<pre>', print_r($answer, true), '</pre>';
};

//
function paymentHandler($request)
{
// обезвреживание входных данных
	$curPayArray = array_map('SqlXss_sec_string', $request);
	$currentPay = ($curPayArray['payMethod'] === 'cashless')
					? new CPaymentCashless($curPayArray)
					: new CPaymentForCash($curPayArray);
	$payArr = $currentPay->insertDB($curPayArray['payMethod']);
	return $payArr;
}

?>

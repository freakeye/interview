<?php
//
// класс описывает способ оплаты наличными 'forcash'
//
class CPaymentForCash extends CPaymentBaseClass
{
	const K_FOR_CASH = 0.05;		// 5%

	private
		$payMethod,					// способ оплаты
		$bonusCoefficient,			// coefficient
		$payBonuses;				// сумма бонусов по платежу

	function __construct($payArray)
	{
		parent::__construct($payArray);
		$this->payMethod = $payArray['payMethod'];
		$this->bonusCoefficient = self::K_FOR_CASH;
	}

// сумма бонуса для данного платежа
// реализация метода для этого класса
	public function getBonusesValue()
	{
		$this->payBonuses = bcmul($this->bonusCoefficient, $this->payValue);
	}

	//
	// вызывает родительскую функцию
	//
	public static function avgByPayment()
	{
		$meth = 'forcash';
		$paySumma = CPaymentBaseClass::averagePaysValues($meth);
		if ($paySumma) {
			$avgBonuses = 'Средняя величина бонусов по наличным платежам: '
							. bcmul($paySumma, self::K_FOR_CASH) . '<br />';
		}
		else {						// NULL для пустой таблицы
			$avgBonuses = "<br />В базе нет записей по наличным платежам.\r\n";
		}
		return $avgBonuses;
	}
}

//
// класс описывает безналичный способ оплаты 'cashless'
//
class CPaymentCashless extends CPaymentBaseClass
{
	const K_CASHLESS = 0.10;

	private
		$payMethod,					// способ оплаты
		$bonusCoefficient,			// coefficient
		$payBonuses;				// сумма бонусов по платежу

	function __construct($payArray)
	{
		parent::__construct($payArray);
		$this->payMethod = $payArray['payMethod'];
		$this->bonusCoefficient = self::K_CASHLESS;
	}

// реализация метода для этого класса
	public function getBonusesValue()
	{
		$this->payBonuses = bcmul($this->bonusCoefficient, $this->payValue);
	}

//
	public static function avgByPayment()
	{
		$meth = 'cashless';
		$paySumma = CPaymentBaseClass::averagePaysValues($meth);
		if ($paySumma) {
			$avgBonuses = 'Средняя величина бонусов по безналичным платежам: '
							. bcmul($paySumma, self::K_CASHLESS) . '<br />';
		}
		else {						// NULL для пустой таблицы
			$avgBonuses = '<br />В базе нет записей по безналичным платежам.\r\n';
		}
		return $avgBonuses;
	}
}

?>
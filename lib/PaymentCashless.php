<?php
//
// класс описывает безналичный способ оплаты 'cashless'
//
class CPaymentCashless extends CPaymentBaseClass
{
	const K_CASHLESS = '0.10';		// без кавычек теряется точность: K*10= 3.4

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
			$avgBonuses = '<br />Нет записей по безналичным платежам.<br />';
		}
		return $avgBonuses;
	}
}

?>
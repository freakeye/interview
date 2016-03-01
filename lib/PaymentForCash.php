<?php
//
// класс описывает способ оплаты наличными 'forcash'
//
class CPaymentForCash extends CPaymentBaseClass
{
	const K_FOR_CASH = '0.05';		// 5%

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
			$avgBonuses = '<br />Нет записей по наличным платежам.<br />';
		}
		return $avgBonuses;
	}
}

?>
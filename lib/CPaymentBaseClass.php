<?php

// коэффициенты для начисления бонусов
const	K_FOR_CASH = 0.05,
		K_CASHLESS = 0.10;
// КУДА ИХ ЛУЧШЕ?


require_once $_SERVER['DOCUMENT_ROOT'] . '/test_job/lib/CDataBaseMySQL.php';

// Класс описывает абстрактный платёж
// вся работы с базой данных идёт через него
//
abstract class CPaymentBaseClass
{
	const ACCURACY = 2;				// Точность
// общие для потомков
	protected
		$payValue,					// сумма платежа
		$payTime,					// время и дата платежа
		$payID;						// id платежа или его инициатора
		// $payBonuses;				// сумма бонусов по платежу
// Общая часть конструктора
	protected function __construct($payArray)
	{
		$this->payID = $payArray['payID'];
		$this->payValue = $payArray['payValue'];
		$this->payTime = time();						// unix epoch
// Вычисление бонуса по платежу
		$this->getBonusesValue();
	}

//
// по условию ТЗ: здесь — описание, реализация — в потомках
	abstract protected function getBonusesValue();

// пройти по всем полям и записать в базу
	public function insertDB($meth)
	{
		$id = $this->payID;
		$value = $this->payValue;
		// формат MySQL DATETIME
		$datiStr = date('Y-m-d H:i:s', $this->payTime);
		CDataBaseMySQL::openDataBase();
		$insertion = CDataBaseMySQL::prepareSql($id, $value, $meth, $datiStr);
		CDataBaseMySQL::closeDataBase();
		$res = ($insertion === true)
					? compact('id', 'value', 'meth', 'datiStr')
					: 'Платёж не прошёл';
		return $res;
	}

//
// возвращает строку среднюю величину платежей данного типа
//
	protected static function averagePaysValues($meth)
	{
		CDataBaseMySQL::openDataBase();
		$sql = "SELECT AVG(pay_value) AS 'avg'
					FROM paytable WHERE pay_method='$meth'";
		$avgThis = mysql_fetch_object(mysql_query($sql))->avg;
		CDataBaseMySQL::closeDataBase();
		return $avgThis;
	}

//
// Возвращает платеж с максимальным бонусом за последнюю неделю.
//
	public static function maxLastWeek()
	{
		CDataBaseMySQL::openDataBase();
		$sql = "SELECT MAX(pay_value) AS 'max'
					FROM paytable WHERE pay_method = 'forcash'
							AND pay_time > NOW() - INTERVAL 7 DAY";
		$maxCash = mysql_fetch_object(mysql_query($sql))->max;
		$sql = "SELECT MAX(pay_value) AS 'max'
					FROM paytable WHERE pay_method = 'cashless'
							AND pay_time > NOW() - INTERVAL 7 DAY";
		$maxLess = mysql_fetch_object(mysql_query($sql))->max;
	// бонусы по типам платежей
		$bonusCash = bcmul($maxCash, CPaymentForCash::K_FOR_CASH);
		$bonusLess = bcmul($maxLess, CPaymentCashless::K_CASHLESS);
	// совпадение бонусов для двух типов платежей
		if ($bonusCash === $bonusLess) {
			$maxSumma = $bonusLess;
	// объединение платежей
			$sql = "SELECT * AS arr FROM paytable
						WHERE pay_value = $maxCash AND pay_method = 'forcash'
						OR pay_value = $maxLess AND pay_method = 'cashless'";
		}
		else {
			$maxPay = ($bonusCash > $bonusLess) ? $maxCash : $maxLess;
			$maxSumma = ($bonusCash > $bonusLess) ? $bonusCash : $bonusLess;
			$sql = "SELECT * FROM paytable WHERE pay_value = $maxPay";
		}
		$sql_result = mysql_query($sql);
		CDataBaseMySQL::closeDataBase();
		return self::printPayWithMaxBonuses($sql_result, $maxSumma);
	}

// форматирование вывода
	private static function printPayWithMaxBonuses($sql_result, $maxSumma)
	{
		$countRows = mysql_num_rows($sql_result);
		switch ($countRows) {			// result header
			case 0:
				$result = 'В течение прошлой недели платежи не осуществлялись.
							<br />Записи отсутствуют.<br />';
				break;
			case 1:
				$result = 'Платёж с максимальной суммой бонуса:<br /><br />';
				break;
			default:				// платежей несколько
				$result = "Найдено платёжей с максимальной суммой бонуса:
							<b>$countRows</b><br /><br />";		
				break;
		}
		for ($i = 0; $i < $countRows; $i++) {
			$row = mysql_fetch_row($sql_result);
			$result = $result . "ID платежа: $row[0]<br />" .
						"Сумма платежа: $row[1] <br />" .
						"Вид платежа: $row[2]<br />" .
						"Время платежа: $row[3]<br /><br />";
		}
		return $result . 'Сумма бонуса: ' . $maxSumma . '<br />';
	}
}

?>

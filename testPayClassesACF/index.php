<!DOCTYPE html>
<html lang="ru">
<head>
  <meta http-equiv="content-type" type="text/html" charset="UTF-8" />
  <link rel="stylesheet" type="text/css" href="styles/style.css" />
  <title>Payments</title>
</head>
<body>
<?php
	// require_once $_SERVER['DOCUMENT_ROOT'] . '/test_job/lib/PaymentBaseClass.php';
	// require_once $_SERVER['DOCUMENT_ROOT'] . '/test_job/lib/PaymentForCash.php';
	// require_once $_SERVER['DOCUMENT_ROOT'] . '/test_job/lib/PaymentCashless.php';
?>
	<div class='container'>
		<header>
			<noscript>
	You're browser not support JavaScript, or you have configured not to enable
	javascripts.<br />
	Ваш браузер не поддерживает javascript, или вы отключили его в настройках.
  			</noscript>
  		</header>
    <h3><span>Платёжная форма</span></h3>
	<form id="formPay" method='POST'>
		<div id="payID" class='form_item'>
			<span>payID</span>
			<input type="text" name="payID" size='6' value=
				<?php echo rand(); ?>
			/>
		</div>
		<div class="form_item">
			<span>Сумма платежа</span>
			<input type="number" name="payValue" required/>
		</div>
		<div class="pay_header"><span>Способ оплаты</span></div>
		<div class="pay_method">
			<div class="pay_cashless">
				<label>
					<span>Безналичный платёж</span>
					<input type="radio" name="payMethod" value="cashless"
						<?php if (empty($_REQUEST) ||
									($_REQUEST['payMethod'] === 'cashless'))
								echo 'checked=\'checked\''; ?>
					/></label>
				<div class="label_bonus">Бонус
            		<?php echo 10//CPaymentForCash::K_FOR_CASH; ?>
            		% от суммы платежа
            	</div>
			</div>
			<div class="pay_forcash">
				<label><span>Оплата наличными</span>
					<input type="radio" name="payMethod" value="forcash"
						<?php if (!empty($_REQUEST) &&
									($_REQUEST['payMethod'] === 'forcash'))
								echo 'checked=\'checked\''; ?>
					/></label>
				<div class="label_bonus">Бонус
            		<?php echo 5//CPaymentCashless::K_CASHLESS; ?>
            		% от суммы платежа
            	</div>
			</div>
		</div>
		<div class="btn_holder">
			<button type="submit" name="submit" value="SendPay">Оплатить
			</button>
		</div>
	</form>
	<h3><span>Информация по бонусам</span></h3>
	<form id="formInf" method="GET">
		<div class="form_item">
			<span>Максимум за последнюю неделю</span>
			<button type="submit" name="submit" value="maxLastWeek">
				максимальный бонус</button>
		</div>
		<div class="pay_header">Cреднее по виду платежа</div>
		<div class='pay_method'>
			<div class='pay_cashless'>
				<label><span>безналичный</span>
					<input type="radio" name="payMethod" value="cashless"
						<?php if (empty($_REQUEST) ||
									($_REQUEST['payMethod'] === 'cashless'))
								echo 'checked=\'checked\''; ?>
					/></label>
			</div>
			<div class='pay_forcash'>
				<label><span>наличными</span>
					<input type="radio" name="payMethod" value="forcash"
						<?php if (!empty($_REQUEST) &&
									($_REQUEST['payMethod'] === 'forcash'))
								echo 'checked=\'checked\''; ?>
					/></label>
			</div>
		</div>
		<div class="btnHolder">
			<button type="submit" name="submit" value="avgByPayment">
				среднее по платежам</button>
		</div>
	</form>
		<div id="answBox"></div>
		<footer>
			<span class='year_label'>2016</span>
		</footer>
	</div>
</body>
  <script src="scripts/jquery-2.1.4.min.js"></script>
  <script src="scripts/transport.js"></script>
</html>

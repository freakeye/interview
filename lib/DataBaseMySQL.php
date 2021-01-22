<?php

//
// класс для работы с базой данных MySQL
//
class CDataBaseMySQL
{
	private static
		$db_hostname = DB_HOSTNAME,
		$db_database = DB_DATABASE,
		$db_username = DB_USERNAME,
		$db_password = DB_PASSWORD,
		$db_server = NULL;

	private static
		$db_exist;					// таблица в БД создана

	public static function openDataBase()
	{
		self::$db_server = mysql_connect(self::$db_hostname,
							self::$db_username, self::$db_password);
		if (! self::$db_server) mysql_action_error('connect');
		mysql_select_db(self::$db_database)
			or mysql_action_error('select');
	}

	public static function closeDataBase()
	{
		mysql_close(self::$db_server);
	}

// подготовка SQL-инструкции
	public static function prepareSql($id, $value, $method, $dati)
	{
		if ( ! self::$db_exist)
		{
		// Создание таблицы CREATE TABLE IF NOT EXIST
			$query = "CREATE TABLE IF NOT EXISTS paytable (
						pay_id INT(11) UNSIGNED NOT NULL PRIMARY KEY,
						pay_value INT(10) UNSIGNED,
						pay_method CHAR(8) NOT NULL DEFAULT 'cashless',
						pay_time DATETIME NOT NULL
					  )
					  ENGINE InnoDB;";
			$tmpRes = mysql_query($query);
			if ($tmpRes) self::$db_exist = true;
		}

		$query = 'PREPARE statement FROM "INSERT INTO 
					paytable VALUES(?, ?, ?, ?)"';
		mysql_query($query);
		$query = "SET 	 @pay_id = \"$id\"," .
						"@pay_value = \"$value\", " .
						"@pay_method = \"$method\", ".
						"@pay_time = \"$dati\"";
		mysql_query($query);
		$query = 'EXECUTE statement USING @pay_id, @pay_value,
					@pay_method, @pay_time';
// результат: выполнен ли запрос
		$insertionResult = (mysql_query($query)) ? true 
							: mysql_action_error('query', 'noecho');
		$query = 'DEALLOCATE PREPARE statement';
		mysql_query($query);
		return $insertionResult;	// успех проведённого sql запроса
	}
}

// сообщения об ошибках MySQL
function mysql_action_error($action, $outMethod)
{
	switch ($action) {
		case 'connect':
			$headerErrMsg = 'Ошибка соединения с БД MySQL';
			break;
		case 'select':
			$headerErrMsg = 'Ошибка выбора БД';
			break;
		case 'query' || 'execute':
			$headerErrMsg = 'Сбой при выполнении запроса к БД';
			break;
		case 'update':
			$headerErrMsg = 'Сбой при выполнении запроса на обновление БД';
			break;
		default:
			break;
	}
	$errorMessage = mysql_error();
	$errCode = mysql_errno();
	if ($outMethod === 'echo') {
		echo <<<_END
$headerErrMsg: <p>$errorMessage<br />$errCode</p>
Обновите страницу и повторите попытку.
Если ошибка останется, напишите <a href="mailto:admin@server.test">
администратору</a>."
_END;
	}
	else {
		return $headerErrMsg . "<br /><i>$errorMessage<br />$errCode</i>";
	}
}

//
// предотвращение SQL и XSS внедрений
//
function SqlXss_sec_string($string)
{
	if (get_magic_quotes_gpc())
		$string = stripslashes($string);
	$str = mysql_real_escape_string($string);
	$str = htmlentities(strip_tags($str));
	return $str;
}

?>
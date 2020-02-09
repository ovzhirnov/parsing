<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<meta charset="UTF-8">
<title>Parsing from parishop.ru</title>
</head>

<body>
<?php
// Второй коммит
// Автор - Жирнов Олег Вячеславович
// Парсер цен для url: https://parishop.ru/catalog/zhenskoye-nizhneye-belye/
// Запись производится в таблицу БД MySQL, параллельно выводится лог на экран

if (!$_POST['graburl'])
{ ?>
<form action="index.php" method="post" name="graburl">
<br>Введите адрес страницы для парсинга:<br>
<input type="text" size="60" name="graburl">
<input type="submit" value="поехали" name="go">
</form>
<?php }
else
{
$db_username = "user";
$db_password = "pass";
$db_name = "database";
$table_name = "table1";
$conn = new mysqli("localhost", $db_username, $db_password, $db_name);
if ($conn->connect_error) {die("Ошибка подключения: " . $conn->connect_error);}

$graburl = $_POST['graburl'];
ini_set('max_execution_time', 600);
function curl_get($host, $referer = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $html = curl_exec($ch);
    echo curl_error($ch);
    curl_close($ch);
    return $html;
}
$url_readfile1 = curl_get($graburl);
$url_readfile = iconv("utf-8","cp1251",$url_readfile1);
$rf = trim(chop($url_readfile));
$s2 = 0;
$number = 0;
$mask1 = "<span class=\"pr_card-price\">";
$mask2 = "&nbsp;&#8381;";
while ($s1 = strpos($rf,$mask1,$s2))
{
$s2 = strpos($rf,$mask2,$s1);
$s = $s2-$s1;
$price = trim(substr($rf,$s1,$s));
$price = str_replace($mask1,'',$price);
$sql = "INSERT INTO ".$table_name." (price) VALUES (".$price.")";
if ($conn->query($sql) === TRUE) 
	{
	echo date(DATE_RFC822).": В таблице ".$table_name." создана новая запись price - ".$price.".</br>";
	$number++;
	} 
else {echo "Ошибка: " . $sql  . $conn->error . "</br>";}
$s1 = strpos($rf,$mask1,$s2);
}
echo "Успешно создано записей - ".$number." в таблице ".$table_name;
$conn->close();
}
?>
</body>
</html>
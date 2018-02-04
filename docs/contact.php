<?php

	$email = 'info@neo-vo.ru';
	//$email = 'max@vipset.net';
	$subject = 'Заказать обратный звонок';
	$template = '
Страница: %s.
Имя: %s.
Email: %s.
Телефон: %s.
Вопрос: %s.';

	$encoding = 'utf-8';
	$preferences = [
		'input-charset' => $encoding,
		'output-charset' => $encoding,
		'line-length' => 76,
		'line-break-chars' => "\r\n"
	];
	$headers = "Content-type: text/plain; charset=" . $encoding . "\r\n";
	$headers.= "MIME-Version: 1.0 \r\n";
	$headers.= "Content-Transfer-Encoding: 8bit \r\n";
	$headers.= "Date: ".date("r (T)")." \r\n";
	$headers.= iconv_mime_encode('Subject', $subject, $preferences);

	$data = [
		'page' => 'undefined',
		'txtname' => null,
		'txtemail' => null,
		'txtphone' => null,
		'txtquestion' => '-',
	];
	foreach (array_keys($data) as $name) {
		if (array_key_exists($name, $_REQUEST)
			&& is_string($value = $_REQUEST[$name])) {
			$data[$name] = $value;
		}
	}

	$result = [];
	if (0 != count(array_keys($data, null, true))
		|| !mail($email, $subject, sprintf($template, $data['page'], $data['txtname'], $data['txtemail'], $data['txtphone'], $data['txtquestion']), $headers)) {
		$message = 'error';
	} else {
		$message = 'ok';
	}
	
	header('Content-Type: text/plain; charset=UTF-8');
	echo $message;

	//header('Content-Type: application/json; charset=UTF-8');
	//echo json_encode(['msg' => $message]);

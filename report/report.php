<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Космические пришельцы похищали меня - сообщение о похищении</title>
		<link rel="stylesheet" href="style.css" />
	</head>
	<body>
		<h2>Космические пришельцы похищали меня - сообщение о похищении</h2>
		<?php
			$first_name = $_POST['firstname'];
			$last_name = $_POST['lastname'];
			$when_it_happened = $_POST['whenithappened'];
			$how_long = $_POST['howlong'];
			$how_many = $_POST['howmany'];
			$alien_description = $_POST['aliendescription'];
			$what_they_did = $_POST['whattheydid'];
			$fang_spotted = $_POST['fangspotted'];
			$other = $_POST['other'];
			$email = $_POST['email'];
			/*
			$to = 'consultantvs@yandex.ru';
			$subject = 'Космические пришельцы похищали меня - сообщение о похищении';
			$msg = "$name был похищен $when_it_happened и отсутствовал в течении $how_long\n" .
			"Количество космических пришельцев $how_many\n" .
			"Описание космических пришельцев: $alien_description\n" .
			"Что они делали? $what_they_did\n" .
			"Фенг замечен? $fang_spotted\n" .
			"Дополнительная информация: $other";
			mail($to, $subject, $msg, 'From: ' . $email);
			*/
			$dbc = mysqli_connect ('localhost', 'root', 'root', 'aliendatabase')
				or die ('Ошибка соединения с MySQL-сервером');
			
			$query = "INSERT INTO aliens_abduction (first_name, last_name, " .
				"when_it_happened, how_long, how_many, alien_description, " .
				"what_they_did, fang_spotted, other, email) " .
				"VALUES ('$first_name', '$last_name', '$when_it_happened', '$how_long', '$how_many', " .
				"'$alien_description', '$what_they_did', " .
				"'$fang_spotted', '$other', '$email')";
			
			$result = mysqli_query ($dbc, $query)
				or die ('Ошибка при выполнении запроса к базе данных.');
				
			mysqli_close($dbc);
			
			echo 'Спасибо за заполнение формы.<br />';
			echo 'Вы были похищены ' . $when_it_happened;
			echo ' и отсутствовали в течение ' . $how_long . '<br />';
			echo 'Количество космических пришельцев: ' . $how_many . '<br />';
			echo 'Опишите их: ' . $alien_description . '<br />';
			echo 'Что они делали: ' . $what_they_did . '<br />';
			echo 'Видели ли вы мою собаку Фэнга? ' . $fang_spotted . '<br />';
			echo 'Дополнительная информация: ' . $other . '<br />';
			echo 'Ваш адрес электронной почты: ' . $email;
		?>
	</body>
</html>
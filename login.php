<?php
	require_once('connectvars.php');
  // Старт сессии
  session_start();
  // Обнуление собщения об ошибке
  $error_msg = "";
  // Если пользователь еще не вошел в приложение, попытка войти
  if (!isset($_SESSION['user_id'])) {

    if (isset($_POST['submit'])) {
      // Соединение с базой данных
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        or die('Ошибка соединения с MySQL-сервером');
      // Получение введенных пользователем данных для аутентификации
      $user_username = mysqli_real_escape_string($dbc, trim($_POST['username']));
      $user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));

      if (!empty($user_username) && !empty($user_password)) {
        $query = "SELECT user_id, username FROM mismatch_user WHERE username = '$user_username' AND password = SHA('$user_password')";
          $data = mysqli_query($dbc, $query);

        if (mysqli_num_rows($data) == 1) {
          // Вход в npиложение прошел успешно, присваиваем значение идентификатора пользователя и его имени
          // переменным сессии (и куки) и переадресуем браузер на главную страницу
          $row = mysqli_fetch_array($data);
          $_SESSION['user_id'] = $row['user_id'];
          $_SESSION['username'] = $row['username'];
          setcookie('user_id', $row['user_id'], time() + (60 * 60 * 24 * 30)); // Срок истечения cookie через 30 дней
          setcookie('username', $row['username'], time() + (60 * 60 * 24 *30)); // Срок истечения cookie через 30 дней
          $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
          header('Location: ' . $home_url);
        }
        else {
          // Имя пользователя/его пароль введеный неверно, создание сообщения об ошибке
          $error_msg = 'Извените, для того чтобы войти в приложение, вы должны ввести правильные имя и пароль.';
        }
      }
      else{
        // Имя пользователя/его пароль не введены, создание сообщения об ошибке
        $error_msg = 'Извените, для того чтобы войти в приложение, вы должны ввести имя и пароль.';
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
		<title>Несоответствия. Вход в пpиложение.</title>
		<link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <h3>Несоответствия. Вход в пpиложение.</h3>

    <?php
      echo '<p><a href="index.php">Вернуться на главную.</a></p>';
    // Если куки не содержит данных, выводятся сообщения об ошибке
    // и форма форма входа в приложение; в противном случае подтверждение входа
      if (!isset($_SESSION['user_id'])) {
        echo '<p class="error">' . $error_msg . '</p>';
        ?>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <fieldset>
            <legend>Вход в приложение</legend>
            <label for="username">Имя пользователя:</label>
            <input type="text" name="username" value="<?php if (!empty($user_username)) echo $user_username; ?>" /><br/>
            <label for="password">Пароль:</label>
            <input type="password" name="password" />
          </fieldset>
          <input type="submit" value="Войти" name="submit" />
        </form>

        <?php
      }
      else {
        // Поддтверждение успешного входа в приложение
        echo ('<p class="login">Вы вошли в приложение как ' . $_SESSION['username'] . '.</p>');
      }
    ?>
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Mismatch - Sign Up</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <h3>Mismatch - Sign Up</h3>

    <?php
      require_once('appvars.php');
      require_once('connectvars.php');
      echo '<p><a href="index.php">Вернуться на главную.</a></p>';
      // Connect to the database 
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
          or die('Ошибка соединения с MySQL-сервером');
      if (isset($_POST['submit'])) {
        // Извлечение данных профиля из суперглобального массива $_POST
        $username = mysqli_real_escape_string($dbc, trim($_POST['username']));
        $password1 = mysqli_real_escape_string($dbc, trim($_POST['password1']));
        $password2 = mysqli_real_escape_string($dbc, trim($_POST['password2']));

        if (!empty($username) && !empty($password1) && !empty($password2) && ($password1 == $password2)) {
          // Проверка того, что никто из уже записанных пользователей не пользуется таким же именем,
          // как то, которое ввел новый пользователь
          $query = "SELECT * FROM mismatch_user WHERE username = '$username'";
          $data = mysqli_query($dbc, $query);

          if (mysqli_num_rows($data) == 0) {
            // Имя, введенное пользавателем, не используется, поэтому добавляем данные в базу
            $query = "INSERT INTO mismatch_user (username, password, join_date) VALUES" .
            "('$username', SHA('$password1'), NOW())";
            mysqli_query($dbc, $query);
            // Вывод подтверждения пользователю
            echo '<p>Ваша новая учетная запись успешно создана. Теперь Вы можете ' . 
            '<a href="login.php">войти в приложение</a></p>';
            mysqli_close($dbc);
            exit();
          }
          // Учетная запись с таким именем уже существует в базе данных, поэтому выводится сообщение об ошибке
          else {
              echo '<p class="error">Учетная запись с таким именем уже существует . Введите , пожалуйста, другое имя.</p>';
              $username = "";
          }
        }
        else {
            echo '<p class="error">Вы должны ввести все данные для создания учетной записи, в том числе пароль - дважды.</р>';
        }
      }

      mysqli_close($dbc);
    ?>

    <p>Введите, пожалуйста, ваше имя и пароль для создания учетной записи в приложении &quot;Несоответствия&quot;.</p>
    <form method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
      <fieldset>
        <legend>Входные данные</legend>
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" value="<?php if (!empty($username)) echo $username;?>"/><br/>
        <label for="password1">Пароль:</label>
        <input type="password" id="password1" name="password1"/><br/>
        <label for="password2">Повторите пароль:</label>
        <input type="password" id="password2" name="password2"/><br/>
      </fieldset>
      <input type="submit" name="submit" value="Создать"/>
    </form>
  </body> 
</html>
<?php
  // Generate the navigation menu
  if (isset($_SESSION['username'])) {
    echo '&#10084; <a href="index.php">Главная страница</a>';
    echo ' &#10084; <a href="viewprofile.php">Просмотр профиля</a>';
    echo ' &#10084; <a href="editprofile.php">Редактирование профиля</a>';
    echo ' &#10084; <a href="questionnaire.php">Анкета</a>';
    echo ' &#10084; <a href="mymismatch.php">Мое несоответствие</a>';
    echo ' &#10084; <a href="logout.php">Выход (' . $_SESSION['username'] . ')</a>';
  }
  else {
    echo '&#10084; <a href="login.php">Вход в систему</a>';
    echo ' &#10084; <a href="signup.php">Регистрация</a>';
  }
?>

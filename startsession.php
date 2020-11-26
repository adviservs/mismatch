<?php
    // Старт сессии
  session_start();
    // Проверяем если переменные сессии не имеют значений
  if (!isset($_SESSION['user_id'])) {
      // Проверяем если переменные куки имеют знаяения,
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
        // присвоение значения от соответствующих куки
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      $_SESSION['username'] = $_COOKIE['username'];
    }
  }
?>
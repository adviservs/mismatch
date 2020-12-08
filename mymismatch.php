<?php
  // Custom function to draw a bar graph given a data set, maximum value, and image filename
  function draw_bar_graph($width, $height, $data, $max_value, $filename) {
		// Создание пустого изобржения
		$img = imagecreatetruecolor($width, $height);
		
		// Установка цветов: белого для фона, белого и черного для текста и серого для графика
		$bg_color = imagecolorallocate($img, 255, 255, 255); // белый цвет для фона
		$text_color = imagecolorallocate($img, 255, 255, 255); // белый цвет для текста
		$bar_color = imagecolorallocate($img, 0, 0, 0); // черный цвет для баров
		$border_color = imagecolorallocate($img, 192, 192, 192); // серый цвет для бордера
		
		// Заполнение фона
		imagefilledrectangle($img, 0, 0, $width, $height, $bg_color);
		
		// Рисование столбцов гистограммы
		$bar_width = $width / ((count($data) * 2) + 1);
		
		for ($i = 0; $i < count($data); $i++) {
			imagefilledrectangle($img, ($i * $bar_width * 2) + $bar_width, $height,
			($i * $bar_width * 2) + ($bar_width * 2), $height - (($height / $max_value) * $data[$i][1]), $bar_color);
			imagestringup($img, 5, ($i * $bar_width * 2) + ($bar_width), $height - 5, $data[$i][0], $text_color);
		}
		
		// Рисование: прямоугольник вокруг всей гистограммы
		imagerectangle($img, 0, 0, $width - 1, $height - 1, $border_color);
		
		// Рисование: диапазон значений слева от гистограммы
		for ($i = 1; $i <= $max_value; $i++) {
			imagestring($img, 5, 0, $height - ($i * ($height / $max_value)), $i, $bar_color);
		}
		
		// Сохранение гистограммы в файл
		imagepng($img, $filename, 5);
		
		imagedestroy($img);
  }
  
    // Start the session
    // Открытие сессии, сценарий проверки, вошел пользователь в приложение или нет
  require_once('startsession.php');

    // Insert the page header
    // Вывод заголовков страницы
  $page_title = 'Мое несоответствие';
  require_once('header.php');

    // Подключаем файлы с константами для подключения к базе данных и загрузке файлов
  require_once('appvars.php');
  require_once('connectvars.php'); 

    // Make sure the user is logged in before going any further.
    // Прежде чем продолжать сценарий, необходимо проверить вошел ли пользователь в приложение
  if (!isset( $_SESSION['user_id'])) {
    echo '<p class="login">Пожалуйста, <а hrеf="lоgin.рhр">войдите в приложение </a>' .
      'чтобы получить доступ к этой странице </p>';
      exit();
  }

    // Выводим навигационное меню
  require_once('navmenu.php');

    // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
    or die('Ошибка соединения с MySQL-сервером');

    // Поиск несоответствий производится, только если пользователь
    // уже внес в анкету свои значения признаков несоответствия
  $query = "SELECT * FROM mismatch_response WHERE user_id ='" . $_SESSION['user_id'] . "'";
  $data = mysqli_query($dbc, $query);

  if (mysqli_num_rows($data) != 0) {
    // Вначале извлечение значений признаков несоответствия из таблицы,
    // содержащей информацию опризнаках несоответствия пользователей
    // (для получения наименований признаков несоответствия используется объединение - JOIN)
    $query = "SELECT mr.response_id, mr.topic_id, mr.response, mt.name AS topic_name, mc.name AS category_name " .
					"FROM mismatch_response AS mr " .
					"INNER JOIN mismatch_topic AS mt USING (topic_id) " .
					"INNER JOIN mismatch_category AS mc USING (category_id) " .
					"WHERE mr.user_id = '" . $_SESSION['user_id'] . "'";
    $data = mysqli_query($dbc, $query);
    $user_responses = array();
    
    while ($row = mysqli_fetch_array($data)) {
      array_push($user_responses, $row);
    }

    // Инициализация переменных, в которых будут сохранены результаты поиска
    $mismatch_score = 0;
	  $mismatch_user_id = -1;
    $mismatch_topics = array();
    $mismatch_categories = array();
    
      // Проход в цикле записей в таблице, содержащей информацию о пользователях,
      // и сравнение значений признаков несоответствия пользователя с такими же значениями других
      // пользователей
    $query = "SELECT user_id FROM mismatch_user WHERE user_id != '" . $_SESSION['user_id'] . "'";
    $data = mysqli_query($dbc, $query);

    while ($row = mysqli_fetch_array($data)) {
        // Извлечение значений признаков несоответствий для пользователя (кандидата на наилучшее несоответствие)
      $query2 = "SELECT response_id, topic_id, response FROM mismatch_response WHERE user_id = '" . $row['user_id'] . "'";
      $data2 = mysqli_query($dbc, $query2);
      $mismatch_responses = array();

      while ($row2 = mysqli_fetch_array($data2)) {
        array_push($mismatch_responses, $row2);
      }

      # var_dump($row);
      # echo 'массив1';
      # var_dump($user_responses);
      # echo 'массив2';
      # var_dump($mismatch_responses);
      # exit();


        // Сравнение значений пpизнаков несоответствия и вычисление оценки несоответствия
      $score = 0;
      $topics = array();
      $categories = array();

      for ($i = 0; $i < count($user_responses); $i++) {
        if ($user_responses[$i]['response'] + $mismatch_responses[$i]['response'] == 3) {
          $score += 1;
          array_push($topics, $user_responses[$i]['topic_name']);
          array_push($categories, $user_responses[$i]['category_name']);
        }
      }

        // Оценка несоответствия текущего пользователя
        // сравнивается с наилучшей оценкой на данный момент
      if ($score > $mismatch_score) {

          // Найдено лучшее несоответствие, поэтому переменные,
          // отслеживающие параметры процесса поиска, обновляются.
        $mismatch_score = $score;
        $mismatch_user_id = $row['user_id'];
        $mismatch_topics = array_slice($topics, 0);
        $mismatch_categories = array_slice($categories, 0);
      }
    }

    if ($mismatch_user_id != -1) {
      $query = "SELECT username, first_name, last_name, city, state, picture FROM mismatch_user WHERE user_id = '$mismatch_user_id'";
      $data = mysqli_query($dbc, $query);

      if (mysqli_num_rows($data) == 1) {
          // Запись пользователя с наилучшим несоответствием найдена в таблице,
          // вывод информации об этом пользователе
        $row = mysqli_fetch_array($data);
        echo '<table><tr><td class="lable">';

        if (!empty($row['first_name']) && !empty($row['last_name'])) {
          echo $row['first_name'] . ' ' . $row['last_name'] . '<br/>';
        }

        if (!empty($row['city']) && !empty($row['state'])) {
          echo $row['city'] . ' ' . $row['state'] . '<br/>';
        }
        echo '</td><td>';

        if (!empty($row['picture'])) {
          echo '<img src="' . MM_UPLOADPATH . $row['picture'] . '" alt="Фотография пользоваетеля" /><br/>';
        }
        echo '</td></tr></table>';

        // Вывод значений признаков несоответствия
        echo '<h4>Вы несоответсвуете по следующим ' . count($mismatch_topics) . ' признакам:</h4>';
        echo '<table><tr>';
        $i = 0;
        foreach ($mismatch_topics as $topic) {
          echo '<td>' . $topic . '</td>';
          if (++$i > 4) {
            echo '</tr><tr>';
            $i = 0;
          }
        }
        echo '</tr></table>';

        $category_totals = array(array($mismatch_categories[0], 0));
	
        foreach ($mismatch_categories as $category) {
          
          if ($category_totals[count($category_totals) - 1][0] != $category) {
            array_push($category_totals, array($category, 1));
          }
          else {
            $category_totals[count($category_totals) - 1][1]++;
          }
        }

        // Generate and display the mismatched category bar graph image
        echo '<h4>Mismatched category breakdown:</h4>';
        draw_bar_graph(480, 240, $category_totals, 5, MM_UPLOADPATH . $_SESSION['user_id'] . '-mymismatchgraph.png');
        echo '<img src="' . MM_UPLOADPATH . $_SESSION['user_id'] . '-mymismatchgraph.png" alt="Mismatch category graph" /><br />';

          // Вывод гиперссылки на профиль пользователя с наилучшим несоответствием
        echo '<h4>Промотр профиля <a href="viewprofile.php?user_id=' . $mismatch_user_id . '">' . $row['first_name'] . ' </a></h4>';
      }
    }
  }
  else {
    echo '<p>Вы должны <a href="questionnaire.php">заполнить анкету, </a>' .
        'прежде чем для вас модет быть найдено несоответствие.</p>';
  }

  mysqli_close($dbc);

  // Insert the page footer
  require_once('footer.php');
?>
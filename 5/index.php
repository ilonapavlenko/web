<?php

header('Content-Type: text/html; charset=UTF-8');

$user = 'u52854';
$pass = '6516432';
$db = new PDO('mysql:host=localhost;dbname=u52854', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();
  $messages1 = array();
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    $messages1['allok'] = '<div class="good">Спасибо, результаты сохранены</div>';
    if (!empty($_COOKIE['password'])) {
      $messages1['login'] = sprintf('<div class="login">Логин: <strong>%s</strong><br>
        Пароль: <strong>%s</strong><br>Войдите в аккаунт с этими данными,<br>чтобы изменить введёные значения формы</div>',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['password']));
    }
    setcookie('login', '', 100000);
    setcookie('password', '', 100000);
  }

  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']);
  $errors['email1'] = !empty($_COOKIE['email_error1']);
  $errors['email2'] = !empty($_COOKIE['email_error2']);
  $errors['date1'] = !empty($_COOKIE['date_error1']);
  $errors['date2'] = !empty($_COOKIE['date_error2']);
  $errors['sex1'] = !empty($_COOKIE['sex_error1']);
  $errors['sex2'] = !empty($_COOKIE['sex_error2']);
  $errors['limbs1'] = !empty($_COOKIE['limbs_error1']);
  $errors['limbs2'] = !empty($_COOKIE['limbs_error2']);
  $errors['abilities1'] = !empty($_COOKIE['abilities_error1']);
  $errors['abilities2'] = !empty($_COOKIE['abilities_error2']);
  $errors['bio1'] = !empty($_COOKIE['bio_error1']);
  $errors['bio2'] = !empty($_COOKIE['bio_error2']);
  $errors['checkbox'] = !empty($_COOKIE['checkbox_error']);

  if (!empty($errors['fio'])) {
    setcookie('fio_error', '', 100000);
    $messages['fio'] = '<p class="msg">Заполните имя</p>';
  }
  if (!empty($errors['email1'])) {
    setcookie('email_error1', '', 100000);
    $messages['email1'] = '<p class="msg">Заполните email</p>';
  } else if (!empty($errors['email2'])) {
    setcookie('email_error2', '', 100000);
    $messages['email2'] = '<p class="msg">Корректно* заполните email</p>';
  }
  if (!empty($errors['date1'])) {
    setcookie('date_error1', '', 100000);
    $messages['date1'] = '<p class="msg">Неправильный формат ввода года</p>';
  } else if (!empty($errors['date2'])) {
    setcookie('date_error2', '', 100000);
    $messages['date2'] = '<p class="msg">Вам должно быть 14 лет</p>';
  }
  if (!empty($errors['sex1'])) {
    setcookie('sex_error1', '', 100000);
    $messages['sex1'] = '<p class="msg">Выберите пол</p>';
  }
  if (!empty($errors['sex2'])) {
    setcookie('sex_error2', '', 100000);
    $messages['sex2'] = '<p class="msg">Выбран неизвестный пол</p>';
  }
  if (!empty($errors['limbs1'])) {
    setcookie('limbs_error1', '', 100000);
    $messages['limbs1'] = '<p class="msg">Выберите руку</p>';
  }
  if (!empty($errors['limbs2'])) {
    setcookie('limbs_error2', '', 100000);
    $messages['limbs2'] = '<p class="msg">Выбрана неизвестная рука</p>';
  }
  if (!empty($errors['abilities1'])) {
    setcookie('abilities_error1', '', 100000);
    $messages['abilities1'] = '<p class="msg">Выберите хотя бы одну <br> сверхспособность</p>';
  } else if (!empty($errors['abilities2'])) {
    setcookie('abilities_error2', '', 100000);
    $messages['abilities2'] = '<p class="msg">Выбрана неизвестная <br> сверхспособность</p>';
  }
  if (!empty($errors['bio1'])) {
    setcookie('bio_error1', '', 100000);
    $messages['bio1'] = '<p class="msg">Расскажи о себе что-нибудь</p>';
  } else if (!empty($errors['bio2'])) {
    setcookie('bio_error2', '', 100000);
    $messages['bio2'] = '<p class="msg">Недопустимый формат ввода <br> биографии</p>';
  }
  if (!empty($errors['checkbox'])) {
    setcookie('checkbox_error', '', 100000);
    $messages['checkbox'] = '<p class="msg">Ознакомьтесь с контрактом</p>';
  }

  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['fio_value']));
  $values['email'] = empty($_COOKIE['email_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['email_value']));
  $values['date'] = empty($_COOKIE['date_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['date_value']));
  $values['sex'] = empty($_COOKIE['sex_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['sex_value']));
  $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : htmlspecialchars(strip_tags(strip_tags($_COOKIE['limbs_value'])));
  $values['abilities'] = empty($_COOKIE['abilities_value']) ? '' : strip_tags($_COOKIE['abilities_value']);
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['bio_value']));
  $values['checkbox'] = empty($_COOKIE['checkbox_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['checkbox_value']));

  if (count(array_filter($errors)) === 0 && !empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
    $login = $_SESSION['login'];
    try {
      $stmt = $db->prepare("SELECT application_id FROM users WHERE login = ?");
      $stmt->execute([$login]);
      $app_id = $stmt->fetchColumn();

      $stmt = $db->prepare("SELECT fio, email, date, sex, limbs, bio FROM application WHERE application_id = ?");
      $stmt->execute([$app_id]);
      $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $stmt = $db->prepare("SELECT superpower_id FROM abilities WHERE application_id = ?");
      $stmt->execute([$app_id]);
      $abilities = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

      if (!empty($dates[0]['fio'])) {
        $values['fio'] = htmlspecialchars($dates[0]['fio']);
      }
      if (!empty($dates[0]['email'])) {
        $values['email'] = htmlspecialchars($dates[0]['email']);
      }
      if (!empty($dates[0]['date'])) {
        $values['date'] = htmlspecialchars($dates[0]['date']);
      }
      if (!empty($dates[0]['sex'])) {
        $values['sex'] = htmlspecialchars($dates[0]['sex']);
      }
      if (!empty($dates[0]['limbs'])) {
        $values['limbs'] = htmlspecialchars($dates[0]['limbs']);
      }
      if (!empty($abilities)) {
        $values['abilities'] =  serialize($abilities);
      }
      if (!empty($dates[0]['bio'])) {
        $values['bio'] = htmlspecialchars($dates[0]['bio']);
      }

    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }

    printf('<div id="header"><p>Вход с логином %s; uid: %d</p><a href=logout.php>Выйти</a></div>', $_SESSION['login'], $_SESSION['uid']);
  }
  include('form.php');
} else {
  $errors = FALSE;

  $fio = $_POST['fio'];
  $email = $_POST['email'];
  $date = $_POST['date'];
  $sex = $_POST['sex'];
  $limbs = $_POST['limbs'];
  if(isset($_POST["abilities"])) {
    $abilities = $_POST["abilities"];
    $filtred_abilities = array_filter($abilities,
    function($value) {
      return($value == 1 || $value == 2 || $value == 3);
    }
    );
  }
  $bio = $_POST['bio'];
  $checkbox = isset($_POST['checkbox']);

  if (empty($fio)) {
    setcookie('fio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('fio_value', $fio, time() + 30 * 24 * 60 * 60);
  }

  if (empty($email)) {
    setcookie('email_error1', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error2', '1', time() + 24 * 60 * 60);
    setcookie('email_value', $email, time() + 30 * 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('email_value', $email, time() + 30 * 24 * 60 * 60);
  }

  if (!is_numeric($date)) {
    setcookie('date_error1', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else if ((2023 - $date) < 14) {
    setcookie('date_error2', '1', time() + 24 * 60 * 60);
    setcookie('date_value', $date, time() + 30 * 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('date_value', $date, time() + 30 * 24 * 60 * 60);
  }

  if (empty($sex)) {
    setcookie('sex_error1', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else if ($sex != 'male' && $sex != 'female') {
    setcookie('sex_error2', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('sex_value', $sex, time() + 30 * 24 * 60 * 60);
  }

  if (empty($limbs)) {
    setcookie('limbs_error1', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else if ($limbs != 'right' && $limbs != 'left') {
    setcookie('limbs_error2', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('limbs_value', $limbs, time() + 30 * 24 * 60 * 60);
  }

  if (empty($abilities)) {
    setcookie('abilities_error1', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else if (count($filtred_abilities) != count($abilities)) {
    setcookie('abilities_error2', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('abilities_value', serialize($abilities), time() + 30 * 24 * 60 * 60);
  }

  if (empty($bio)) {
    setcookie('bio_error1', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else if (!preg_match('/^[\p{Cyrillic}\d\s,.!?-]+$/u', $bio)) {
    setcookie('bio_error2', '1', time() + 24 * 60 * 60);
    setcookie('bio_value', $bio, time() + 30 * 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('bio_value', $bio, time() + 30 * 24 * 60 * 60);
  }

  if ($checkbox == '') {
    setcookie('checkbox_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('checkbox_value', $checkbox, time() + 30 * 24 * 60 * 60);
  }

  if ($errors) {
    header('Location: index.php');
    exit();
  } else {
    setcookie('fio_error', '', 100000);
    setcookie('email_error1', '', 100000);
    setcookie('email_error2', '', 100000);
    setcookie('date_error1', '', 100000);
    setcookie('date_error2', '', 100000);
    setcookie('sex_error1', '', 100000);
    setcookie('sex_error2', '', 100000);
    setcookie('limbs_error1', '', 100000);
    setcookie('limbs_error2', '', 100000);
    setcookie('abilities_error1', '', 100000);
    setcookie('abilities_error2', '', 100000);
    setcookie('bio_error1', '', 100000);
    setcookie('bio_error2', '', 100000);
    setcookie('checkbox_error', '', 100000);
  }

  if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
    if (!empty($_POST['token']) && hash_equals($_POST['token'], $_SESSION['token'])) {
      $login = $_SESSION['login'];
      try {
        $stmt = $db->prepare("SELECT application_id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $app_id = $stmt->fetchColumn();

        $stmt = $db->prepare("UPDATE application SET fio = ?, email = ?, date = ?, sex = ?, limbs = ?, bio = ?
          WHERE application_id = ?");
        $stmt->execute([$fio, $email, $date, $sex, $limbs, $bio, $app_id]);

        $stmt = $db->prepare("SELECT superpower_id FROM abilities WHERE application_id = ?");
        $stmt->execute([$app_id]);
        $abil = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        if (array_diff($abil, $abilities)) {
          $stmt = $db->prepare("DELETE FROM abilities WHERE application_id = ?");
          $stmt->execute([$app_id]);

          $stmt = $db->prepare("INSERT INTO abilities (application_id, superpower_id) VALUES (?, ?)");
          foreach ($abilities as $superpower_id) {
            $stmt->execute([$app_id, $superpower_id]);
          }
        }

      } catch (PDOException $e) {
          print('Error : ' . $e->getMessage());
          exit();
      }
    } else {
      die('Ошибка CSRF: недопустимый токен');
    }
  }
  else {
    $login = 'user' . rand(1, 1000);
    $password = rand(1, 100);
    setcookie('login', $login);
    setcookie('password', $password);
    try {
      $stmt = $db->prepare("INSERT INTO application (fio, email, date, sex, limbs, bio) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->execute([$fio, $email, $date, $sex, $limbs, $bio]);
      $application_id = $db->lastInsertId();
      $stmt = $db->prepare("INSERT INTO abilities (application_id, superpower_id) VALUES (?, ?)");
      foreach ($abilities as $superpower_id) {
        $stmt->execute([$application_id, $superpower_id]);
      }
      $stmt = $db->prepare("INSERT INTO users (application_id, login, password) VALUES (?, ?, ?)");
      $stmt->execute([$application_id, $login, md5($password)]);
    } catch (PDOException $e) {
      print('Error : ' . $e->getMessage());
      exit();
    }
  }

  setcookie('save', '1');
  header('Location: ./');
}


<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
         $messages = array();


  // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
   if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Если есть параметр save, то выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
      if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
  }
  
  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['date'] = !empty($_COOKIE['date_error']);
  $errors['sex'] = !empty($_COOKIE['sex_error']);
  $errors['limbs'] = !empty($_COOKIE['limbs_error']);
  $errors['abilities'] = !empty($_COOKIE['abilities_error']);
  $errors['bio'] = !empty($_COOKIE['bio_error']);
  $errors['checkbox'] = !empty($_COOKIE['checkbox_error']);
  
  if ($errors['fio']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('fio_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните имя. <br> Используйте символы: А-Я, а-я, A-Z,a-z</div>';
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div class="error">Заполните email. <br> Должен содержать @ </div>';
  }
  if ($errors['date']) {
    setcookie('date_error', '', 100000);
    $messages[] = '<div class="error">Заполните дату рождения.</div>';
  }
  if ($errors['sex']) {
    setcookie('sex_error', '', 100000);
    $messages[] = '<div class="error">Укажите пол.</div>' ;
  }
  if ($errors['limbs']) {
    setcookie('limbs_error', '', 100000);
    $messages[] = '<div class="error">УУкажите количество конечностей.</div>';
  }
  if ($errors['abilities']) {
    setcookie('abilities_error', '', 100000);
    $messages[] = '<div class="error">Укажите сверхспособность.</div>';
  }
  if ($errors['bio']) {
    setcookie('bio_error', '', 100000);
    $messages[] = '<div class="error">Заполните биографию. <br> Используйте символы: А-Я, а-я, A-Z,a-z</div>';
  }
  if ($errors['checkbox']) {
    setcookie('checkbox_error', '', 100000);
    $messages[] = '<div class="error">Примите условия соглашения.</div>';
  }
  
  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : strip_tags($_COOKIE['fio_value']);
  $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
  $values['date_of_birth'] = empty($_COOKIE['date_value']) ? '' :strip_tags($_COOKIE['date_value']);
  $values['sex'] = empty($_COOKIE['sex_value']) ? '' : strip_tags($_COOKIE['sex_value']);
  $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : strip_tags($_COOKIE['limbs_value']);
  $values['abilities'] = empty($_COOKIE['abilities_value']) ? array() : unserialize($_COOKIE['abilities_value']);
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
  $values['checkbox'] = empty($_COOKIE['checkbox_value']) ? '' : strip_tags($_COOKIE['checkbox_value']);
  
  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  //todo fix_errors
  if (count(array_filter($errors)) === 0 && !empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {


        $stmt = $db->prepare("SELECT * FROM application2 where user_id=?");
        $stmt -> execute([$_SESSION['uid']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $values['fio'] = empty($result[0]['fio']) ? '' : strip_tags($result[0]['fio']);
        $values['email'] = empty($result[0]['email']) ? '' : strip_tags($result[0]['email']);
  $values['date'] = empty($result[0]['date']) ? '' :strip_tags($result[0]['date']);
  $values['sex'] = empty($result[0]['sex']) ? '' : strip_tags($result[0]['sex']);
  $values['limbs'] = empty($result[0]['limbs']) ? '' : strip_tags($result[0]['limbs']);
  $values['bio'] = empty($result[0]['bio']) ? '' : strip_tags($result[0]['bio']);
  $values['checkbox'] = empty($result[0]['checkbox']) ? '' : strip_tags($result[0]['checkbox']);
  
  $stmt = $db->prepare("SELECT * FROM app_ability2 where id_app=(SELECT id FROM application2 where user_id=?) ");
 

  $stmt -> execute([$_SESSION['uid']]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  
  
  $values['abilities'][0] = empty($result[0]["id_ab"]) ? '' : strip_tags($result[0]["id_ab"]);
  $values['abilities'][1] = empty($result[1]["id_ab"]) ? '' : strip_tags($result[1]["id_ab"]);
  $values['abilities'][2] = empty($result[2]["id_ab"]) ? '' : strip_tags($result[2]["id_ab"]);


    // TODO: загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
  printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  }
  
  // Включаем содержимое файла form.php.
  include('form.php');
  
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else{
// Проверяем ошибки.
$errors = FALSE;
if (empty($_POST['fio'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('fio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
           if(!preg_match('/^([а-яА-ЯЁёa-zA-Z0-9_,.\s-]+)$/u', $_POST['fio'])){
        setcookie('fio_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    // Сохраняем ранее введенное в форму значение на год.
    setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60 );
  }

  if (empty($_POST['email'])) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
           if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        setcookie('email_error', '1', time() + 24 * 60 * 60);
      $errors = TRUE;
    }
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60 );
  }

  if (empty($_POST['date'])) {
    setcookie('date_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
           if(!preg_match('%[1-2][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]%', $_POST['date'])){
        $errors = TRUE;
        setcookie('date', '1', time() + 24 * 60 * 60);
    }
    setcookie('date_value', $_POST['date'], time() + 30 * 24 * 60 * 60 );
  }

  if (empty($_POST['sex'])) {
    setcookie('sex_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
           if( !in_array($_POST['sex'], ['m','w'])){
        $errors = TRUE;
        setcookie('sex_error', '1', time() + 24 * 60 * 60);
    }
    setcookie('sex_value', $_POST['sex'], time() + 30 * 24 * 60 * 60 );
  }

  if (empty($_POST['limbs'])) {
    setcookie('limbs_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
           if(!in_array($_POST['limbs'], [1,2,3,4])){
        setcookie('limbs_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('limbs_value', $_POST['limbs'], time() + 30 * 24 * 60 * 60 );
  }

  if (empty($_POST['abilities'])) {
    setcookie('abilities_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    foreach ($_POST['abilities'] as $ability) {
          if (!in_array($ability, [1,2,3])){
              setcookie('abilities_error', '1', time() + 24 * 60 * 60);
              $errors = TRUE;
              break;
          }
      }
    setcookie('abilities_value', $_POST['abilities'], time() + 30 * 24 * 60 * 60 );
  }

  if (empty($_POST['bio'])) {
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('bio_value', $_POST['bio'], time() + 30 * 24 * 60 * 60 );
  }

  if (empty($_POST['checkbox'])) {
    setcookie('checkbox_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
           if(!preg_match('/^([а-яА-ЯЁёa-zA-Z0-9_,.\s-]+)$/u', $_POST['bio'])){
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
}
    setcookie('checkbox_value', $_POST['checkbox'], time() + 30 * 24 * 60 * 60 );
  }


if ($errors) {
  // При наличии ошибок завершаем работу скрипта.
  header('Location: index.php');
  exit();
}
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('fio_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('date_error', '', 100000);
    setcookie('sex_error', '', 100000);
    setcookie('limbs_error', '', 100000);
    setcookie('abilities_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('checkbox_error', '', 100000);
  }

 // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
          
          
          $stmt = $db->prepare("UPDATE application2 SET fio = ?, email = ?, date = ?,sex = ?, limbs=?, bio = ?, checkbox =?  WHERE user_id = ?");
          
          $stmt -> execute([$_POST['fio'], $_POST['email'], $_POST['date'], $_POST['sex'], $_POST['limbs'], $_POST['bio'], $_POST['checkbox'], $_SESSION['uid']]);
    // TODO: перезаписать данные в БД новыми данными,
    // кроме логина и пароля.
  }
  else {
    // Генерируем уникальный логин и пароль.
    // TODO: сделать механизм генерации, например функциями rand(), uniqid(), md5(), substr().
    $login = substr(uniqid('', true), -8, 8);
    $pass = uniqid();
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('pass', $pass);

// Сохранение в базу данных.

$user = 'u52854';
$pass = '6516432';
$db = new PDO('mysql:host=localhost;dbname=u52854', $user, $pass,
[PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX

// Подготовленный запрос. Не именованные метки.
try {

  $stmt = $db->prepare("INSERT INTO application (name,email,date,sex,limbs,bio,checkbox) VALUES 
  (?,?,?,?,?,?,?)");
  $stmt -> execute([$_POST['fio'], $_POST['email'], $_POST['date'], $_POST['sex'], $_POST['limbs'], $_POST['bio'], $_POST['checkbox']]);
  $id = $db->lastInsertId();
  $stmt = $db->prepare("INSERT INTO app_ability (id_app, id_ab) VALUES (?,?)");
    foreach ($_POST['abilities'] as $ability) {
          $stmt->execute([$id, $ability]);
        }



 
}
catch(PDOException $e){
  print('Error : ' . $e->getMessage());
  exit();
}
  }

 // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}

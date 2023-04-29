<?php



// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  // TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
  // Делаем перенаправление на форму.
  header('Location: ./');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>

<form action="" method="post">
  <input name="login" />
  <input name="pass" />
  <input type="submit" value="Войти" />
</form>

<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
  $user = 'u52854';
  $pass = '6516432';
  $db = new PDO('mysql:host=localhost;dbname=u52854', $user, $pass,
      [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); 
      try {
      $stmt = $db->prepare("SELECT * FROM user 
      where user=? and pass = ?");
      $stmt -> execute([$_POST['login'], md5($_POST['pass'])]);
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
  // TODO: Проверть есть ли такой логин и пароль в базе данных.
  // Выдать сообщение об ошибках.
  if(!empty($result)){
  // Если все ок, то авторизуем пользователя.
  $_SESSION['login'] = $_POST['login'];
  // Записываем ID пользователя.
  $_SESSION['uid'] =$result[0]["id"];

  // Делаем перенаправление.
  header('Location: ./');}
}
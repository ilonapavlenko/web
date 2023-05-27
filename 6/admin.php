<?php


include('megamodule.php');
if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
$stmt = $db->prepare("SELECT * FROM admin
      where user=?");
$stmt -> execute([$_SERVER['PHP_AUTH_USER']]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);

}



    if (!$result || !password_verify($_SERVER['PHP_AUTH_PW'], $result['pass'])) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="My site"');
        print('<h1>401 Требуется авторизация</h1>');
        exit();
    }

    
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $messages2 = array();
    if(isset($_POST["row"])){
    
        $ids = $_POST["id"];
        $rows = $_POST["row"];
        
       
    if (isset($_POST['delete'])) {
        

        foreach ($rows as $row) {
            
        $stmt = $db->prepare("DELETE FROM application2 WHERE id=?");
        $stmt -> execute([$ids[$row]]);
        
        $stmt = $db->prepare("DELETE FROM app_ability2 WHERE id_app=?");
        $stmt -> execute([$ids[$row]]);
       
    }
    $messages2[] = 'Элементы удалены.';
    }
    
    if (isset($_POST['update'])) {
        $errors=array();
        foreach ($rows as $row) {
            $data = [
                'fio' => $_POST['fio'][$row],
                'email' => $_POST['email'][$row],
                'date' => $_POST['date'][$row],
                'sex' => $_POST['sex' . $row],
                'limbs' => $_POST['limbs' . $row],
                'bio' => $_POST['bio'][$row],
                'checkbox' => $_POST['checkbox'.$row]
            ];
           // var_dump($data);
            $abilities = $_POST['abilities' . $row];
            $errors[$row]=validateFormData($data, $abilities,$row);

        }
        if (count(array_filter($errors))!=0) {
            // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
            header('Location: admin.php');
            exit();
        }
        else{
        
        foreach ($rows as $row) {
            $data = [
                'fio' => $_POST['fio'][$row],
                'email' => $_POST['email'][$row],
                'date' => $_POST['date'][$row],
                'sex' => $_POST['sex' . $row],
                'limbs' => $_POST['limbs' . $row],
                'bio' => $_POST['bio'][$row],
                'checkbox' => $_POST['checkbox'.$row]
            ];
            
            $abilities = $_POST['abilities' . $row];
            
            update_application($db, $ids[$row], $data, $abilities);
            
        }
        $messages2[] = 'Результаты сохранены.';
        }
    }
    }
    else {
        $messages2[] = 'Вы не выбрали ни одного элемента, который хотите сохранить или удалить!';
    }
}
print('Вы успешно авторизовались и видите защищенные паролем данные.');


    
    
    print("<br>");
    $stmt = $db->prepare("select count(*) from app_ability2 where id_ab =?");
    $stmt -> execute(['1']);
    print("Количество людей со способностью бессмертие: ");
    print($stmt->fetchAll(PDO::FETCH_ASSOC)[0]["count(*)"]);
    print("<br>");
    $stmt -> execute(['2']);
    print("Количество людей со способностью прохождение сквозь стены: ");
    print($stmt->fetchAll(PDO::FETCH_ASSOC)[0]["count(*)"]);
    print("<br>");
    $stmt -> execute(['3']);
    print("Количество людей со способностью левитация: ");
    print($stmt->fetchAll(PDO::FETCH_ASSOC)[0]["count(*)"]);
    $stmt = $db->prepare("SELECT * FROM application2");
    $stmt -> execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<html>
<head>
    <link rel="icon" type="image/x-icon" href="favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <title>Админка</title>
   
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" defer></script>

</head>
<body>
<style>
html, body {
    height: 100vh;
  font-size: 15px;
  }
  
 
  .error {
  border: 2px solid red;
}
#messages{
	text-align: center;
}

body{
padding:10px;
}

#del_adm{
	float:right;
}

</style>
<?php if (!empty($messages2)) {
    print('<div id="messages">');
    // Выводим все сообщения.
    foreach ($messages2 as $message) {
        print($message);
        
    }
    

    
    print('</div>');
}?>

<?php
$counter = 0;



foreach ($result as $res): ?>

<?php
$errors = array();
$errors=err_declare($counter);
$messages = array();
$messages = msg_declare($messages, $errors, $counter);





if (!empty($messages)) {
    print('<div id="messages">');
    // Выводим все сообщения.
    foreach ($messages as $message) {
        print($message);
        
    }
    
    print("<div class='error'>Поля, содержащие ошибки были подсвечены. Последние данные, не содержащие ошибок, были подгружены из бд</div>");
    
    print('</div>');
}

$counter++; 
?>


<?php endforeach; ?>

<form action="" method="POST">
<table class="table table-bordered">
<tr>
<th scope="col">id</th>
<th scope="col">имя</th>
<th scope="col">email</th>
<th scope="col">дата рождения</th>
<th scope="col">пол</th>
<th scope="col">конечности</th>
<th scope="col">способности</th>
<th scope="col">биография</th>

<th scope="col">согласие</th>
<th scope="col">uid</th>
<th scope="col">Выделить</th>
</tr>


<?php
$counter = 0;



foreach ($result as $res): ?>

<?php
$errors = array();
$errors=err_declare($counter);
?>
  <tr>
    <td><?= $res["id"] ?></td>
    <input name="id[]" class="form-control form-control-sm" value="<?= strip_tags($res["id"]) ?>" type="hidden">
    <td><input name="fio[]" class="form-control form-control-sm <?php if ($errors['fio']) {print 'is-invalid';} ?>" placeholder="Введите имя" value="<?= strip_tags($res["fio"])  ?>"></td>
    <td><input name="email[]" type="email" class="form-control form-control-sm <?php if ($errors['email']) {print 'is-invalid';} ?>" id="email" placeholder="Введите почту" value="<?= strip_tags($res["email"]) ?>"></td>
    <td><input name="date[]" type="date" class="form-control form-control-sm <?php if ($errors['date']) {print 'is-invalid';} ?>" value="<?= strip_tags($res["date"]) ?>"></td>
    <td> <label for="g1"><input type="radio" class="form-check-input <?php if ($errors['sex']) {print 'is-invalid';} ?>" name="sex<?= $counter ?>" id="g1" value="m" <?php if ($res["sex"]=="m") {print 'checked';} ?>>
                    М</label> 
                     <label for="g2"><input type="radio" class="form-check-input <?php if ($errors['sex']) {print 'is-invalid';} ?>" name="sex<?= $counter ?>" id="g2" value="w" <?php if ($res["sex"]=="w") {print 'checked';} ?>>
                    Ж</label></td>
                    
    <td> <label for="l1"><input type="radio" class="form-check-input <?php if ($errors['limbs']) {print 'is-invalid';} ?>" name="limbs<?= $counter ?>" id="l1" value="1" <?php if ($res["limbs"]=="1") {print 'checked';} ?>>
                    1</label> 
                    <label for="l2"><input type="radio" class="form-check-input <?php if ($errors['limbs']) {print 'is-invalid';} ?>" name="limbs<?= $counter ?>" id="l2" value="2" <?php if ($res["limbs"]=="2") {print 'checked';} ?>>
                    2</label> 
                    <label for="l3"><input type="radio" class="form-check-input <?php if ($errors['limbs']) {print 'is-invalid';} ?>" name="limbs<?= $counter ?>" id="l3" value="3" <?php if ($res["limbs"]=="3") {print 'checked';} ?>>
                    3</label> 
                     <label for="l4"><input type="radio" class="form-check-input <?php if ($errors['limbs']) {print 'is-invalid';} ?>" name="limbs<?= $counter ?>" id="l4" value="4" <?php if ($res["limbs"]=="4") {print 'checked';} ?>>
                    4</label></td>
<?php     $stmt = $db->prepare("SELECT * FROM app_ability2 where id_app=?");
$stmt -> execute([$res["id"]]);
$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
    <td> <select class="form-control form-control-sm <?php if ($errors['abilities']) {print 'is-invalid';} ?>" name="abilities<?= $counter ?>[]" id="mltplslct" multiple="multiple">
                    <option value="1" <?php if(!empty($result2)) {if ($result2[0]['id_ab']=='1') {print 'selected';}} ?>>бессмертие</option>
                    <option value="2" <?php if(!empty($result2)) {if ((isset($result2[0]['id_ab']) && $result2[0]['id_ab'] == '2') ||
    (isset($result2[1]['id_ab']) && $result2[1]['id_ab'] == '2')) {print 'selected';}} ?>>прохождение сквозь стены</option>
                    <option value="3" <?php if(!empty($result2)) {if ((isset($result2[0]['id_ab']) && $result2[0]['id_ab'] == '3') ||
    (isset($result2[1]['id_ab']) && $result2[1]['id_ab'] == '3') ||
    (isset($result2[2]['id_ab']) && $result2[2]['id_ab'] == '3')) {print 'selected';}} ?>>левитация</option>
                </select></td>
    <td><textarea  name="bio[]" rows="3" class="form-control form-control-sm <?php if ($errors['bio']) {print 'is-invalid';} ?>"><?= strip_tags($res["bio"]) ?></textarea></td>
    <td><input name="checkbox<?= $counter ?>" type="checkbox" class="form-check-input <?php if ($errors['checkbox']) {print 'is-invalid';} ?>" value="1" <?php if ($res["checkbox"]=="1") {print 'checked';} ?>></td>
    <td><?= $res["user_id"] ?></td>
    <td><input type="checkbox" name="row[]" value="<?= $counter ?>"></td>
    <?php $counter++ ?>
  </tr>
<?php endforeach; ?>

</table>
  <button class="btn btn-primary" type="submit" name="update" value="upd">Сохранить</button>
  <button id="del_adm" class="btn btn-primary" type="submit" name="delete" value="del">Удалить</button>
</form>
<?php     $stmt = $db->prepare("SELECT * FROM app_ability2");
$stmt -> execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); ?>




</body>
</html>

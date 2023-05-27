<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <title>6</title>
    <link rel="stylesheet" href="style5.css">
</head>
<body>
<?php
if (!empty($messages)) {
  print('<div id="messages">');
  // Выводим все сообщения.
  foreach ($messages as $message) {
    print($message);
  }


  print('</div>');
}

// Далее выводим форму отмечая элементы с ошибками классом error
// и задавая начальные значения элементов ранее сохраненными.
?>    

<?php
if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])){
    echo '
        <div class = "login">
        <form action="" method="POST" >
            <input type="hidden" name="logout" value="true">
            <button type="submit">Выйти</button>
        </form>
        </div>
    ';
}
else 
    echo'
    <div class = "login">
    <form action="login.php" target="_blank">
    <button>Войти</button>
    </form>
    </div>
';
?>
    <form id="form" action="" method="POST">
        <table>
            <caption>Данные формы</caption>
            <tr> 
                <th>id</th>
                <th>Имя</th>
                <th>email</th>
                <th>Дата рождения</th>
                <th>Пол</th>
                <th>Количество конечностей</th>
                <th>Суперсила</th>
                <th>Биография</th>
            </tr>
            <?php
                foreach ($values as $value) {
                    echo    '<tr>
                            <td style="font-weight: 700;">'; print($value['application_id']); echo '</td>
                            <td>
                                <input class="input" name="fio'.$value['application_id'].'" value="'; print(htmlspecialchars($value['fio'])); echo '">
                            </td>
                            <td>
                                <input class="input" name="email'.$value['application_id'].'" value="'; print(htmlspecialchars($value['email'])); echo '">
                            </td>
                            <td>
                            <input class="input" name="date'.$value['application_id'].'" value="'; print($value['date']); echo '">
                            </td>
                            <td> 
                                <div class="column-item">
                                    <input type="radio" id="g1'.$value['application_id'].'" name="sex'.$value['application_id'].'" value="m" '; if (htmlspecialchars($value['sex']) == 'm') echo 'checked'; echo '>
                                    <label for="g1'.$value['application_id'].'">Мужчина</label>
                                </div>
                                <div class="column-item">
                                    <input type="radio" id="g2'.$value['application_id'].'" name="sex'.$value['application_id'].'" value="w" '; if (htmlspecialchars($value['sex']) == 'w') echo 'checked'; echo '>
                                    <label for="g2'.$value['application_id'].'">Женщина</label>
                                </div>
                            </td>
                            <td>
                                <div class="column-item">
                                Количество конечностей:
                                    <input type="radio" id="l1'.$value['application_id'].'" name="limbs'.$value['application_id'].'" value="1" '; if (htmlspecialchars($value['limbs'] == '1')) echo 'checked'; echo '>
                                    <label for="l1'.$value['application_id'].'">1</label>
                                </div>
                                <div class="column-item">
                                    <input type="radio" id="l2'.$value['application_id'].'" name="limbs'.$value['application_id'].'" value="left" '; if (htmlspecialchars($value['limbs'] == '2')) echo 'checked'; echo '>
                                    <label for="radioLeft'.$value['application_id'].'">2</label>
                                </div>
                            </td>';
                    $stmt = $db->prepare("SELECT powers_id FROM abilities WHERE application_id = ?");
                    $stmt->execute([$value['application_id']]);
                    $abilities = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    echo    '<td class="abilities">
                                <div class="column-item">
                                    <input type="checkbox" id="god'.$value['application_id'].'" name="abilities'.$value['application_id'].'[]" value="1"' . (in_array(1, $abilities) ? ' checked' : '') . '>
                                    <label for="god'.$value['application_id'].'">бессмертие</label>
                                </div>
                                <div class="column-item">
                                    <input type="checkbox" id="noclip'.$value['application_id'].'" name="abilities'.$value['application_id'].'[]" value="2"' . (in_array(2, $abilities) ? ' checked' : '') . '>
                                    <label for="noclip'.$value['application_id'].'">прохождение сквозь стены</label>
                                </div>
                                <div class="column-item">
                                    <input type="checkbox" id="levitation'.$value['application_id'].'" name="abilities'.$value['application_id'].'[]" value="3"' . (in_array(3, $abilities) ? ' checked' : '') . '>
                                    <label for="levitation'.$value['application_id'].'">левитация</label>
                                </div>
                            </td>
                            <td>
                                <textarea name="bio'.$value['application_id'].'" id="" cols="30" rows="4" maxlength="128">'; print (htmlspecialchars($value['bio']); echo '</textarea>
                            </td>
                            <td>
                                <div class="column-item">
                                    <input name="save'.$value['application_id'].'" type="submit" value="save'.$value['application_id'].'"/>
                                </div>
                                <div class="column-item">
                                    <input name="clear'.$value['application_id'].'" type="submit" value="clear'.$value['application_id'].'"/>
                                </div>
                            </td>
                        </tr>'; 
                }
            ?>
        </table>
        <?php if (!empty($_SESSION['login'])) {echo '<input type="hidden" name="token" value="' . $_SESSION["token"] . '">'; } ?>
    </form>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" defer></script>
  <link rel="stylesheet" href="style.css">
  <title>4</title>
</head>
<body>
  <div class="col col-10 col-md-6" id="forma">
    <?php
  if (!empty($messages)) {
    print('<div id="messages">');
  // Выводим все сообщения.
    foreach ($messages as $message) {
      print($message);
  }
  print('</div>');
}
    ?>
        <form id="form" action="" method="POST">
            <div class="group">
                <label for="name">Имя:
                <input name="fio" id="name" class="form-control <?php if ($errors['fio']) {print 'error';} ?>" placeholder="Введите ваше имя" value="<?php print $values['fio']; ?>" ></label>
            </div>
            <div class="group">
                <label for="email">E-mail:
                <input name="email" type="email" id="email" class="form-control <?php if ($errors['email']) {print 'error';} ?>" placeholder="Введите вашу почту" value="<?php print $values['email']; ?>">
                </label>
            </div>
            <div class="group">
                Дата рождения:
                <input name="date" type="date" class="form-control <?php if ($errors['date']) {print 'error';} ?>" value="<?php print $values['date']; ?>" />
            </div>
            <div class="group">
                Пол:
                <label for="g1"><input type="radio" class="form-check-input <?php if ($errors['sex']) {print 'error';} ?>" name="sex" id="g1" value="1" <?php if ($values['sex']=='1') {print 'checked';} ?>>
                    Мужской</label>
                <label for="g2"><input type="radio" class="form-check-input <?php if ($errors['sex']) {print 'error';} ?>" name="sex" id="g2" value="2" <?php if ($values['sex']=='2') {print 'checked';} ?>>
                    Женский</label>
            </div>
            <div class="group">
                Количество конечностей:
                <label for="l1"><input type="radio" class="form-check-input <?php if ($errors['limbs']) {print 'error';} ?>" name="limbs" id="l1" value="1" <?php if ($values['limbs']=='1') {print 'checked';} ?>>
                    1</label>
                <label for="l2"><input type="radio" class="form-check-input <?php if ($errors['limbs']) {print 'error';} ?>" name="limbs" id="l2" value="2" <?php if ($values['limbs']=='2') {print 'checked';} ?>>
                    2</label>
                <label for="l3"><input type="radio" class="form-check-input <?php if ($errors['limbs']) {print 'error';} ?>" name="limbs" id="l3" value="3" <?php if ($values['limbs']=='3') {print 'checked';} ?>>
                    3</label>
                <label for="l4"><input type="radio" class="form-check-input <?php if ($errors['limbs']) {print 'error';} ?>" name="limbs" id="l4" value="4" <?php if ($values['limbs']=='4') {print 'checked';} ?>>
                    4</label>
            </div>
            <div class="group">
                <label for="powers">Сверхспособности:
                <select class="form-control <?php if ($errors['abilities']) {print 'error';} ?>" name="abilities[]" id="powers" multiple="multiple">
                    <option value="1" <?php if(!empty($values['abilities'][0])) {if ($values['abilities'][0]=='1') {print 'selected';}} ?>>Бессмертие</option>
                    <option value="2" <?php if(!empty($values['abilities'][1])) {if ($values['abilities'][0]=='2') {print 'selected';}} ?>>Прохождение сквозь стены</option>
                    <option value="3" <?php if(!empty($values['abilities'][2])) {if ($values['abilities'][0]=='3') {print 'selected';}} ?>>Левитация</option>
                </select></label>
            </div>
            <div class="group">
                <label for="bio">Биография:
                <textarea name="bio" id="bio" rows="3" class="form-control <?php if ($errors['bio']) {print 'error';} ?>"><?php print $values['bio']; ?></textarea></label>
            </div>
            <label><input type="checkbox" class="form-check-input <?php if ($errors['checkbox']) {print 'error';} ?>" id="checkbox" value="1" name="checkbox" <?php if ($values['checkbox']=='1') {print 'checked';} ?>>
                C контрактом ознакомлен(а) </label><br>
            <input type="submit" id="end" class="btn btn-primary" value="Отправить">
        </form>
  </div>
</body> 
</html>

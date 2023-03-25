<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>3</title>
</head>
<body>
  <div class="col col-10 col-md-6" id="forma">
        <form id="form" action="" method="POST">
            <div class="group">
                <label for="name">Имя:</label>
                <input name="fio" id="name" placeholder="Введите ваше имя">
            </div>
            <div class="group">
                <label for="email">E-mail:</label>

                <input name="email" type="email" id="email" placeholder="Введите вашу почту">

            </div>
            <div class="group">

                Дата рождения:
                <input name="date" type="date" value="<?php echo date('Y-m-d'); ?>" />

            </div>
            <div class="group">
                Пол:
                <label for="g1"><input type="radio" name="sex" id="g1" value="1">
                    Мужской</label>
                <label for="g2"><input type="radio" name="sex" id="g2" value="2">
                    Женский</label>
            </div>
            <div class="group">
                Количество конечностей:
                <label for="l1"><input type="radio" name="limbs" id="l1" value="1">
                    1</label>
                    <label for="l2"><input type="radio" name="limbs" id="l2" value="2">
                    2</label>
                <label for="l3"><input type="radio" name="limbs" id="l3" value="3">
                    3</label>
                <label for="l4"><input type="radio" name="limbs" id="l4" value="4">
                    4</label>

            </div>
            <div class="group">
                <label for="powers">Сверхспособности:</label>
                <select name="abilities[]" id="powers" multiple="multiple">
                    <option value="1">Бессмертие</option>
                    <option value="2">Прохождение сквозь стены</option>
                    <option value="3">Левитация</option>
                </select>
            </div>


            <div class="group">
                <label for="bio">Биография:</label>
                <textarea name="bio" id="bio" rows="3"></textarea>
            </div>
            <label><input type="checkbox" id="checkbox" value="1" name="checkbox">
                C контрактом ознакомлен(а) </label><br>
            <input type="submit" id="end" value="Отправить">
        </form>
  </div>
</body> 
</html>

<?php

include('auth.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT application_id, fio, email, date, sex, limbs, bio FROM application");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $messages = array();

    $errors = array();
    $errors['error_id'] = empty($_COOKIE['error_id']) ? '' : $_COOKIE['error_id'];
    $errors['fio'] = !empty($_COOKIE['fio_error']);
    $errors['email1'] = !empty($_COOKIE['email_error1']);
    $errors['email2'] = !empty($_COOKIE['email_error2']);
    $errors['date'] = !empty($_COOKIE['date_error']);
    $errors['sex'] = !empty($_COOKIE['sex_error']);
    $errors['limbs'] = !empty($_COOKIE['limbs_error']);
    $errors['abilities1'] = !empty($_COOKIE['abilities_error']);
    $errors['bio1'] = !empty($_COOKIE['bio_error1']);
    $errors['bio2'] = !empty($_COOKIE['bio_error2']);
  
    if (!empty($errors['fio'])) {
        setcookie('name_error', '', 100000);
        $messages['fio'] = '<p class="msg">Заполните имя</p>';
    }
    if (!empty($errors['email1'])) {
        setcookie('email_error1', '', 100000);
        $messages['email1'] = '<p class="msg">Заполните email</p>';
    } else if (!empty($errors['email2'])) {
        setcookie('email_error2', '', 100000);
        $messages['email2'] = '<p class="msg">Некорректно заполнено поле email</p>';
    }
    if (!empty($errors['date'])) {
        setcookie('date_error', '', 100000);
        $messages['date'] = '<p class="msg">Неправильный формат ввода года</p>';
    }
    if (!empty($errors['sex'])) {
        setcookie('sex_error', '', 100000);
        $messages['sex'] = '<p class="msg">Не выбран пол</p>';
    }
    if (!empty($errors['limbs'])) {
        setcookie('limbs_error', '', 100000);
        $messages['limbs'] = '<p class="msg">Не выбрана конечность</p>';
    }
    if (!empty($errors['abilities'])) {
        setcookie('abilities_error', '', 100000);
        $messages['abilities'] = '<p class="msg">Не выбрана ни одна сверхспособность</p>';
    }
    if (!empty($errors['bio1'])) {
        setcookie('bio_error1', '', 100000);
        $messages['bio1'] = '<p class="msg">Не заполнено поле биографии</p>';
    } else if (!empty($errors['bio2'])) {
        setcookie('bio_error2', '', 100000);
        $messages['bio2'] = '<p class="msg">Недопустимый формат ввода биографии</p>';
    }
    include('form.php');
    exit();
} else {
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)$/', $key, $matches)) {
            $app_id = $matches[1];
            setcookie('clear', $app_id, time() + 24 * 60 * 60);
            $stmt = $db->prepare("DELETE FROM application WHERE application_id = ?");
            $stmt->execute([$app_id]);
            $stmt = $db->prepare("DELETE FROM abilities WHERE application_id = ?");
            $stmt->execute([$app_id]);
            $stmt = $db->prepare("DELETE FROM users WHERE application_id = ?");
            $stmt->execute([$app_id]);
        }
        if (preg_match('/^save(\d+)$/', $key, $matches)) {
            $app_id = $matches[1];
            $dates = array();
            $dates['fio'] = $_POST['fio' . $app_id];
            $dates['email'] = $_POST['email' . $app_id];
            $dates['date'] = $_POST['date' . $app_id];
            $dates['sex'] = $_POST['sex' . $app_id];
            $dates['limbs'] = $_POST['limbs' . $app_id];
            $abilities = $_POST['abilities' . $app_id];
            $filtred_abilities = array_filter($abilities, function($value) {return($value == 1 || $value == 2 || $value == 3);});
            $dates['bio'] = $_POST['bio' . $app_id];
        
            $fio = $dates['fio'];
            $email = $dates['email'];
            $date = $dates['date'];
            $sex = $dates['sex'];
            $limbs = $dates['limbs'];
            $bio = $dates['bio'];
        
            if (empty($fio)) {
                setcookie('fio_error', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            }
            if (empty($email)) {
                setcookie('email_error1', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                setcookie('email_error2', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } 
            if (!is_numeric($date)) {
                setcookie('date_error', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } 
            if (empty($sex)) {
                setcookie('sex_error', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } 
            if (empty($limbs)) {
                setcookie('limbs_error', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } 
            if (empty($abilities)) {
                setcookie('abilities_error', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
              } 
            if (empty($bio)) {
                setcookie('bio_error1', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } else if (!preg_match('/^[\p{Cyrillic}\d\s,.!?-]+$/u', $bio)) {
                setcookie('bio_error2', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } 
        
            if ($errors) {
                setcookie('error_id', $app_id, time() + 24 * 60 * 60);
                header('Location: index.php');
                exit();
            } else {
                setcookie('fio_error', '', 100000);
                setcookie('email_error1', '', 100000);
                setcookie('email_error2', '', 100000);
                setcookie('date_error', '', 100000);
                setcookie('sex_error', '', 100000);
                setcookie('limbs_error', '', 100000);
                setcookie('abilities_error', '', 100000);
                setcookie('bio_error1', '', 100000);
                setcookie('bio_error2', '', 100000);
                setcookie('error_id', '', 100000);
            }
            $stmt = $db->prepare("SELECT fio, email, date, sex, limbs, bio FROM application WHERE application_id = ?");
            $stmt->execute([$app_id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT superpower_id FROM abilities WHERE application_id = ?");
            $stmt->execute([$app_id]);
            $old_abilities = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (array_diff($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE application SET fio = ?, email = ?, date = ?, sex = ?, limbs = ?, bio = ? WHERE application_id = ?");
                $stmt->execute([$dates['fio'], $dates['email'], $dates['date'], $dates['sex'], $dates['limbs'], $dates['bio'], $app_id]);
            }
            if (array_diff($abilities, $old_abilities) || count($abilities) != count($old_abilities)) {
                $stmt = $db->prepare("DELETE FROM abilities WHERE application_id = ?");
                $stmt->execute([$app_id]);
                $stmt = $db->prepare("INSERT INTO abilities (application_id, superpower_id) VALUES (?, ?)");
                foreach ($abilities as $superpower_id) {
                    $stmt->execute([$app_id, $superpower_id]);
                }
            }
        }
    }
    header('Location: index.php');
}

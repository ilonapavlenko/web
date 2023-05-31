<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, name FROM Region");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $new = array();
    $new['name'] = empty($_COOKIE['name']) ? '' : $_COOKIE['name'];
    include('assets/Region.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {
        if (empty($_POST['name'])) {
            $errors['name1'] = 'Заполните поле "Название региона"';
            setcookie('name', '', time() + 24 * 60 * 60);
        } else if (!preg_match('/^[\p{L}\p{N}]+$/u', $_POST['name'])) {
            $errors['name2'] = 'Некорректно заполнено поле "Название региона"';
            setcookie('name', $_POST['name'], time() + 24 * 60 * 60);
        } else {
            $name = $_POST['name'];
            $stmt = $db->prepare("INSERT INTO Region (name) VALUES (?)");
            $stmt->execute([$name]);
            $messages['added'] = 'Регион "'.$name.'" успешно добавлен';
            setcookie('name', '', time() + 24 * 60 * 60);
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("(SELECT id FROM Journal WHERE RegionId = ?)");
            $stmt->execute([$id, $id]);
            $empty = $stmt->rowCount() === 0;
            if (!$empty) {
                $errors['delete'] = 'Поле с <b>id = '.$id.'</b> невозможно удалить, т.к. оно связанно с журналом учета';
            } else {
                $stmt = $db->prepare("DELETE FROM Region WHERE id = ?");
                $stmt->execute([$id]);
                $messages['deleted'] = 'Регион с <b>id = '.$id.'</b> успешно удалён';
            }
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT name FROM Region WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['name'] = $_POST['name' . $id];

            if (array_diff($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE Region SET name = ? WHERE id = ?");
                $stmt->execute([$dates['name'], $id]);
                $messages['edited'] = 'Регион с <b>id = '.$id.'</b> успешно обновлён';
            }
        }
    }
    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: Region.php');
}

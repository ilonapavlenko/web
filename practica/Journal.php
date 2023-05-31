<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, RegionID, CompanyId, date FROM Journal");
            $stmt->execute();
            $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $new = array();
    $new['RegionID'] = empty($_COOKIE['RegionID']) ? '' : $_COOKIE['RegionID'];
    $new['CompanyId'] = empty($_COOKIE['CompanyId']) ? '' : $_COOKIE['CompanyId'];
    $new['date'] = empty($_COOKIE['date']) ? '' : $_COOKIE['date'];
    include('assets/Journal.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {

        if (empty($_POST['RegionID'])) {
            $errors['RegionID'] = 'Заполните поле "RegionID"';
        } else {
            setcookie('RegionID', $_POST['RegionID'], time() + 24 * 60 * 60);
        }

        if (empty($_POST['CompanyId'])) {
            $errors['CompanyId'] = 'Заполните поле "CompanyId"';
        } else {
            setcookie('CompanyId', $_POST['CompanyId'], time() + 24 * 60 * 60);
        }

        if (empty($_POST['date'])) {
            $errors['date'] = 'Заполните поле "date"';
        } else {
            setcookie('date', $_POST['date'], time() + 24 * 60 * 60);
        }

        if (empty($errors)) {
            $RegionID = $_POST['RegionID'];
            $CompanyId = $_POST['CompanyId'];
            $date = $_POST['date'];

            $stmt = $db->prepare("INSERT INTO Journal (RegionID, CompanyId, date) 
                VALUES (?, ?, ?)");
            $stmt->execute([$RegionID, $CompanyId, $date]);
            $messages['added'] = 'Данные успешно добавлены';
            setcookie('RegionID', '', time() + 24 * 60 * 60);
            setcookie('CompanyId', '', time() + 24 * 60 * 60);
            setcookie('date', '', time() + 24 * 60 * 60);
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("DELETE FROM Journal WHERE id = ?");
            $stmt->execute([$id]);
            $messages['deleted'] = 'Запись с <b>id = '.$id.'</b> успешно удалена';
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT RegionID, CompanyId, date FROM Journal WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['RegionID'] = $_POST['RegionID' . $id];
            $dates['CompanyId'] = $_POST['CompanyId' . $id];
            $dates['date'] = $_POST['date' . $id];

            if (array_diff_assoc($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE Journal SET RegionID = ?, CompanyId = ?, date = ? WHERE id = ?");
                $stmt->execute([$dates['RegionID'], $dates['CompanyId'], $dates['date'], $id]);
                $messages['edited'] = 'Запись с <b>id = '.$id.'</b> успешно обновлена';
            }
        }
    }
    
    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: Journal.php');
}

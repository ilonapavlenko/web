<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, name, revenue, city FROM Indicators");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT id, name, revenue, city FROM Indicators");
        $params = [];

        if (!empty($_COOKIE['revenues'])) {
            $filter_revenue_ids = unserialize($_COOKIE['revenues']);
            $in_values1 = implode(',', array_fill(0, count($filter_revenue_ids), '?'));
            $stmt_sql = isset($stmt_sql) ? $stmt_sql." AND revenue IN ($in_values1)" : "revenue IN ($in_values1)";
            $params = array_merge($params, $filter_revenue_ids);
        }

        if (!empty($_COOKIE['cities'])) {
            $filter_city_ids = unserialize($_COOKIE['cities']);
            $in_values2 = implode(',', array_fill(0, count($filter_city_ids), '?'));
            $stmt_sql = isset($stmt_sql) ? $stmt_sql." AND city IN ($in_values2)" : "city IN ($in_values2)";
            $params = array_merge($params, $filter_city_ids);
        }

        if (isset($stmt_sql)) {
            $stmt_sql = "SELECT id, name, revenue, city FROM Indicators WHERE ".$stmt_sql;
            $stmt = $db->prepare($stmt_sql);
            $stmt->execute($params);
            $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt->execute();
            $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT revenue FROM Indicators");
            $stmt->execute();
            $a_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filter_revenue_ids = [];
            foreach ($a_ids as $a_id) {
                $filter_revenue_ids[] = $a_id['revenue'];
            }

            $stmt = $db->prepare("SELECT city FROM Indicators");
            $stmt->execute();
            $c_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filter_city_ids = [];
            foreach ($c_ids as $c_id) {
                $filter_city_ids[] = $c_id['city'];
            }
        }
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    

    $new = array();
    $new['name'] = empty($_COOKIE['name']) ? '' : $_COOKIE['name'];
    $new['revenue'] = empty($_COOKIE['revenue']) ? '' : $_COOKIE['revenue'];
    $new['city'] = empty($_COOKIE['city']) ? '' : $_COOKIE['city'];
    include('assets/Indicators.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {
        if (empty($_POST['name'])) {
            $errors['name1'] = 'Заполните поле "Название предприятия"';
            setcookie('name', '', time() + 24 * 60 * 60);
        } else if (!preg_match('/^[\p{L}\p{M}\s.]+$/u', $_POST['name'])) {
            $errors['name2'] = 'Некорректно заполнено поле "Название предприятия"';
            setcookie('name', $_POST['name'], time() + 24 * 60 * 60);
        } else {
            setcookie('name', $_POST['name'], time() + 24 * 60 * 60);
        }

        if (empty($_POST['revenue'])) {
            $errors['revenue1'] = 'Заполните поле "Прибыль"';
            setcookie('revenue', '', time() + 24 * 60 * 60);
        } else if (!is_numeric($_POST['revenue'])) {
            $errors['revenue2'] = 'Некорректно заполнено поле "Прибыль"';
            setcookie('revenue', $_POST['revenue'], time() + 24 * 60 * 60);
        } else {
            setcookie('revenue', $_POST['revenue'], time() + 24 * 60 * 60);
        }

        if (empty($_POST['city'])) {
            $errors['city1'] = 'Заполните поле "Город"';
            setcookie('city', '', time() + 24 * 60 * 60);
        } else if (!preg_match('/^[\p{L}\p{M}\s.]+$/u', $_POST['city'])) {
            $errors['city2'] = 'Некорректно заполнено поле "Город"';
            setcookie('city', $_POST['city'], time() + 24 * 60 * 60);
        } else {
            setcookie('city', $_POST['city'], time() + 24 * 60 * 60);
        }
        
        if (empty($errors)) {
            $name = $_POST['name'];
            $revenue = intval($_POST['revenue']);
            $city = $_POST['city'];
            $stmt = $db->prepare("INSERT INTO Indicators (name, revenue, city) VALUES (?, ?, ?)");
            $stmt->execute([$name, $revenue, $city]);
            $messages['added'] = 'Показатель "'.$name.'" успешно добавлен';
            setcookie('name', '', time() + 24 * 60 * 60);
            setcookie('revenue', '', time() + 24 * 60 * 60);
            setcookie('city', '', time() + 24 * 60 * 60);
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("SELECT id FROM Journal WHERE IndicatorsID = ?");
            $stmt->execute([$id]);
            $empty = $stmt->rowCount() === 0;
            if (!$empty) {
                $errors['delete'] = 'Поле с <b>id = '.$id.'</b> невозможно удалить, т.к. оно связанно с журналом учета';
            } else {
                $stmt = $db->prepare("DELETE FROM Indicators WHERE id = ?");
                $stmt->execute([$id]);
                $messages['deleted'] = 'Показатель с <b>id = '.$id.'</b> успешно удалён';
            }
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT name, revenue, city FROM Indicators WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['name'] = $_POST['name' . $id];
            $dates['revenue'] = $_POST['revenue' . $id];
            $dates['city'] = $_POST['city' . $id];

            if (array_diff_assoc($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE Indicators SET name = ?, revenue = ?, city = ? WHERE id = ?");
                $stmt->execute([$dates['name'], $dates['revenue'], $dates['city'], $id]);
                $messages['edited'] = 'Показатель с <b>id = '.$id.'</b> успешно обновлён';
            }
        }
    }

    if (!empty($_POST['resetall'])) {
        setcookie('revenues', '');
        setcookie('cities', '');
    }

    if (!empty($_POST['filter'])) {

        $filter_revenue_ids = array();
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'filter_revenue_') !== false) {
                $id = substr($key, 11);
                $filter_revenue_ids[] = $id;
            }
        }
        setcookie('revenues', serialize($filter_revenue_ids));

        $filter_city_ids = array();
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'filter_city_') !== false) {
                $id = substr($key, 15);
                $filter_city_ids[] = $id;
            }
        }
        setcookie('cities', serialize($filter_city_ids));
        
    }

    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: Indicators.php');
}

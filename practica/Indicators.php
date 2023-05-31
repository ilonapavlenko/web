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

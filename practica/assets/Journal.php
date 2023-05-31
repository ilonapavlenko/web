<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="stylesheet" href="style/style.css">
    <link type="image/x-icon" href="logo.png" rel="shortcut icon">
    <link type="Image/x-icon" href="logo.png" rel="icon">
    <title>Holding</title>
    
</head>
<body>
    <header>
        <div class="header-items">
            <a href="index.php" class="logo">
                <img src="logo.png" alt="logo" width="100" height="80">
                <h1>Холдинг</h1>
            </a>
            <nav>
                <ul>
                    <li><a href="Region.php">Список регионов</a></li>
                    <li><a href="Company.php">Список предприятий</a></li>
                    <li><a href="Indicators.php">Список показателей</a></li>
                    <li><a class="active" href="#">Журнал учета отчетных данных</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <?php
            if (!empty($_COOKIE['messages'])) {
                echo '<div class="messages">';
                $messages = unserialize($_COOKIE['messages']);
                foreach ($messages as $message) {
                    echo $message . '</br>';
                }
                echo '</div>';
                setcookie('messages', '', time() + 24 * 60 * 60);
            }
            if (!empty($_COOKIE['errors'])) {
                echo '<div class="errors">';
                $errors = unserialize($_COOKIE['errors']);
                foreach ($errors as $error) {
                    echo $error . '</br>';
                }
                echo '</div>';
                setcookie('errors', '', time() + 24 * 60 * 60);
            }
        ?>
        <form action="" method="POST">
            <div class="main-content">
                <h2>Журнал учета отчетных данных</h2>
            </div>
            <div class="main-content">
                <div class="top-table">
                    <div class="newdates">
                        <div class="newdates-item">
                            <label for="RegionID">Название региона</label>
                        </div>
                        <div class="newdates-item">
                            <select name="RegionID">
                                <?php
                                $stmt = $db->prepare("SELECT id, name FROM Region");
                                $stmt->execute();
                                $Region = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                print("<option selected disabled>выберите регион</option>");
                                foreach ($Region as $region) {
                                    if (!empty($new['RegionID']) && ($new['RegionID'] ==  $region['id'])) {
                                        printf('<option selected value="%d">%d. %s</option>', $region['id'], $region['id'], $region['name']);
                                    } else {
                                        printf('<option value="%d">%d. %s</option>', $region['id'], $region['id'], $region['name']);
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="newdates-item">
                            <label for="CompanyId">Предприятие</label>
                        </div>
                        <div class="newdates-item">
                            <select name="CompanyId">
                                <?php
                                $stmt = $db->prepare("SELECT id, name FROM Company");
                                $stmt->execute();
                                $Company = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                print("<option selected disabled>выберите предприятие</option>");
                                foreach ($Company as $company) {
                                    if (!empty($new['CompanyId']) && ($new['CompanyId'] ==  $company['id'])) {
                                        printf('<option selected value="%d">%d. %s</option>', $company['id'], $company['id'], $company['name']);
                                    } else {
                                        printf('<option value="%d">%d. %s</option>', $company['id'], $company['id'], $company['name']);
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="newdates-item">
                            <label for="date">Дата подписания журнала</label>
                        </div>
                        <div class="newdates-item">
                            <input type="date" name="date" value=<?php print($new['date']); ?>>
                        </div>
                        <div class="newdates-item">
                            <input type="submit" name="addnewdate" value="Добавить">
                        </div>
                    </div>
                        

                </div>
            </div>
            <div class="main-content">
            <?php
                echo    '<table>
                            <tr>
                                <th>Название региона</th>
                                <th>Предприятие</th>
                                <th>Дата проведения</th>
                                <th colspan=2>
                                    
                                </th>
                            <tr>';
                foreach ($values as $value) {
                    echo    '<tr>';
                    echo        '<td>';
                                    $stmt = $db->prepare("SELECT id, name FROM Region");
                                    $stmt->execute();
                                    $Region = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo            '<select'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                    else print(" "); echo 'name="RegionID'.$value['id'].'">';
                                        foreach ($Region as $region) {
                                            if ($region['id'] == $value['RegionID']) {
                                                printf('<option selected value="%d">%d. %s</option>', $region['id'], $region['id'], $region['name']);
                                            } else {
                                                printf('<option value="%d">%d. %s</option>', $region['id'], $region['id'], $region['name']);
                                            }
                                        }
                    echo            '</select>';
                    echo        '</td>';

                    echo        '<td>';
                                    $stmt = $db->prepare("SELECT id, name FROM Company");
                                    $stmt->execute();
                                    $Company = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo            '<select'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                    else print(" "); echo 'name="CompanyId'.$value['id'].'">';
                                        foreach ($Company as $company) {
                                            if ($company['id'] == $value['CompanyId']) {
                                                printf('<option selected value="%d">%d. %s</option>', $company['id'], $company['id'], $company['name']);
                                            } else {
                                                printf('<option value="%d">%d. %s</option>', $company['id'], $company['id'], $company['name']);
                                            }
                                        }
                    echo            '</select>';
                    echo        '</td>';


                    echo        '<td> <input'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                                                else print(" "); echo 'type="date" name="date'.$value['id'].'" value="'.$value['date'].'"> 
                                </td>';

                if (empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) {
                    echo        '<td> <input name="edit'.$value['id'].'" type="image" src="edit.png" width="20" height="20" alt="submit"/> </td>';
                    echo        '<td> <input name="clear'.$value['id'].'" type="image" src="clear.png" width="20" height="20" alt="submit"/> </td>';
                } else {
                    echo        '<td colspan=2> <input name="save'.$value['id'].'" type="image" src="save.png" width="20" height="20" alt="submit"/> </td>';
                }
                    echo    '</tr>';
                }
                echo '</table>';
            ?>
            </div>
        </form>
    </main>
</body>
</html>

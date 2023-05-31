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
                    <li><a class="active" href="#">Список регионов</a></li>
                    <li><a href="Company.php">Список предприятий</a></li>
                    <li><a href="Indicators.php">Список показателей</a></li>
                    <li><a href="Journal.php">Журнал учета отчетных данных</a></li>
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
                <h2>Список регионов</h2>
            </div>
            <div class="main-content">
                <div class="newdates">
                    <div class="newdates-item">
                        <label for="name">Регион:</label>
                    </div>
                    <div class="newdates-item">
                        <input name="name" value="<?php print($new['name']); ?>" placeholder="Название региона">
                    </div>
                    <div class="newdates-item">
                        <input type="submit" name="addnewdate" value="Добавить">
                    </div>
                </div>
            </div>
            <div class="main-content">
            <?php
                echo    '<table>
                            <tr>
                                <th>id</th>
                                <th>Название региона</th>
                                <th colspan=2>&nbsp;</th>
                            <tr>';
                foreach ($values as $value) {
                    echo    '<tr>';
                    echo        '<td>'; print($value['id']); echo '</td>';
                    echo        '<td>
                                    <input'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                                    else print(" "); echo 'name="name'.$value['id'].'" value="'.$value['name'].'">
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

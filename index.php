<?php

function prepareData($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function checkDatabaseIsEmpty($pdo) {
    $stmt = $pdo->query('SELECT * FROM person');
    if ($stmt->rowCount() == 0) {
        return true;
    }
}

try {

    $pdo = new PDO('mysql:host=localhost;dbname=phonebook;charset=utf8',
        'phonebook_user',
        'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Инициализация переменных
    $prompt = '';
    $form = '';
    $contacts = '';
    $selectedPersonId = '';
    $cityOptions = '<option value = "">Выберите город...</option>';

    $stmt = $pdo->query('SELECT id, name FROM city ORDER BY name');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $name = prepareData($row['name']);
        $cityOptions .= '<option value = "' . $id . '">' . $name . '</option>';
    }

    $streetOptions = '<option value="">Выберите улицу...</option>';

    // Запрос на добавление контакта
    if (isset($_POST['add-id'])) {

        $lastname = prepareData($_POST['lastname']);
        $firstname = prepareData($_POST['firstname']);
        $patronymic = prepareData($_POST['patronymic']);
        $birthdate = prepareData($_POST['birthdate']);
        $cityId = $_POST['city-id'];
        $streetId = $_POST['street-id'];
        $tel = prepareData($_POST['tel']);

        $stmt = $pdo->prepare("INSERT INTO person SET
            last_name = :last_name,
            first_name = :first_name,
            patronymic = :patronymic,
            birthdate = :birthdate,
            city_id = :city_id,
            street_id = :street_id,
            tel = :tel");
        $stmt->execute(array(':last_name' => $lastname,
            ':first_name' => $firstname,
            ':patronymic' => $patronymic,
            ':birthdate' => $birthdate,
            ':city_id' => $cityId,
            ':street_id' => $streetId,
            ':tel' => $tel));

        header('Location: .');
        exit();
    }

    // Запрос на удаление контакта
    elseif (isset($_POST['delete-id'])) {

        $deleteId = $_POST['delete-id'];

        $stmt = $pdo->prepare("DELETE FROM person WHERE id = :delete_id");
        $stmt->bindParam(':delete_id', $deleteId);
        $stmt->execute();

        // Если удаляется последний контакт, сбрасываем счетчик person.id
        if (checkDatabaseIsEmpty($pdo)) {
            $pdo->exec('TRUNCATE TABLE person');
        }

        header('Location: .');
        exit();
    }

    // Запрос на подтверждение изменения контакта
    elseif (isset($_POST['confirm-update-id'])) {

        $lastname = prepareData($_POST['lastname']);
        $firstname = prepareData($_POST['firstname']);
        $patronymic = prepareData($_POST['patronymic']);
        $birthdate = prepareData($_POST['birthdate']);
        $cityId = $_POST['city-id'];
        $streetId = $_POST['street-id'];
        $tel = prepareData($_POST['tel']);
        $confirmUpdateId = prepareData($_POST['confirm-update-id']);

        $stmt = $pdo->prepare("UPDATE person SET
            last_name = :last_name,
            first_name = :first_name,
            patronymic = :patronymic,
            birthdate = :birthdate,
            city_id = :city_id,
            street_id = :street_id,
            tel = :tel
            WHERE id = :confirm_update_id");
        $stmt->execute(array(':last_name' => $lastname,
            ':first_name' => $firstname,
            ':patronymic' => $patronymic,
            ':birthdate' => $birthdate,
            ':city_id' => $cityId,
            ':street_id' => $streetId,
            ':tel' => $tel,
            ':confirm_update_id' => $confirmUpdateId));

        header('Location: .');
        exit();
    }


    // Запрос на именение контакта
    elseif (isset($_POST['update-id'])) {

        $selectedPersonId = $_POST['update-id'];

        $stmt = $pdo->prepare("SELECT
            last_name,
            first_name,
            patronymic,
            birthdate,
            person.city_id AS city_id,
            street_id,
            tel
            FROM person, city, street
            WHERE person.city_id = city.id
            AND street_id = street.id
            AND person.id = :selected_person_id");
        $stmt->bindParam(':selected_person_id', $selectedPersonId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastName = prepareData($row['last_name']);
        $firstName = prepareData($row['first_name']);
        $patronymic = prepareData($row['patronymic']);
        $birthdate = prepareData($row['birthdate']);
        $cityId = $row['city_id'];
        $selectedStreetId = $row['street_id'];
        $tel = prepareData($row['tel']);

        $cityOptions = '<option value = "">Выберите город...</option>';

        $stmt = $pdo->query('SELECT id, name FROM city ORDER BY name');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $name = prepareData($row['name']);
            $cityOptions .= '<option value="' . $id . '"';
            if ($id == $cityId) {
                $cityOptions .= ' selected>';
            } else {
                $cityOptions .= '>';
            }
            $cityOptions .= $name . '</option>';
        }

        $stmt = $pdo->prepare("SELECT DISTINCT street.id AS street_id, street.name AS street_name
            FROM street, city
            WHERE city_id = :city_id
            ORDER BY street_name");
        $stmt->bindParam(':city_id', $cityId);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $streetId = $row['street_id'];
            $streetName = prepareData($row['street_name']);
            $streetOptions .= '<option value="' . $streetId . '"';
            if ($streetId == $selectedStreetId) {
                $streetOptions .= ' selected>';
            } else {
                $streetOptions .= '>';
            }
            $streetOptions .= $streetName . '</option>';
        }

        $form = '<tr>
        <td>
            <input type="text" name="lastname" id="form-add-lastname" maxlength="45" required value="' . $lastName . '">
        </td>
        <td>
            <input type="text" name="firstname" id="form-add-firstname" maxlength="45" required value="' . $firstName . '">
        </td>
        <td>
            <input type="text" name="patronymic" id="form-add-patronymic" maxlength="45" required value="' . $patronymic . '">
        </td>
        <td>
            <input type="date" name="birthdate" id="form-add-birthdate" placeholder="гггг-мм-дд" required value="' . $birthdate . '">
        </td>
        <td>
            <select name="city-id" id="form-add-city" required>' . $cityOptions . '</select>
        </td>
        <td>
            <select name="street-id" id="form-add-street" required>' . $streetOptions . '</select>
        </td>
        <td>
            <input type="tel" name="tel" id="form-add-tel" maxlength="45" required value="' . $tel . '">
        </td>
        <td>
            <button type="submit" name="confirm-update-id" required value="' . $selectedPersonId . '">Изменить</button>
        </td>
        </tr>';

    } else {

        // GET запрос

        If (checkDatabaseIsEmpty($pdo)) {
            $prompt = '<p>Похоже, Ваша телефонная книга пуста...<br>' .
            'Но вы легко можете <button id="add-first-contact">добавить контакт</button></p>';
        }

        // Собираем форму для добавления контакта
        $form = '<tr>
        <td>
            <input type="text" name="lastname" id="form-add-lastname" maxlength="45" required>
        </td>
        <td>
            <input type="text" name="firstname" id="form-add-firstname" maxlength="45" required>
        </td>
        <td>
            <input type="text" name="patronymic" id="form-add-patronymic" maxlength="45" required>
        </td>
        <td>
            <input type="date" name="birthdate" id="form-add-birthdate" placeholder="гггг-мм-дд" required>
        </td>
        <td>
            <select name="city-id" id="form-add-city" required>' . $cityOptions . '</select>
        </td>
        <td>
            <select name="street-id" id="form-add-street" disabled required>' . $streetOptions . '</select>
        </td>
        <td>
            <input type="tel" name="tel" id="form-add-tel" maxlength="45" required>
        </td>
        <td>
            <input type="submit" name ="add-id" value="Добавить" required>
        </td>
        </tr>';

}

    // Выгрузка данных из базы для таблицы контактов
    $stmt = $pdo->prepare("SELECT
        person.id AS person_id,
        last_name,
        first_name,
        patronymic,
        DATE_FORMAT(birthdate, '%e.%m.%Y') AS birthdate,
        city.name AS city,
        street.name AS street,
        tel
        FROM person, city, street
        WHERE person.city_id = city.id
        AND person.street_id = street.id
        AND person.id != :selected_person_id
        ORDER BY last_name, first_name, patronymic");
    $stmt->bindParam(':selected_person_id', $selectedPersonId);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $person_id = $row['person_id'];
        $lastName = prepareData($row['last_name']);
        $firstName = prepareData($row['first_name']);
        $patronymic = prepareData($row['patronymic']);
        $birthdate = prepareData($row['birthdate']);
        $city = prepareData($row['city']);
        $street = prepareData($row['street']);
        $tel = prepareData($row['tel']);

        $contacts .= '<tr id="' . $person_id . '">
        <td>' . $lastName . '</td>
        <td>' . $firstName . '</td>
        <td>' . $patronymic . '</td>
        <td>' . $birthdate . '</td>
        <td>' . $city . '</td>
        <td>' . $street . '</td>
        <td>' . $tel . '</td>
        <td>
            <button type="submit" name="update-id" value="' . $person_id . '">Изменить</button>
            <button type="submit" name="delete-id" value="' . $person_id . '">Удалить</button>
        </td>
        </tr>';
    }

    include 'phonebook.php';
}

catch (PDOException $e) {

    $error = $e->getMessage();
    echo "<p>Ошибка при обращении к базе данных:<br>$error</p>";
    exit();
}

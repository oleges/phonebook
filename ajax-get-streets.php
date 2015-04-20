<?php

try {

    $pdo = new PDO('mysql:host=localhost;dbname=phonebook;charset=utf8', 'phonebook_user', 'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $cityId = $_POST['city_id'];
    $streetOptions = '<option value="">Выберите улицу...</option>';

    $stmt = $pdo->prepare("SELECT DISTINCT street.id AS street_id, street.name AS street_name
        FROM street, city
        WHERE street.city_id = :city_id
        ORDER BY street.name");
    $stmt->bindParam(':city_id', $cityId);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $streetId = $row['street_id'];
        $streetName = $row['street_name'];
        $streetOptions .= '<option value = "' . $streetId . '">' . $streetName . '</option>';
    }

    echo $streetOptions;
}

catch (PDOException $e) {

    echo '';
}

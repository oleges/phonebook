<!DOCTYPE html>
<html land="ru">
  <head>
    <meta charset="utf-8">
    <title>Телефонная книга</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
  </head>
  <body>
    <header>
      <h1>Телефонная книга</h1>
    </header>
    <div id="prompt">
      <?php echo $prompt ?>
    </div>
    <div id="book">
      <form action="" method="post" id="form-add">
        <table>
          <thead>
            <tr>
              <th>
                <label for="form-add-lastname">Фамилия</label>
              </th>
              <th>
                <label for="form-add-firstname">Имя</label>
              </th>
              <th>
                <label for="form-add-patronymic">Отчество</label>
              </th>
              <th>
                <label for="form-add-birthdate">Дата рождения</label>
              </th>
              <th>
                <label for="form-add-city">Город</label>
              </th>
              <th>
                <label for="form-add-street">Улица</label>
              </th>
              <th>
                <label for="form-add-tel">Телефон</label>
              </th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php echo $form ?>
          </tbody>
        </table>
      </form>
      <form action="" method="post" id="form-update-delete">
        <table>
          <?php echo $contacts ?>
        </table>
      </form>
    </div>
  </body>
</html>

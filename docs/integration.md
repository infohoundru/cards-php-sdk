## Интеграция с сервисом

### 1. Регистрация нового пользователя
Для начала вам необходимо зарегистрировать нового пользователя. Для этого перейдите по ссылке http://cards.infohound.ru/register и следуйте инструкциям.

### 2. Получение токена для доступа к API
Для этого перейдите по ссылке http://cards.infohound.ru/oauth и нажмите "Создать новый ключ", затем введите название ключа и нажмите "Создать". На экране появится ваш персональный токен доступа к API нашего сервиса. Токен будет показан вам **всего один раз**. При утере токена необходимо создать новый.

### 3. Встраивание формы для загрузки фотографий карт
На стороне вашего сервера необходимо встроить форму загрузки фотографий карт. Для этого в html файл необходимо добавить:
```
<head>
    ...
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="http://cards.infohound.ru/js/for_web_form/combineForm.js"></script>
</head>

```
Где первый тэг \<script\> это библиотека jQuery, а второй тэг \<script\> - библиотека нашего сервиса, необходимая для работы формы загрузки.

Сама форма добавляется следующим образом:
```
<body>
    ...
    <div id="icards-form" data-color="" data-api_url="" data-path_to_php="ihcards.php"></div>
    ...
</body>
```
В **data-path_to_php** необходимо указать путь к файлу, который отправит данные из формы на наш сервис. Пример такого файла можно посмотреть здесь [ihcards.php](https://github.com/infohoundru/cards-php-sdk/blob/master/examples/ihcards.php).
В переменную **$access_token** в ihcards.php необходимо подставить ваш персональный токен доступа. К данному файлу необходимо подключить класс [IhCardsImage](https://github.com/infohoundru/cards-php-sdk/blob/master/src/IhCardsImage.php).
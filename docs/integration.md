## Интеграция с сервисом

### 1. Регистрация нового пользователя
Для получениея ссылки на регистрацию вам необходимо обратиться к нашему менеджеру.

### 2. Получение токена для доступа к API
Для этого перейдите по ссылке https://cards.infohound.ru/oauth и нажмите "Создать новый ключ", затем введите название ключа и нажмите "Создать". На экране появится ваш персональный токен доступа к API нашего сервиса. Токен будет показан вам **всего один раз**. При утере токена необходимо создать новый.

### 3. Встраивание формы для загрузки фотографий карт
На стороне вашего сервера необходимо встроить форму загрузки фотографий карт. Для этого в html файл необходимо добавить:
```
<head>
    ...
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cards.infohound.ru/js/for_web_form/combineForm.js"></script>
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

### 4. Разработка вашей собственной формы
Если наша готовая форма загрузки фотографии не подходит, вы можете написать собственную.

#### Порядок вызова методов API
1. /upload/
2. /crop-photo/
3. /apply/
4. /get-result/

#### Читайте также:
1. [Описание методов API](https://github.com/infohoundru/cards-php-sdk/blob/master/docs/api_methods.md)
2. [Готовая реализация отправки данных из формы на API](https://cards.infohound.ru/js/for_web_form/combineForm.js)
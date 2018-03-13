# cards-php-sdk
PHP-SDK с примером интеграции сервиса распознования и верификации банковских карт cards.infohound.ru

## Термины и определения
+ **Сервис** - сервис cards.infohound.ru
+ **Компания** - компания-пользователь Сервиса
+ **Пользователь** - пользователь Компании, загружающий фото в Сервис
+ **Виджет** - JS-виджет, встраиваемые в сайт Компании
+ **API** - API компании infohound.ru

## Поток работ (Workflow)
1. Пользователь загружает через виджет фото на бэкенд Компании, откуда в режиме реального времени фото передается на API-метод `upload`.
2. Пользователю открывается фото-редактор с возможностью обрезать/подогнать под размер
фотографию карты.
3. Пользователь нажимает **Сохранить**, в этот момент на бэкенд Компании отправляется запрос на метод `crop-photo`.
4. Пользователю открывается форма с отображением обрезанной карты и полями для ввода данных с карты (имя владельца, номер карты, срок действия карты).
5. Пользователь вводит данные в поля и нажимает **Подтвердить?**. В ответ возвращается json с ссылкой для просмотра результата.

## Описание методов API
Базовый адрес API `http://api.cards.infohound.ru`. Запросы отправляются по HTTP-протоколу, согласно документации. Для авторизации по протоколу [OAuth2](https://ru.wikipedia.org/wiki/OAuth).
 
### POST /upload 
Загрузить фотографию для обрезки и дальнейшей обработки. 
#### Пример запроса 
```curl -X POST \
    http://api.cards.infohound.ru/upload \
    -H 'authorization: Bearer accesstokenhere' \
    -H 'Accept: application/json' \
    -F file=@\\path\to\card.jpg``
```
Где `accesstokenhere` - ваш токен, полученный в личном кабинете.

#### Ответ
```
   {  
      "ok":"1",
      "info":"http:\/\/api.cards.infohound.ru\/get-photo\/dc376921-e455-47e0-9579-1859f9c8a6d3",
      "width":800,
      "height":600
   }
```
Где `info` - url фото-оригинала, а `width` и `height` - ширина и высота, соответственно.

### POST /crop-photo/
#### Пример запроса 
```curl -X POST \
    http://api.cards.infohound.ru/crop-photo/ \
    -H 'authorization: Bearer accesstokenhere' \
    -H 'Accept: application/json' \
    -F viewPortW=1024
    -F viewPortH=1024
    -F imageSource=http://api.cards.infohound.local/get-photo/dc376921-e455-47e0-9579-1859f9c8a6d3
    -F martrix[0]=1
```
Где `accesstokenhere` - ваш токен, полученный в личном кабинете.
`matrix` - матрица с афинными преобразованиями.
`viewPortH, viewPortW, matrix` - считаются в виджете.

#### Ответ
```{  
      "file":"http:\/\/api.cards.infohound.local\/get-photo\/dc376921-e455-47e0-9579-1859f9c8a6d3\/cropped",
      "edges":[  
         {  
            "count":108,
            "weight":243.692,
            "bad":false,
            "combo":242.612
         },
         {  
            "count":133,
            "weight":110.087,
            "bad":false,
            "combo":108.757
         },
         {  
            "count":456,
            "weight":208.007,
            "bad":false,
            "combo":203.447
         },
         {  
            "count":99,
            "weight":150.339,
            "bad":false,
            "combo":149.349
         }
      ],
      "corners":[  
         {  
            "x":816.559,
            "y":1213.11,
            "bad":false
         },
         {  
            "x":92.9794,
            "y":1225.74,
            "bad":false
         },
         {  
            "x":102.935,
            "y":84.894,
            "bad":false
         },
         {  
            "x":816.559,
            "y":97.3504,
            "bad":false
         }
      ],
      "id":"dc376921-e455-47e0-9579-1859f9c8a6d3"
   }
```
Где `file` - ссылка на обрезанное фото.

### POST /apply
```curl -X POST \
    http://api.cards.infohound.ru/apply/ \
    -H 'authorization: Bearer accesstokenhere' \
    -H 'Accept: application/json' \
    -F id=dc376921-e455-47e0-9579-1859f9c8a6d3
    -F card_holder=IVAN IVANOV
    -F card_number=5213243700000000
    -F card_exp=2021/01
```

Где `id` - `id` заявки из ответа метода crop-photo, `card_holder` - имя на карте, `card_number` - номер карты,
а `card_exp` - дата истечения в формате `ГГГГ/ММ`.

#### Ответ
```
{
    "id": dc376921-e455-47e0-9579-1859f9c8a6d3,
    "status":"ok",
    "url":"http:\/\/api.cards.infohound.local\/get-result?id=dc376921-e455-47e0-9579-1859f9c8a6d3"
}
```
Где `url` - ссылка на результат.

### GET /get-result/
Получить итоговый результат.
#### Пример запроса
```curl -X POST \
    http://api.cards.infohound.ru/get-result/ \
    -H 'authorization: Bearer accesstokenhere' \
    -H 'Accept: application/json' \
    -F id=dc376921-e455-47e0-9579-1859f9c8a6d3
```
#### Ответ
Заявка обрабатывается, попробуйте повторить запрос через несколько секунд:
```
"Photo in processing. Repeat the request after few seconds."
```
или результаты проверки:
```
{  
   "id":dc376921-e455-47e0-9579-1859f9c8a6d3,
   "scoreAccount":0.539316429425159,
   "scoreCardholder":0.2705212543675632,
   "scoreValid":0.5348315303041271,
   "answer":"RED",
   "summary":"Очень вероятно, что изображение подвергалось обработке в графической программе. ",
   "details":"Изображение не содержит метаданных об исходной камере, и его сигнатура соответствует известным программным продуктам. "
}
```
Где `id` - наш `id` заявки.
`scoreAccount` - соответствие номера карты на фото с введенным в apply (от 0 до 1).
`scoreCardholder` - соответствие имени на фото с введенным в apply (от 0 до 1).
`scoreValid` - соответствие срока действия карты на фото с введенным в apply (от 0 до 1).
`answer` - обработано ли фото. `RED` - обработано, `GREEN` - фото не проходило обработку.
`summary` - краткий вывод (по-русски).
`details` - подробный вывод (по-русски).


### GET /get-photo/{id}
Получить фото. В случае успеха в ответ приходит фотография карты.

#### Пример запроса
```curl -X GET \
    http://api.cards.infohound.ru/get-photo/dc376921-e455-47e0-9579-1859f9c8a6d3 \
    -H 'authorization: Bearer accesstokenhere' 
```

Где `dc376921-e455-47e0-9579-1859f9c8a6d3` - наш `id` заявки.
`accesstokenhere` - ваш токен, полученный в личном кабинете.

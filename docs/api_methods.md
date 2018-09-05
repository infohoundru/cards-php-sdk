## Описание методов API
Базовый адрес API `https://api.infohound.ru`. Запросы отправляются по HTTP-протоколу, согласно документации. Для авторизации по протоколу [OAuth2](https://ru.wikipedia.org/wiki/OAuth).

### POST /upload
Загрузить фотографию для обрезки и дальнейшей обработки.
#### Пример запроса
```
    curl -X POST \
    https://api.infohound.ru/upload \
    -H 'authorization: Bearer accesstokenhere' \
    -H 'Accept: application/json' \
    -F file=@\\path\to\card.jpg``
```
Где `accesstokenhere` - ваш токен, полученный в личном кабинете.

#### Ответ
```
   {
      "ok":"1",
      "info":"https:\/\/api.infohound.ru\/get-photo?token=oKQnITnvObmXN2wcFPvtE7Hv74pDB3Prb7cSNedklcgXNqvWQMviWlDiS7VV",
      "width":800,
      "height":600
   }
```
Где `info` - url фото-оригинала, а `width` и `height` - ширина и в пикселях, соответственно.
`token` в url действителен сутки. Спустя 24 часа url становится недействительным.

### POST /crop-photo/
#### Пример запроса
```
    curl -X POST \
    https://api.infohound.ru/crop-photo/ \
    -H 'authorization: Bearer accesstokenhere' \
    -H 'Accept: application/json' \
    -F imageSource=https://api.infohound.ru/get-photo?token=oKQnITnvObmXN2wcFPvtE7Hv74pDB3Prb7cSNedklcgXNqvWQMviWlDiS7VV
    -F matrix[0][]: 0.528169014084507
    -F matrix[0][]: 0
    -F matrix[0][]: -20
    -F matrix[1][]: 0
    -F matrix[1][]: 0.528169014084507
    -F matrix[1][]: -12.957746478873219
    -F matrix[2][]: 0
    -F matrix[2][]: 0
    -F matrix[2][]: 1
```
Где `accesstokenhere` - ваш токен, полученный в личном кабинете.
`matrix` - матрица 3 на 3 с аффинными преобразованиями. Она необходима для обрезки фотографии строго по краям карты.
Обрезка происходит автоматически на стороне нашего сервера.

#### Как считается matrix

Изначально матрица имеет вид:
```
         [1 0 0]
matrix = [0 1 0]
         [0 0 1]
```

При масштабировании фотографии:
```
         [scale 0     0]
matrix = [0     scale 0] * matrix
         [0     0     1]
```

При повороте фотогарфии вокруг центра координат:
```
         [cos -sin  0]
matrix = [sin  cos  0] * matrix
         [0    0    1]
```

Отражение относительно оси Х:
```
         [-1 0 0]
matrix = [ 0 1 0] * matrix
         [ 0 0 1]
```

Перемещение фотографии по осям Х и У:
```
         [1 0 x]
matrix = [0 1 y] * matrix
         [0 0 1]
```

Перед отправкой фото на обрезку:
```
                  [1 0 -img_w/2]
matrix = matrix * [0 1 -img_h/2]
                  [0 0  1      ]

         [1 0 710/2]
matrix = [0 1 460/2] * matrix
         [0 0 1    ]
```
где img_w ширина загруженной фотографии в пикселях, img_w - высота.

#### Ответ
```
    {
      "api_url":"https://api.infohound.ru",
      "file":"https:\/\/api.infohound.ru\/get-photo?token=oKQnITnvObmXN2wcFPvtE7Hv74pDB3Prb7cSNedklcgXNqvWQMviWlDiS7VV&cropped=1",
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
      "token":"oKQnITnvObmXN2wcFPvtE7Hv74pDB3Prb7cSNedklcgXNqvWQMviWlDiS7VV"
   }
```
Где `file` - ссылка на обрезанное фото,
`token` - токен, использующийся в методах /get-photo, /apply и /get-result, действителен 24 часа.

### POST /apply
```
    curl -X POST \
    https://api.infohound.ru/apply/ \
    -H 'authorization: Bearer accesstokenhere' \
    -H 'Accept: application/json' \
    -F token=oKQnITnvObmXN2wcFPvtE7Hv74pDB3Prb7cSNedklcgXNqvWQMviWlDiS7VV
    -F card_holder=IVAN IVANOV
    -F card_number=5213243700000000
    -F card_exp=01/25
```

Где `token` - `token` заявки из ответа метода crop-photo, `card_holder` - имя на карте, `card_number` - номер карты,
а `card_exp` - дата истечения в формате `ММ/ГГ`.

#### Ответ
```
{
    "status":"ok",
    "cropped_photo":"https:\/\/api.infohound.ru\/get-photo?token=oKQnITnvObmXN2wcFPvtE7Hv74pDB3Prb7cSNedklcgXNqvWQMviWlDiS7VV&cropped=1",
    "get_result":"https:\/\/api.infohound.ru\/get-result?token=oKQnITnvObmXN2wcFPvtE7Hv74pDB3Prb7cSNedklcgXNqvWQMviWlDiS7VV"
}
```
Где `cropped_photo` - ссылка на обрезанное фото,
`get_result` - ссылка на получение результата по фото.

### GET /get-result/
Получить итоговый результат.
#### Пример запроса
```
    curl -X POST \
    https://api.infohound.ru/get-result/ \
    -H 'authorization: Bearer accesstokenhere' \
    -H 'Accept: application/json' \
    -F token=oKQnITnvObmXN2wcFPvtE7Hv74pDB3Prb7cSNedklcgXNqvWQMviWlDiS7VV
```
#### Ответ
Заявка обрабатывается, попробуйте повторить запрос через несколько секунд:
```
"Photo in processing. Repeat the request after few seconds."
```
или результаты проверки:
```
{
   "scoreAccount":0.539316429425159,
   "scoreCardholder":0.2705212543675632,
   "scoreValid":0.5348315303041271,
   "answer":"RED",
   "summary":"Очень вероятно, что изображение подвергалось обработке в графической программе. ",
   "details":"Изображение не содержит метаданных об исходной камере, и его сигнатура соответствует известным программным продуктам. "
}
```
Где `scoreAccount` - соответствие номера карты на фото с введенным в apply (от 0 до 1).
`scoreCardholder` - соответствие имени на фото с введенным в apply (от 0 до 1).
`scoreValid` - соответствие срока действия карты на фото с введенным в apply (от 0 до 1).
`answer` - обработано ли фото. `RED` - обработано, `GREEN` - фото не проходило обработку.
`summary` - краткий вывод (по-русски).
`details` - подробный вывод (по-русски).


### GET /get-photo/
Получить фото. В случае успеха в ответ приходит фотография карты.

#### Пример запроса
```
    curl -X GET \
    https://api.infohound.ru/get-photo?token=oKQnITnvObmXN2wcFPvtE7Hv74pDB3Prb7cSNedklcgXNqvWQMviWlDiS7VV \
    -H 'authorization: Bearer accesstokenhere'
```

Где `token` - `token` заявки.
`accesstokenhere` - ваш токен, полученный в личном кабинете.
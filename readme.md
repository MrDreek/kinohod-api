## Порядок деплоя

**_git clone git@github.com:MrDreek/kinohod-api.git_**

**_composer install --no-dev_** установка зависимостей без require-dev

**_php -r "file_exists('.env') || copy('.env.example', '.env');"_**

**_php artisan key:generate_**

Указать необходимоые данные в файле .env (Подключение к базе, настройки прокси, ключ кинохода)

_**php artisan config:cache**_  // команда для кеширования настроек окружения


## Методы API


GET _{host}/api/get-city-list_

Вернёт ОК или ошибку

`Запишет в базу список всех городов`


POST _{host}/api/get-code_

`Вернёт код города из базы, код соответствует ID города в сервисе Киноход`

тело запроса:

```json
{
	"name": "Санкт-Петербург"
}
```

Результат: 
```json
{
    "code": 2
}
```

Запрос без параметров:
```json
{
    "error": {
        "movieId": "Требуется указать id фильма"
    }
}
```

Запрос со строкой:
```json
{
    "error": {
        "movieId": "id фильма должен быть числом"
    }
}
```

POST _{host}/api/get-movie-list

`Вернёт список фильмов по коду города`

Обязательный параметр **{code}** - это код города из сервиса Киноход

тело запроса:

```json
{
	"code": 2
}
```

Результат:
```json
[  
   {  
      "id":17676,
      "originalTitle":"The Predator",
      "annotationFull":"Экипаж корабля экстренно высаживается в джунглях, где обитает некая тварь, о которой не говорят вслух. Люди начинают исчезать один за другим, и их тела с содранной кожей находят на деревьях. Только когда по счастливой случайности одна из жертв хищника выживает, становится понятно, что положение оставшихся в живых не просто ужасное — оно безнадежное.",
      "genres":[  
         {  
            "name":"ужасы",
            "id":17
         },
         {  
            "name":"экшен",
            "id":7
         }
      ],
      "countries":[  
         "США"
      ],
      "productionYear":2018,
      "title":"Хищник",
      "ageRestriction":"18+",
      "annotationShort":"Ужасы. Экипаж корабля экстренно высаживается в джунглях, где обитает жуткая тварь.",
      "poster":{  
         "rgb":"d96633",
         "name":"b9ed7923-7269-4de8-b9ce-264e076971ef.jpg"
      },
      "imdbId": "7886614"
   },
   {  
      "id":12096,
      "originalTitle":"Счастья! Здоровья!",
      "annotationFull":"Три истории любви: три свадьбы, три разные культурные традиции. Герои русской новеллы знают, что именно их свадьба должна быть самой лучшей, а свадебный ролик — самым красивым. Но подруги успевают наговорить лишнего, родители абсолютно не готовы к празднику, а яркое прошлое молодоженов может сорвать церемонию. Вторая история случается с двумя татарскими семьями, для которых свадьба собственных детей оказывается абсолютной неожиданностью, а обстоятельства, толкнувшие ребят на этот шаг, остаются для них загадкой. Героиня нашей третьей истории — коренная москвичка, а ее жених — ассимилированный армянин, выросший в столице. Хотят герои того или нет, но свадьба должна пройти при соблюдении всех традиций, которые молодоженам, конечно же, неизвестны. Всем трём парам хочется пожелать счастья и здоровья – ведь любовь у них уже есть!",
      "genres":[  
         {  
            "name":"комедия",
            "id":2
         }
      ],
      "countries":[  
         "Россия"
      ],
      "productionYear":2017,
      "title":"Счастья! Здоровья!",
      "ageRestriction":"16+",
      "annotationShort":"Комедия. Три истории любви: три свадьбы, три разные культурные традиции.",
      "poster":{  
         "rgb":"807e79",
         "name":"8334f41c-7d2a-4a76-a4a8-98bd4ebe8d86.jpg"
      },
      "imdbId": null
   }
]
```

Запрос без параметров:
```json
{
    "error": {
        "code": "Требуется указать код города"
    }
}
```

Запрос со строкой:
```json
{
    "error": {
        "code": "Код должен быть числом"
    }
}
```

POST _{host}/api/get-movie-detail

`Вернёт подробную информацию о фильме по его Id`

Обязательный параметр **{Id}** - это Id фильма сервиса Киноход

Тело запроса:
```json
{
	"movieId": 17676
}
```

Результат:
```json
{
   "_id":"5b97ba879b27090b274ffe7f",
   "originalTitle":"The Predator",
   "annotationFull":"Экипаж корабля экстренно высаживается в джунглях, где обитает некая тварь, о которой не говорят вслух. Люди начинают исчезать один за другим, и их тела с содранной кожей находят на деревьях. Только когда по счастливой случайности одна из жертв хищника выживает, становится понятно, что положение оставшихся в живых не просто ужасное — оно безнадежное.",
   "genres":[
      {
         "name":"ужасы",
         "id":17
      },
      {
         "name":"экшен",
         "id":7
      }
   ],
   "id":17676,
   "countries":[
      "США"
   ],
   "productionYear":2018,
   "title":"Хищник",
   "ageRestriction":"18+",
   "annotationShort":"Ужасы. Экипаж корабля экстренно высаживается в джунглях, где обитает жуткая тварь.",
   "poster":{
      "rgb":"d96633",
      "name":"b9ed7923-7269-4de8-b9ce-264e076971ef.jpg"
   },
   "trailers":[
      {
         "source":{
            "filename":"dad85cc0-5c94-465a-8736-56bdbadc168f.qt",
            "duration":102.03,
            "contentType":"video/quicktime"
         },
         "videos":[
            {
               "filename":"dad85cc0-5c94-465a-8736-56bdbadc168f_mobile_mp4.mp4",
               "duration":102.03,
               "contentType":"video/quicktime"
            }
         ],
         "preview":{
            "rgb":"070a0a",
            "name":"7ef42c20-b452-4341-8d36-89faf7804459.jpg"
         }
      }
   ],
   "premiereDateWorld":"2018-09-13",
   "imdbId":"3829266",
   "directors":[
      {
         "name":"Шейн Блэк",
         "id":1581
      }
   ],
   "duration":105,
   "updated_at":"2018-09-11 12:52:23",
   "created_at":"2018-09-11 12:52:23"
}
```

Запрос без параметров:
```json
{
    "error": {
        "movieId": "Требуется указать id фильма"
    }
}
```

Запрос со строкой:
```json
{
    "error": {
        "movieId": "id фильма должен быть числом"
    }
}
```

POST _{host}/api/get-seances

`Вернёт все сеансы в городе по обпределённому фильму`

Обязательный параметр **{code}** - это код города из сервиса Киноход
Обязательный параметр **{movieId}** - это id фильма сервиса Киноход

Тело запроса:
```json
{
	"code": 2,
	"movieId": 17676
}
```

Результат:
```json
[
   {
      "id":"77393033",
      "hallId":4499,
      "startTime":"2018-09-17 00:20:00+03",
      "languageId":null,
      "subtitleId":null,
      "groupName":"Сеансы 3D",
      "time":"00:20",
      "formats":[
         "3d"
      ],
      "minPrice":400,
      "maxPrice":400,
      "date":"2018-09-16",
      "cinemaId":225
   },
   {
      "id":"77385081",
      "hallId":4525,
      "startTime":"2018-09-17 00:20:00+03",
      "languageId":null,
      "subtitleId":null,
      "groupName":"Обычные сеансы 2D",
      "time":"00:20",
      "formats":[
         "2d"
      ],
      "minPrice":360,
      "maxPrice":360,
      "date":"2018-09-16",
      "cinemaId":223
   }
]
```

Запрос без параметров:
```json
{
    "error": {
        "code": "Требуется указать код города",
        "movieId": "Требуется указать id фильма"
    }
}
```

Запрос со строкой:
```json
{
    "error": {
        "code": "Код должен быть числом"
    }
}
```

# Описание методов API

## Оглавление
<!-- TOC -->
* [Описание методов API](#описание-методов-api)
  * [Оглавление](#оглавление)
  * [Адрес домена](#адрес-домена)
  * [Структуры данных](#структуры-данных)
  * [Метод login](#метод-login)
    * [Описание метода](#описание-метода)
    * [Адрес](#адрес)
    * [Параметры](#параметры)
    * [Успешный ответ](#успешный-ответ)
    * [Ошибки](#ошибки)
  * [Метод logout](#метод-logout)
    * [Описание метода](#описание-метода-1)
    * [Адрес](#адрес-1)
    * [Параметры](#параметры-1)
    * [Успешный ответ](#успешный-ответ-1)
    * [Ошибки](#ошибки-1)
  * [Метод selectTeam](#метод-selectteam)
    * [Описание метода](#описание-метода-2)
    * [Адрес](#адрес-2)
    * [Параметры](#параметры-2)
  * [Успешный ответ](#успешный-ответ-2)
  * [Ошибки](#ошибки-2)
  * [Метод getTeamsInfo](#метод-getteamsinfo)
    * [Описание метода](#описание-метода-3)
    * [Адрес](#адрес-3)
    * [Параметры](#параметры-3)
    * [Успешный ответ](#успешный-ответ-3)
    * [Ошибки](#ошибки-3)
  * [Метод getSkins](#метод-getskins)
    * [Описание метода](#описание-метода-4)
    * [Адрес](#адрес-4)
    * [Параметры](#параметры-4)
    * [Успешный ответ](#успешный-ответ-4)
    * [Ошибки](#ошибки-4)
  * [Метод setSkins](#метод-setskin)
    * [Описание метода](#описание-метода-5)
    * [Адрес](#адрес-5)
    * [Параметры](#параметры-5)
    * [Успешный ответ](#успешный-ответ-5)
    * [Ошибки](#ошибки-5)  
  * [AutoLogin](#autologin)
  * [AutoRegistration](#autoregistration)
  * [CheckToken](#checktoken)

<!-- TOC -->

## Адрес домена

```http://localhost```

## Структуры данных

* Успешный ответ

```
CorrectAnswer = {
    result: 'ok',
    data: Data
}
```

* Ошибка

```
WrongAnswer = {
    result: 'error',
    error: {
        code: number,
        text: string
    }
}
```

* Пользователь

```
User = {
    id: number,
    name: string,
    token: string,
}
```

## Метод login

### Описание метода
Метод авторизации. См параметры ответа ниже

### Адрес

```/?method=login```

### Параметры

| параметр | тип    | комментарий                  |
|----------|--------|------------------------------|
| login    | string | логин юзера                  |
| hash     | string | md5(md5(login+password)+rnd) |
| rnd      | number | целое рандомное число        |

### Успешный ответ

```
CorrectAnswer=>User
```

### Ошибки

```
WrongAnswer(code: 1001, text: 'params login or password not set')
WrongAnswer(code: 1002, text: 'error in auth user')
WrongAnswer(code: 1004, text: 'Unable to find user. Are you cheater?')
```

## Метод logout

### Описание метода
При успешном ответе(см.ниже) поступает запрос в базу данных, производится поиск по 
токену(к какому пользователю принадлежит) и обнуляется

### Адрес

```/?method=logout```

### Параметры

| параметр | тип    | комментарий           |
|----------|--------|-----------------------|
| token    | string | авторизационный токен |

### Успешный ответ

```
CorrectAnswer=>true
```

### Ошибки

```
WrongAnswer(code: 242, text: 'params not set fully ')
Дописать
```

## Метод selectTeam

### Описание метода

При успешной авторизации и при успешном ответе(см ниже) в базу данных, где уникальный идентификатор - это
teamCode, записывается токен пользователя.

### Адрес
```/?method=selectTeam```

### Параметры

| Параметры | Тип    | Комментарий            |
|-----------|--------|------------------------|
| id        | number | Id пользователя        |
| token     | string | Авторизационный токен  |
| teamCode  | number | Уникальный Код команды |



## Успешный ответ

```
CorrectAnswer=>true
```

## Ошибки

``` 
WrongAnswer(code:603, text:'Selected team is full')
WrongAnswer(code:604, text: 'Team not found')
WrongAnswer(code:605, text: 'In selected team more gamers than in the other.
Please, select other team ')
WrongAnswer(code:1002, text: 'error in auth user')
```

## Метод getTeamsInfo

### Описание метода
При успешной авторизации и при успешном ответе(см ниже) из базы данных по teamCode
извлекается информация о команде: количество очков, количество игроков

### Адрес
```
/?method=getTeamsInfo
```

### Параметры

| Параметры | Тип    | Комментарий            |
|-----------|--------|------------------------|
| teamCode  | number | Уникальный код команды |

### Успешный ответ
```
CorrectAnswer=>data = {
numberOfPlayers: number,
numberOfTeamPoints: number
}
```

### Ошибки

```
WrongAnswer(code:304, text: 'Team not found')

```

## Метод getSkins

### Описание метода
При успешном ответе(см.ниже) возвращаются возможные скины, 
применимые для игрока

### Адрес
```/?method = getSkins```

### Параметры

| Параметры | Тип    | Комментарий              |
|-----------|--------|--------------------------|
| id        | number | Id пользователя          |
| token     | string | Аутентификационный токен |     


### Успешный ответ
```
CorrectAnswer=>data = {
skins: skins{ },
numberOfSkins:number
}
```
### Ошибки
```
WrongAnswer(code:1002, text: 'error in auth user')
WrongAnswer(code:700, text:'No skins')
WrongAnswer(code:705, text:'User is not found')

```

## Метод setSkin

### Описание метода
При успешном ответе(см.ниже) устанавливает игроку переданный скин

### Адрес
```/?method = setSkin```

### Параметры

| Параметры | Тип    | Комментарий              |
|-----------|--------|--------------------------|
| id        | number | Id пользователя          |
| token     | string | Аутентификационный токен |   
| skin      | string | Выбранный скин           |       


### Успешный ответ
```
CorrectAnswer=>data = {
id: number,
setSkin: string
}
```
### Ошибки
```
WrongAnswer(code:1002, text: 'error in auth user')
WrongAnswer(code:700, text:'No skins')
WrongAnswer(code:701, text:'Skin is not found')
WrongAnswer(code:705, text:'User is not found')
```

## AutoLogin

## AutoRegistration

## CheckToken

##






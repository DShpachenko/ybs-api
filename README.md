# Сервис API

## Описание

Сервис содержит в себе на данный момент функционал для обработки:
- регистрации
- авторизации
- восстановления пароля
- обновления токена

Формат обращений к сервису должен осуществляться согласно спецификации JsonRPC 2.0 
путем отправки POST запросов.

Сервер доступен по ссылке http://api.local

## Методы API

### Модуль Авторизации

Регистрация проходит в 2 этапа:
1. Запрос регистрации (отправка смс сообщения с кодом для её подтверждения)
2. Подтверждение регистрации.

Восстановление проходит аналогично в 2 этапа, как и регистрация.

При восстановлении пароля сбрасываются все токены пользователя.

#### Начало регистрации <code>POST - http://api.local </code>
<pre>
Запрос:
{
    "jsonrpc": "2.0",
    "method": "registrationStart",
    "params": {
    	"name": "SDmitry",
        "phone": "+7 (919) 874-81-91",
        "password": "123456"
    },
    "id": "123"
}
</pre>
<pre>
На этапе разработки код смс сообщения прилетает в теле ответа запроса на регистрацю.

Ответ:
{
    "jsonrpc": "2.0",
    "result": {
        "status": "success",
        "key": 2967
    },
    "id": "123"
}
</pre>

#### Подтверждение регистрации <code>POST - http://api.local </code>
<pre>
Запрос:
{
    "jsonrpc": "2.0",
    "method": "registrationConfirm",
    "params": {
        "phone": "+7 (919) 874-81-91",
        "key": "2967"
    },
    "id": "123"
}
</pre>
<pre>
Ответ:
{
    "jsonrpc": "2.0",
    "result": {
        "status": "success"
    },
    "id": "123"
}
</pre>

#### Авторизация <code>POST - http://api.local </code>
<pre>
Запрос:
{
    "jsonrpc": "2.0", 
    "method": "login",
    "params": {
        "phone": "+7 (919) 874-81-91",
        "password": "123456"
    },
    "id": "123"
}
</pre>
<pre>
Ответ:
{
    "jsonrpc": "2.0",
    "result": {
        "refresh_token": "eyJ0eXAiOiJKV...zfufkXsw",
        "access_token": "eyJ0eXAiOiJKV1...CGOSD2Rc"
    },
    "id": "123"
}
</pre>

#### LogOut (Выход, сброс токена) <code>POST - http://api.local </code>
<pre>
Запрос:
{
    "jsonrpc": "2.0", 
    "method": "logout",
    "params": {
    	refresh_token": "eyJ0eXAiOiJKV...zfufkXsw"
    },
    "id": "123"
}
</pre>
<pre>
Ответ:
{
    "jsonrpc": "2.0",
    "result": {
        "status": "OK"
    },
    "id": "123"
}
</pre>

#### Обновление токена <code>POST - http://api.local </code>
<pre>
Запрос:
{
    "jsonrpc": "2.0", 
    "method": "updateToken",
    "params": {
    	refresh_token": "eyJ0eXAiOiJKV...zfufkXsw"
    },
    "id": "123"
}
</pre>
<pre>
Ответ:
{
    "jsonrpc": "2.0",
    "result": {
        "refresh_token": "eyJ0eXAiOiJKV...zfufkXsw",
        "access_token": "eyJ0eXAiOiJKV1...CGOSD2Rc"
    },
    "id": "123"
}
</pre>

#### Восстановление пароля, начало <code>POST - http://api.local </code>
<pre>
Запрос:
{
    "jsonrpc": "2.0",
    "method": "restoreStart",
    "params": {
        "phone": "+7 (919) 874-81-91"
    },
    "id": "123"
}
</pre>
<pre>
На этапе разработки код смс сообщения прилетает в теле ответа запроса на восстановление.
Ответ:
{
    "jsonrpc": "2.0",
    "result": {
        "status": "success",
        "key": 1538
    },
    "id": "123"
}
</pre>

#### Восстановление пароля, подтверждение <code>POST - http://api.local </code>
<pre>
Запрос:
{
    "jsonrpc": "2.0",
    "method": "restoreConfirm",
    "params": {
        "phone": "+7 (919) 874-81-91",
        "password": "123456",
        "key": "1538"
    },
    "id": "123"
}
</pre>
<pre>
Ответ:
{
    "jsonrpc": "2.0",
    "result": {
        "status": "success"
    },
    "id": "123"
}
</pre>

#### Запрос повторной отправки смс-кода <code>POST - http://api.local </code>

Запрос повторной отправки смс-кода может использоваться как при регистрации, так и при восстановлении пароля.
В зависимости от статуса пользователя (подтверждена или нет его регистрация) будет отправлено сообщение 
для подтверждения регистрации либо для подтверждения смены пароля.

<pre>
Запрос:
{
    "jsonrpc": "2.0",
    "method": "sendSms",
    "params": {
        "phone": "+7 (919) 874-81-91"
    },
    "id": "123"
}
</pre>
<pre>
Ответ:
{
    "jsonrpc": "2.0",
    "result": {
        "status": "success",
        "key": 9939
    },
    "id": "123"
}
</pre>

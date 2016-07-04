# LuaParameterHandler

Скрипт проверки параметров для LUA скриптов в проектах 8bitgroup.
Проверяет соответствие параметров в целевом файле nginx/parameters.conf с файлом nginx/parameters.conf.dist
Цель: синхронизация пераметров для всех серверов, при деплое.

Файл  nginx/parameters.conf.dist, должен располагаться в репозитарии и иметь актуальные данные.

В файле прописываются параметры по умолчанию.
Для "секретных параметров", необходимо порписать значение "" (пустая строка).

Файл nginx/parameters.conf, должен располагаться в shared файлах, на всех серверах и содержать актуальные данные для своего окружения.

Если одного из параметров в целевом файле не найдено, предлагает в интерактивном режиме заполнить значение параметров.




## Установка

1) composer require 8bitov/lua-parameter-handler

2) В секции extra, в composer.json прописать:

        "8bit-lua-parameters": {
            "file": "nginx/parameters.conf"
        }

3) В секциях post-install-cmd  и post-update-cmd, в composer.json прописать:

 "Bitov8\\LuaParameterHandler\\ScriptHandler::buildParameters"


﻿# doodle-mutator

## EN

Parallel turn-based multiplayer online drawing game.
Server-side scripts in PHP (version 7.3.0 and later) and client-side in JS (ES5 and later).
URL-rewriting is currently required to work (Apache htaccess is added automatically, Nginx conf example is available).
An optional board for user feedback is available [here](https://github.com/f2d/bakareha) (uses Perl).

### How to install

1. Put the folders onto your web-server, make sure they are writeable by the scripts.
	1. You can rename root folders as you wish, but change the paths in respective config files too.
	2. Even if you keep everything as is, don't forget to change passwords.
2. Get the web-server up and running, index.php must be default root for the doodle folder.
	* Required PHP modules: gd, intl, json, mbstring.
	* Optional PHP modules: opcache, shmop.
3. Open the doodle folder in a web-browser, input a name for the admin account.

### What is the game about

0. Make a room.
1. First person writes some initial descriptions.
2. Another person gets one of those descriptions as a task to draw, up to one's imagination.
3. Someone else then gets to see that picture to describe its content, meaning or effect, whatever, without seeing its initial description.
4. Draw by description of unknown origin.
5. Describe a drawing of unknown origin.

May be continued as long as the participants wish to, then watch resulting chains of sense mutation.
Can be fun, depending on people's efforts and ideas.
Another way to name the game is "broken telephone", it can be played with just a pen and paper.

### Contributing

* Participating in the game makes it meaningful to keep the server working.
* Help with development may be helpful: code patches, reports, ideas.
* Donations to support my server hosting can be sent via [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PY8G49CJCDQLU), [Qiwi](https://qiwi.me/f2d), [etc.](https://2draw.me/index.htm)

## RU

### В чём смысл игры

0. Создаётся комната.
1. Один человек пишет начальные описания.
2. Другой получает что-то из описаний как задание это нарисовать, как понимает.
3. Кто-то ещё видит получившийся рисунок и должен описать, что видит. Изначальное описание не показывается.
4. Рисовать по описанию неизвестно чего.
5. Описывать рисунки неизвестно чего.

Можно продолжать, пока не надоест, потом смотреть на получившиеся цепочки.
Иногда забавно выходит, смотря кто как постарается и какие идеи принесёт.
Вариант названия — "испорченный телефон", в такое можно играть и на бумаге.

### Помощь проекту

* Участие в игре сохраняет смысль держать работающий сервер.
* Помощь в доработке пригодится, будь то патчи, сообщения или идеи.
* Поддержать оплату сервера будет кстати, через [Киви](https://qiwi.me/f2d), [Пейпал](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PY8G49CJCDQLU), [и т.д.](https://2draw.me/index.htm)

﻿<?php

$tmp_about = 'О сайте';
$tmp_announce = array(
	'anno' =>	'Общее объявление'
,	'stop' =>	'Игра заморожена'
,	'room_anno' =>	'Комнатное объявление'
,	'room_stop' =>	'Комната заморожена'
,	'new_game' =>	'Пользователей ещё не существует. Первый пользователь станет управляющим — администратором движка.'
,	'new_room' =>	'Эта комната ещё не существует. Первый участник станет управляющим — модератором комнаты.'
,	'new_data' =>	'Найдены данные в старом формате, используйте мод.панель: файлы: привести к новому виду.'
);
$tmp_archive = 'Архив';
$tmp_archive_find = 'Найти';
$tmp_archive_find_by = array(
	'post' => array(
		'select'	=> 'описание'
	,	'found by'	=> 'описанию'
	,	'placeholder'	=> 'Введите часть описания.'
	)
,	'file' => array(
		'select'	=> 'имя файла'
	,	'found by'	=> 'имени файла'
	,	'placeholder'	=> 'Введите часть имени файла, напр.: 0123abcdef.png, jpg, res, и т.д.'
	)
,	'bytes' => array(
		'select'	=> 'размер файла (байты)'
	,	'found by'	=> 'размеру файла'
	,	'placeholder'	=> 'Перечислите рамки размера файла, напр.: > 0, < 123кб, 4м-4.5мб, = 67890, и т.д.'
	)
,	'width' => array(
		'select'	=> 'ширина полотна (пиксели)'
	,	'found by'	=> 'ширине полотна'
	,	'placeholder'	=> 'Перечислите рамки ширины полотна, напр.: > 0, < 640, 640-800, = 800, etc.'
	)
,	'height' => array(
		'select'	=> 'высота полотна (пиксели)'
	,	'found by'	=> 'высоте полотна'
	,	'placeholder'	=> 'Перечислите рамки высоты полотна, напр.: > 0, < 360, 360-800, = 800, etc.'
	)
,	'time' => array(
		'select'	=> 'время на рисование (секунды)'
	,	'found by'	=> 'времени на рисование'
	,	'placeholder'	=> 'Перечислите рамки времени, напр.: > 0, < 10:20:30, 40-50, = 60, и т.д.'
	)
,	'used' => array(
		'select'	=> 'использовано'
	,	'found by'	=> 'использованному'
	,	'placeholder'	=> 'Введите инструмент, напр.: имя рисовалки, undo, read file, text, и т.д.'
	)
,	'name' => array(
		'select'	=> 'часть имени автора'
	,	'found by'	=> 'части имени автора'
	,	'placeholder'	=> 'Введите часть имени автора постов.'
	)
,	ARG_FULL_NAME => array(
		'select'	=> 'полное имя автора'
	,	'found by'	=> 'полному имени автора'
	,	'placeholder'	=> 'Введите полное имя автора постов.'
	)
);
$tmp_archive_found = 'Результаты поиска по';
$tmp_archive_hint = 'Скрытые комнаты не показаны.';
$tmp_archiver_button = 'Приготовить архив';
$tmp_archiver_by_user_id = 'По вашему номеру профиля (отсутствует в архивах)';
$tmp_archiver_by_user_names = 'По указанным именам авторов (целиком одно на строку)';
$tmp_archiver_by_user_names_hint = 'Список имён авторов целиком (по одному на строку).';
$tmp_archiver_from_arch = 'Из архивных нитей';
$tmp_archiver_from_room = 'Из активных (незавершённых)';
$tmp_archiver_hidden_room = 'скрытая комната';
$tmp_archiver_naming = 'Названия файлов в архиве';
$tmp_archiver_naming_parts = array(
	'author' => 'автор'
,	'room' => 'комната'
,	'thread' => 'нить (только в архиве)'
,	'date' => 'дата'
/*,	'width' => 'ширина'
,	'height' => 'высота'
,	'bytes' => 'размер в байтах'
,	'fbytes' => 'размер в К/М/байтах'
*/,	'i' => 'идентификатор рисунка (добавляется в любом случае). Текст в <угловых скобках> пропускается, если внутри не хватает значения для подстановки.'
);
$tmp_archives = 'Архивы';
$tmp_arch_count = 'нитей';
$tmp_arch_last = 'последнее';
$tmp_back = 'Назад.';
$tmp_ban = 'Доступ запрещён.';
$tmp_check_required = 'Дополнительная проверка';
$tmp_describe_hint = 'От '.DESCRIBE_MIN_LENGTH.' до '.DESCRIBE_MAX_LENGTH.' букв.\\
[a|Возможности формата]\\
[hid|\\
	[r poem|\\
		Например так,
		или дважды

		для пустой строки.
	]
	Для блоков стихов начните и завершите весь текст описания и каждую строку косой чертой, полностью отделённой пробелами.
	[cite|'
.POST_LINE_BREAK.' Например так, '
.POST_LINE_BREAK.' или дважды '
.POST_LINE_BREAK.' '
.POST_LINE_BREAK.' для пустой строки. '
.POST_LINE_BREAK.']
	Результат показан справа. →
	Не отделённые полностью черты останутся как есть, включая [cite|'
.POST_LINE_BREAK
.POST_LINE_BREAK.'] двойные.
]';
$tmp_describe_free = 'Напишите что-нибудь';
$tmp_describe_new = 'Опишите рисунок, который хотели бы видеть';
$tmp_describe_next = 'Напишите, что произойдёт после этого';
$tmp_describe_this = 'Опишите, что видите на этом рисунке';
$tmp_draw_app = array('JS Плоская', 'JS Слои', 'Просто загрузить свой файл');
$tmp_draw_app_select = 'Вариант рисовалки';
$tmp_draw_free = 'Нарисуйте что-нибудь';
$tmp_draw_hint = 'Этой странице доступно то же, что и в игре. Можно пользоваться для восстановления, оффлайн-правок, сохранения в файл и т.д.';
$tmp_draw_limit_hint = 'Прикрепите свой рисунок размером от %sх%s до %sх%s пикселей и не более %s байт (%s) в файле одного из типов: %s.';
$tmp_draw_next = 'Попробуйте нарисовать, что произойдёт после этого';
$tmp_draw_test = 'Испытать рисовалку.';
$tmp_draw_this = 'Попробуйте нарисовать';
$tmp_empty = 'Пусто';
$tmp_foot_notes = array('Об этом сайте', 'проект', 'автор', 'доска сообщений', ' для связи.');
$tmp_header_links = array(
	'drawpile' => 'Drawpile (порисовать вместе)'
,	'index' => 'прочее'
);
$tmp_me = 'Назовитесь';
$tmp_me_hint = 'Ваш псевдоним, не более '.USER_NAME_MAX_LENGTH.' букв. Также тут можно ввести свой старый ключ.';
$tmp_me_submit = 'Войти';
$tmp_mod_files = array(
	'arch' =>		'Переписать все архивы по новым шаблонам.'
,	'arch_pix_404' =>	'Переписать все архивы, заменяя ненайденные картинки 404-заглушкой, или наоборот.'
,	'arch_pix_hash' =>	'Переписать все архивы, пересчитывая хэши картинок (для зипа, но не названия файлов) + 404-заглушки.'
,	'img2orphan_check' =>	'Найти картинки, не используемые в комнатах или архивах.'
,	'img2orphan' =>		'Переместить картинки, не используемые в комнатах или архивах, в запасную папку.'
,	'img2subdir' =>		'Переместить картинки из корня папки картинок в подпапки.'
,	'users' =>		'Привести старые данные пользователей к новому виду.'
,	'logs' =>		'Привести старые журналы действий к новому виду.'
,	'room_list_reset' =>	'Очистить кэш счётчиков для списка комнат.'
,	'hta_check' =>		'Проверить шаблон .htaccess для Apache2.'
,	'hta_write' =>		'Переписать .htaccess (автоматически при посещении админом главной страницы).'
,	'nginx' =>		'Пример настройки для Nginx, применять вручную.'
,	'opcache_reset' =>	'Очистить кэш скриптов PHP на всём сервере.'
,	'opcache_inval' =>	'Очистить кэш скриптов PHP из этой папки.'
,	'opcache_check' =>	'Статистика кэша скриптов PHP.'
,	'list' =>		'Смотреть файлы в папке скрипта.'
);
$tmp_mod_pages = array(
	'logs' =>	'Журнал'
,	'files' =>	'Файлы'
,	LK_USERLIST =>	'Пользователи'
,	LK_REF_LIST =>	'Реф.Ссылки'
,	'vars' =>	'Переменные'
,	'varsort' =>	'Перем.сорт'
,	'welcome' =>	'Старт.страница'
);
$tmp_mod_panel = 'Мод-панель';
$tmp_mod_post_hint = 'Операции на этот пост и тред.';
$tmp_mod_user_hint = 'Операции на этого пользователя.';
$tmp_mod_user_info = 'Информация на этого пользователя.';
$tmp_no_change = 'Нет изменений.';
$tmp_no_program_found = 'Не найдено подходящей программы.';
$tmp_no_play_hint = 'Ваше участие в игре отключено (не отбирать цели).';
$tmp_not_found = 'Не найдено.';
$tmp_not_supported = 'Эта функция недоступна.';
$tmp_options = 'Настройки';
$tmp_options_apply = 'Применить';
$tmp_options_area = array(
	'user' => 'Ваши личные данные'
,	'view' => 'Настройки сайта'
,	'arch' => 'Скачать архивы рисунков'
,	'save' => 'Доступные сохранения'
);
$tmp_options_drop = array(
	'out'	=> 'Выйти с сайта'
,	'save'	=> 'Стереть из памяти автосохранения рисунков'
,	'skip'	=> 'Забыть пропуски заданий'
,	'pref'	=> 'Сбросить настройки'
);
$tmp_options_first = 'Нажмите %s или <a href="%s">выберите комнату</a> для продолжения.';
$tmp_options_flags = 'Статус';
$tmp_options_input = array(
	'input' => array(
		ARG_DRAW_APP		=> 'Рисование: начальный вариант рисовалки'
	,	'draw_max_recovery'	=> 'Рисование: автосохраняемых копий для восстановления'
	,	'draw_max_undo'		=> 'Рисование: максимум отменяемых шагов'
	,	'draw_time_idle'	=> 'Рисование: пропуск бездействия в счёте времени после (секунд)'
	,	'trd_per_page'		=> 'Нитей на страницу архива'
	,	'room_default'		=> 'Домашняя комната (одна точка = список комнат)'
	)
,	'check' => array(
		'head'			=> 'Ссылки наверху|полные|короткие'
	,	'count'			=> 'Подсчёт содержимого комнат|показывать'
	,	'names'			=> 'Имена отправителей|показывать'
	,	'times'			=> 'Даты постов|показывать'
	,	'focus'			=> 'Фокус на поле ввода текста|авто'
	,	'active'		=> 'Видимые нити|сворачивать, если больше 1|всегда показывать'
	,	'own'			=> 'Свои посты|выделить цветом|оставить как все'
	,	'kbox'			=> 'Отправка описания|без подтверждения|галочка перед отправкой'
	,	'picprogress'		=> 'Обработка поста с рисунком|показать ход и подождать|скрыть и без пауз'
	,	'save2common'		=> 'Сохранения рисовалок|общие для всех|у каждой свои'
	,	'modtime304'		=> 'Когда на странице ничего нового|из кэша браузера|перезагружать всё равно'
	,	'unknown'		=> 'Предпочитать задания|сначала из неизвестных нитей|любые свободные'
	,	'task_timer'		=> 'Таймер открытого задания|скрыть + автопродление|показать + счёт до нуля'
	,	'capture_altclick'	=> 'Фотоснимок постов (Ctrl/Shift + клик: отметить, Alt + клик: сохранить)'
	,	'capture_textselection'	=> 'Фотоснимок постов c выделением текста (с кнопками вокруг текста)'
	)
,	'admin' => array(
		'time_check_points'	=> 'Время отработки этапов в конце страницы|показывать'
	,	'display_php_errors'	=> 'Ошибки PHP|скрыть|показывать'
	)
);
$tmp_options_email = 'Почта (E-mail)';
$tmp_options_email_hint = 'ваш@почтовый.ящик';
$tmp_options_email_show = 'видно зрителям';
$tmp_options_name = 'Ваш псевдоним';
$tmp_options_profile = 'После нажатия "применить"';
$tmp_options_profile_link = 'Проверьте страницу профиля.';
$tmp_options_qk = 'Ключ для входа';
$tmp_options_qk_hint = 'Кликните дважды, чтобы выделить целиком для копирования. Используйте при входе вместо имени.';
$tmp_options_self_intro = 'Немного о себе, если хотите';
$tmp_options_self_intro_hint = 'Ваш текст тут, ссылки как http://ссылка, картинки как [http://картинка], картинки слева, справа или по центру - [http://картинка left right center].';
$tmp_options_time = 'Общий часовой пояс';
$tmp_options_time_client = 'Ваш часовой пояс';
$tmp_options_warning = array('Предупреждение: сначала проверьте настройки сервера!', 'Смотреть пример.');
$tmp_post_err = array(
	'deny_file_op'		=> 'Правила комнаты не допускают начинать нити рисунком.'
,	'deny_file_reply'	=> 'Правила комнаты не допускают отвечать рисунком.'
,	'deny_text_op'		=> 'Правила комнаты не допускают начинать нити описанием.'
,	'deny_text_reply'	=> 'Правила комнаты не допускают отвечать описанием.'
,	'file_dup'	=> 'Файл отклонён: копия уже существует.'
,	'file_part'	=> 'Файл отклонён: неполные данные, попытайтесь загрузить в рисовалке и отправить ещё раз.'
,	'file_pic'	=> 'Файл отклонён: не рисунок.'
,	'file_put'	=> 'Файл отклонён: сохранить не удалось.'
,	'file_size'	=> 'Файл отклонён: размер вне допустимого.'
,	'no_path'	=> 'Путь не найден.'
,	'pic_fill'	=> 'Рисунок отклонён: пустая заливка.'
,	'pic_size'	=> 'Рисунок отклонён: размер вне допустимого.'
,	'text_short'	=> 'Текст отклонён: слишком мало.'
,	'trd_arch'	=> 'Архив комнаты пополнен.'
,	'trd_max'	=> 'Слишком много нитей.'
,	'trd_miss'	=> 'Промах: сброс в новую нить.'
,	'trd_n_a'	=> 'Указанная нить недоступна.'
,	'unkn_req'	=> 'Неизвестная ошибка: неприемлемый запрос.'
,	'unkn_res'	=> 'Неизвестная ошибка: неприемлемый результат.'
);
$tmp_post_ok = array(
	'new_post'	=> 'Новый пост добавлен.'
,	'skip'		=> 'Эта нить будет пропущена.'
,	'user_opt'	=> 'Настройки приняты.'
,	'user_qk'	=> 'Печеньки приняты.'
,	'user_quit'	=> 'Выход.'
,	'user_reg'	=> 'Пользователь принят.'
);
$tmp_post_progress = array(
	'starting'	=> 'Пожалуйста, подождите, пока ваш рисунок обрабатывается'
,	'opt_full'	=> 'Оптимизация рисунка'
,	'opt_res'	=> 'Оптимизация уменьшенной копии'
,	'low_res'	=> 'Уменьшение рисунка в рамки страницы'
,	'low_bit'	=> 'Ограничение палитры уменьшенной копии для снижения веса ниже полной'
,	'program'	=> 'с помощью программы'
,	'refresh'	=> 'Готово. Кликните <a href="%s">здесь</a>, если переход не начнётся через %s.'
);
$tmp_regex_hint = 'Разрешены регулярные выражения в формате {%s|%s}.';
$tmp_regex_hint_pat = 'предмет поиска';
$tmp_require_js = 'Необходима поддержка JavaScript.';
$tmp_report = 'Действия с этим ';
$tmp_report_freeze = 'Заморозить нить до исправления проблем, ломающих игру';
$tmp_report_hotfix = 'Остановки не требуется (например, опечатки в словах)';
$tmp_report_hint = 'Опишите суть проблемы или что вам нужно. '. REPORT_MIN_LENGTH.'-'.REPORT_MAX_LENGTH.' букв.';
$tmp_report_post_hint = $tmp_report.'постом.';
$tmp_report_user_hint = $tmp_report.'пользователем.';
$tmp_result = 'Результат';
$tmp_room = 'Комната';
$tmp_room_count_threads = 'нитей живо, было, архив';
$tmp_room_count_posts = 'рисунков, описаний';
$tmp_room_default = 'Основа';
$tmp_room_thread_cap = 'В этой комнате достигнут предел начатых нитей.';
$tmp_room_thread_cap_hint = 'Сейчас начинать новые невозможно, но попытайтесь позже.';
$tmp_room_types_select = 'Показать';
$tmp_room_types_hint = 'Виды комнат';
$tmp_room_types_name_example = 'например';
$tmp_room_types_names = array(
	'single_letter'	=> 'Однобуквенные комнаты — одностраничный архив и нет жалоб и модерации'
,	'hidden'	=> 'Скрытые комнаты не показаны, начинаются с точки'
);
$tmp_room_types_title = array(
	'all' => 'Все'
,	'1dpd' => 'Слепой телефон'
,	'simd' => 'Слепой вариант'
,	'draw' => 'Слепая история'
,	'text' => 'Глухая история'
,	'1trd' => 'Свалка'
);
$tmp_room_types = array(
	'1dpd' => 'один рисунок на каждое описание'
,	'simd' => 'несколько рисунков под одним описанием (темой) в каждой нити'
,	'draw' => 'только рисунки, без описаний, как "продолжи историю"'
,	'text' => 'только текст, без рисунков, как "продолжи историю" снова'
,	'1trd' => 'одна активная нить на комнату, что угодно вперемешку'
);
$tmp_rooms = 'Комнаты';
$tmp_rooms_hint = 'Не более '.ROOM_NAME_MAX_LENGTH.' букв.';
$tmp_filter_placeholder =
$tmp_rooms_placeholder = 'Можно вписать сюда часть имени для отсева списка.';
$tmp_rooms_submit = 'Перейти';
$tmp_rules = array(
	'rules' => array(
		'head' => 'Правила'
	,	'list' => array(
			'Игра в рисование в параллелях по очереди для любого количества человек.'
		,	'Цель — весело провести время. Стремитесь вызвать интерес, не проблемы.'
		,	'Сайт не гарантирует хранение всего, что можно прислать.'
		)
	)
,	'works' => array(
		'head' => 'Механика'
	,	'list' => array(
			'В качестве задания в случайном порядке выдаётся последний пост, кроме своих, или предложение начать новую нить.
На описание даётся '.TARGET_DESC_TIME.'s, на рисунок — '.TARGET_DRAW_TIME.'s, спустя которые ваше задание могут отобрать другие люди.
Если ещё не отобрали, или уже бросили, ваш пост всё ещё попадёт в цель.
В случае промаха рисунок создаёт новую нить с копией задания, а описание — просто начинает новую.'
		,	'Задание можно пытаться менять раз в '.TARGET_CHANGE_TIME.'s (или хоть сразу, если пустое), зайдя или обновив комнату.
Не открывайте одну и ту же комнату в нескольких вкладках, на стороне сайта хранится одна цель на комнату и будет изменена.
Если спустя какое-то время или после действий в комнате всё же решите выполнить задание, но отключили автопроверку в настройках, проверьте его кнопкой с таймером (справа). Эта проверка выполняется автоматически при отправке поста.
Замечание: если присутствует сообщение в [report|красной плашке] наверху или в комнате вручную выбран вариант рисовалки, обновление комнаты на месте (например, клавишей F5) не меняет цель. Клик по ссылке на комнату наверху сбрасывает этот эффект.'
		,	'Нити лежат полными при '.TRD_MAX_POSTS.' рисунках ещё '.TRD_ARCH_TIME.'s (для возможности жалоб или правок), после чего идут в архив при создании очередной новой нити.
Однобуквенные комнаты держат в архиве только 1 страницу (не более '.TRD_PER_PAGE.' нитей), лишены жалоб и модерации, а полные нити сразу идут в архив.'
		)
	)
,	'data' => array(
		'head' => 'Личные данные'
	,	'list' => array(
			'Никаких реальных личных данных для использования сайта не требуется.'
		,	'Если вы добавите свой адрес электронной почты, он будет использован только по требованию, например для восстановления входа в систему.'
		,	'Этот сайт хранит IP-адреса участников, которые никогда не удаляются без личного требования.
Эта техническая информация может применяться для запрета доступа вредителям, и никогда не даётся кому-либо, если того не требует закон.'
		,	'Этот сайт использует {https://'.$lang.'.wikipedia.org/wiki/HTTP_cookie|файлы-куки} в вашем браузере (программе для просмотра интернета) только для:
- входа пользователя,
- настроек пользователя,
- запоминания пропущенных заданий.
Сайт получает эти данные автоматически вместе с каждым запросом, и они не передаются за его пределы.'
		,	'Этот сайт использует {https://'.$lang.'.wikipedia.org/wiki/Web_Storage|локальное хранилище} в вашем браузере только для:
- хранения настроек рисовалки,
- хранения рисунков для восстановления,
- хранения списков подпапок архивов для дополнительного удобства листания.
Сайт не получает эти данные, и они всегда остаются в пределах вашего браузера.'
		)
	)
);
$tmp_sending = 'Отправка идёт, подождите...';
$tmp_spam_trap = 'Оставьте это пустым.';
$tmp_stop_all = 'Игра заморожена.';
$tmp_submit = 'Отправить';
$tmp_target_status = array(
	'no_room'	=> 'Эта комната переименована или удалена'
,	'no_task'	=> 'Ваше задание пусто'
,	'task_let_go'	=> 'Это задание взято кем-то другим'
,	'task_owned'	=> 'Это ваше задание'
,	'task_reclaim'	=> 'Это задание было брошено, теперь ваше'
);
$tmp_time_limit = 'Новый срок';
$tmp_time_units = array(
/*	31536000	=> array('год', 'года', 'лет')
,	86400		=> array('день', 'дня', 'дней')
,*/	3600	=> array('час', 'часа', 'часов')
,	60	=> array('минута', 'минуты', 'минут')
,	0	=> array('секунда', 'секунды', 'секунд')
);
$tmp_title = 'Слепой телефон';
$tmp_took = ', ушло %s сек.';
$tmp_user = 'Профиль пользователя';
$tmp_user_about = 'О себе';
$tmp_user_email = $tmp_options_email;
$tmp_user_name = 'Псевдоним';
$tmp_welcome_parts = array(
	'header'=> 'Как играть:'
,	'footer'=> 'Присоединяйтесь или уходите в любое время. Игра не имеет конца.'
,	'head'	=> 'нить'
,	'tail'	=> 'и т.д.'
,	'you'	=> array(
		'who'		=> 'вы'
	,	'desc_see'	=> 'Получив описание'
	,	'desc_do'	=> 'рисуете'
	,	'pic_see'	=> 'Получив картинку'
	,	'pic_do'	=> 'описываете'
	)
,	'other'	=> array(
		'who'		=> 'кто-то ещё'
	,	'desc_see'	=> 'Получает описание'
	,	'desc_do'	=> 'рисует'
	,	'pic_see'	=> 'Получает картинку'
	,	'pic_do'	=> 'описывает'
	)
);

?>
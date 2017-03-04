<?php

//* Bootstrap *----------------------------------------------------------------

define(T0, end($t = explode(' ',microtime())));
define(M0, $t[0]);
define(ME, 'me');
//define(ME_VAL, isset($_POST[ME]) ? $_POST[ME] : (isset($_COOKIE[ME]) ? $_COOKIE[ME] : ''));
define(ME_VAL, $_POST[ME] ?? $_COOKIE[ME] ?? '');	//* <- don't rely on $_REQUEST and EGPCS order; also ?? is only since PHP7
define(POST, 'POST' == $_SERVER['REQUEST_METHOD']);

header('Cache-Control: max-age=0; must-revalidate; no-cache');
header('Expires: Mon, 12 Sep 2016 00:00:00 GMT');
header('Pragma: no-cache');
if (function_exists($f = 'header_remove')) $f('Vary');

if ($_REQUEST['pass']) goto after_posting;		//* <- ignore spam bot requests
if (POST) {
	if (!ME_VAL) goto after_posting;		//* <- ignore anonymous posting
	ignore_user_abort(true);
}

define(NAMEPRFX, 'd');
define(GET_Q, strpos($_SERVER['REQUEST_URI'], '?'));
define(NGINX, stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false);
define(ROOTPRFX, substr($s = $_SERVER['PHP_SELF'] ?: $_SERVER['SCRIPT_NAME'] ?: '/', 0, strrpos($s, '/')+1));

//* source: http://php.net/security.magicquotes.disabling#91653
if (function_exists($f = 'get_magic_quotes_gpc') && $f()) {
	function strip_magic_slashes(&$value, $key) {$value = stripslashes($value);}
	$gpc = array(&$_COOKIE, &$_GET, &$_POST, &$_REQUEST, &$_SESSION);
	array_walk_recursive($gpc, 'strip_magic_slashes');
}

//* clean up included output, like BOMs:
ob_start();

require(NAMEPRFX.'.cfg.php');
foreach ($cfg_dir as $k => $v) define('DIR_'.strtoupper($k), $v.'/');
mb_internal_encoding(ENC);

require(NAMEPRFX.'.fu.php');
require(NAMEPRFX.'.db.php');
if (!POST) {
	if ($r = $_SERVER['HTTP_REFERER']) data_log_ref($r);
	if (
		($r = $_SERVER['HTTP_USER_AGENT'])
	&&	preg_match_all('~(?<=^|\s|\+)(\w+:/+\S+)~i', $r, $m)
	) {
		foreach ($m[1] as $v) {
			if (false === strpos($v, '(')) $v = trim($v, ')');
			if ($v = trim($v)) data_log_ref("$v#(user-agent)");
		}
	}
}

//* UI Translation *-----------------------------------------------------------

//* source: http://www.dyeager.org/blog/2008/10/getting-browser-default-language-php.html
if (isset($_SERVER[$h = 'HTTP_ACCEPT_LANGUAGE'])) {
	$a = array();
	foreach (explode(',', $_SERVER[$h]) as $v) if (preg_match('~(\S+);q=([.\d]+)~ui', $v, $m)) {
		$a[$m[1]] = (float)$m[2];
	} else $a[$v] = 1.0;
//* check for highest q-value. No q-value means 1 by rule
	$q = 0.0;
	foreach ($a as $k => $v) if ($v > $q && in_array($l = strtolower(substr($k, 0, 2)), $cfg_langs)) {
		$q = (float)$v;
		$lang = $l;
	}
}
require(NAMEPRFX.".cfg.$lang.php");

function time_check_point($comment) {global $tcp; $tcp[microtime()][] = $comment;}
time_check_point('done cfg, inb4 user settings');

ob_end_clean();

//* User settings *------------------------------------------------------------

//* keep legacy formats until cookies expire in 3 years:
$u_opts = array();
$opt_sufx = 'aoprui';
$opt_name = array('opta', 'opti', 'trd_per_page', 'draw_max_recovery', 'draw_max_undo', 'draw_time_idle');
$opt_lvls = array('a' => 'admin', 'i' => 'check');

if (ME_VAL && ($me = URLdecode(ME_VAL))) {
	if (false === strpos($me, '/')) {
//* v1, one separator for all, like "hash_etc_etc":
		list($u_qk, $u_opti, $u_trd_per_page, $u_room_default) = explode('_', $me, 4);
	} else
	if (false === strpos($me, ':')) {
//* v2, plain ordered numbers, like "hash/vvv_val_val/etc":
		list($u_qk, $i, $u_room_default, $u_draw_app) = explode('/', $me, 4);
		if (!preg_match_all('~(\d+)([a-z])~', strtolower($i), $m)) {
			list($u_opti, $u_trd_per_page, $u_draw_max_undo) = explode(false !== strpos($i, '_')?'_':'.', $i);
		} else {
//* v3, abbreviated suffixes for middle part, like "01adm_0010opt_30per_page_99undo", or "0a1o2p3u", in any order:
			foreach ($m[1] as $k => $v) if (false !== ($i = strpos($opt_sufx, $m[2][$k]))) ${'u_'.$opt_name[$i]} = $v;
		}
	} else {
//* v4, key prefixes for all, like "hash/key:val/key:val/etc:etc", in any order:
		$a = explode('/', $me);
		$u_qk = array_shift($a);
		$key_value_pairs = $a;
	}
	if (($reg = trim($_POST[ME])) && $u_qk != $reg) $u_qk = $reg;
	if ($u_qk && data_check_u($u_qk, $reg)) {
		if (LOG_IP) data_log_ip();
		if ($u_flag['ban']) die(get_template_page(array(
			'title' => $tmp_ban
		,	'task' => $tmp_ban
		,	'body' => 'burnt-hell'
		)));

		if (!$u_flag['god']) unset($cfg_opts_order['admin'], $tmp_options_input['admin']);
		if ($key_value_pairs) {
			$i = 'input';
			foreach ($a as $key_value_pair) {
				$b = explode(':', $key_value_pair);
				$k = reset($b);
				$v = end($b);
				if (strlen($v)) {
					if (count($b > 2) && $b[1] == 'base64') $v = base64_decode($v);
					if (array_key_exists($k, $cfg_opts_order[$i])) {
						${'u_'.$cfg_opts_order[$i][$k]} = $v;
					} else
					if (array_key_exists($k, $cfg_opts_order) && $k !== $i) {
						$y = (false !== strpos($v, '.') ? explode('.', $v) : str_split($v));
						foreach ($cfg_opts_order[$k] as $v) if (in_array(abbr($v), $y)) $u_opts[$v] = 1;
					}
				}
			}
		} else {
			foreach ($opt_lvls as $i => $a) if ($p = ${"u_opt$i"})
			foreach ($cfg_opts_order[$a] as $k => $v) if ($x = intval($p[$k])) $u_opts[$v] = $x;
		}
		if (POST) $post_status = 'user_qk';
	}
}
define(GOD, !!$u_flag['god']);
define(TIME_PARTS, !POST && GOD && !$u_opts['time_check_points']);	//* <- profiling
if (TIME_PARTS) time_check_point('GOD defined, inb4 room anno check'); else unset($tcp);

//* Location routing *---------------------------------------------------------

$q = $_SERVER['REQUEST_URI'];
if (false !== GET_Q) $q = substr($q, 0, GET_Q);
$q = substr($q, strlen(ROOTPRFX));
$q = preg_split('~/+~', $q, 3, PREG_SPLIT_NO_EMPTY);
list($qdir, $qroom, $etc) = $q;

$qredir = ($qdir = strtolower($qdir)).'s';
foreach ($cfg_dir as $k => $v) if ($qdir == $v) {${"qd_$k"} = 1; break;}

$query = array();
if (false !== GET_Q && ($s = substr($_SERVER['REQUEST_URI'], GET_Q+1))) {
	foreach (explode('&', $s) as $chain) {
		$a = explode('=', $chain);
		$v = (count($a) > 1 ? array_pop($a) : '');
		foreach ($a as $k) $query[URLdecode($k)] = URLdecode($v);
	}
}
if ($qdir) {
	if ($l = mb_strlen($room = trim_room($room_in_url = URLdecode($qroom)))) {
		define(R1, $l = (mb_strlen(ltrim($room, '.')) <= 1));
	}
} else {
	if ($u_key && !$u_room_default) $qd_opts = 1;	//* <- game root page
	if (GOD && !NGINX && strlen(trim(ROOTPRFX)) && substr($query['do'],0,3) !== 'hta') rewrite_htaccess();
}
define(MOD, GOD || $u_flag['mod'] || $u_flag["mod_$room"]);
define(FROZEN_HELL, data_global_announce('stop'));	//* <- after $room is defined

if (FROZEN_HELL && !(GOD || $qd_arch || ($qd_opts && $u_key))) {
	if (POST) goto after_posting;
	if (FROZEN_HELL < 0) die(
		$etc == '-'
		? $tmp_stop_all
		: get_template_page(array(
			'title' => $tmp_stop_all.' '.$tmp_title.'.'
		,	'task' => $tmp_stop_all
		,	'anno' => 1
		,	'header' => array(
				'<a href="'.ROOTPRFX.'">'.$tmp_title.'.</a>'
			, 'r' =>
				'<a href="'.ROOTPRFX.DIR_ARCH.'">'.$tmp_archive.'.</a>'.NL.
				'<a href="'.ROOTPRFX.DIR_OPTS.'">'.$tmp_options.'.</a>'
			)
		))
	);
}

if (TIME_PARTS) time_check_point('MOD defined, inb4 action fork');
if (POST) goto posting;




//* GET: view some page content *----------------------------------------------

$page = array();

//* mod panel -----------------------------------------------------------------

if (GOD && (
	($q = isset($query['mod']))
//||	($etc && strlen(trim($etc, '-')))
)) {
	$a = array_keys($tmp_mod_pages);
	if ($q) {
		$q = $query['mod'] ?: reset($a);
		$do = $query['do'];
		$i = intval($query['id']);
	} else {
		$q = $a[intval($etc)-1];
		if ($i = strpos($etc, '-')) {
			$i = intval(substr($etc, $i+1));
			$a = array_keys($tmp_mod_files);
			$do = $a[$i-1];
		}
	}
	if ($q === 'users' && $i) {
		die(get_template_page(array(
			'title' => $tmp_mod_pages[3].': #'.$i
		,	'content' => (
				($a = data_get_user_info($i))
				? array_merge(array(
					'Current date' => date(TIMESTAMP, T0)
				,	'User ID' => $i
				), $a)
				: $tmp_empty
			)
		,	'listing' => ':'.NL
		)));
	}
	$lnk = $t = '';
	$mod_title = $mod_page = $tmp_mod_pages[$q] ?: $tmp_empty;

	if ($q === 'logs') {
		$day = $query['day'] ?: $etc;
		$ymd = preg_match(PAT_DATE, $day, $m);
		if ($l = data_get_mod_log()) {
			$page['data']['content']['type'] = 'reports';
			$page['content'] = '
day_link = .?mod=logs&day=';
			$last = end(end($l));
			$last = data_get_mod_log($last_ymd = key($l).'-'.$last, 1);
			if (!$day) $day = $ymd = $last_ymd;
			if ($ymd) {
				exit_if_not_mod(data_get_mod_log($day, 1));
				$mod_title = "$mod_page: $day";
				if ($a = data_get_mod_log($day)) {
					$attr = ' data-day="'.$day.'"';
					$page['content'] .= '
images = '.ROOTPRFX.DIR_PICS.'
rooms = '.ROOTPRFX.DIR_ROOM.'
flags = a
'.$a;
				}
			} else {
				exit_if_not_mod($last);
			}
			foreach ($l as $ym => $d) $lnk .= ($lnk?'</p>':'').'
<p>'.$ym.'-'.implode(',', $d);
			$lnk .= ' <small>'.date('H:i:s', $last).'</small></p>';
		}
	} else
	if ($q === 'files') {
		$a = array(
			'opcache_check' => 'opcache_get_status'
		,	'opcache_reset' => 'opcache_reset'
		,	'opcache_inval' => 'opcache_invalidate'
		);
		foreach ($a as $k => $v) if (!function_exists($v)) unset($tmp_mod_files[$k]);
		foreach ($tmp_mod_files as $k => $v) $lnk .= '
<li><a href=".?mod='.$q.'&do='.$k.'">'.str_replace_first(' ', '</a> ', $v).'</li>';
		$lnk = '
<ul>'.indent($lnk).'</ul>';
		if (!$do) $do = end(array_keys($tmp_mod_files));
		if ($do && array_key_exists($do, $tmp_mod_files)) {
			if ($a = $tmp_mod_files[$do]) $lnk .= '
<p>'.rtrim($a, ':.').':</p>';
			ignore_user_abort(true);
			data_lock($lk = 'mod_panel');
if (TIME_PARTS) time_check_point('ignore user abort');
			if ($do === 'list') {
				$len = array(0,0,0);
				$dirs = array();
				$files = array();
				foreach (get_dir_contents() as $f) {
					$d = is_dir($f);
					$m = date(TIMESTAMP, filemtime($f));
					$s = ($d ? 'DIR' : filesize($f).' B');
					$a = array($f, $s, $m);
					foreach ($len as $k => &$v) $v = max($v, strlen($a[$k]));
					${$d?'dirs':'files'}[] = $a;
				}
				foreach (array_merge($dirs, $files) as $a) {
					foreach ($len as $k => $v) {
						$s = strlen($a[$k]);
						if ($s < $v) $a[$k] .= str_repeat(' ', $v-$s);
					}
					$t .= implode('	', $a).NL;
				}
			} else
			if ($do === 'opcache_check') {
				if (is_array($a = opcache_get_status())) {
					if (array_key_exists($k = 'scripts', $a) && is_array($b = &$a[$k])) ksort($b);
					$t = print_r($a, true);
				} else $t = $a;
			} else
			if ($do === 'opcache_reset') {
				$t = opcache_reset();
			} else
			if ($do === 'opcache_inval') {
				$t = implode(NL, array_filter(array_map(
					function($f) {
						return get_file_ext($f) === 'php' ? "$f\t".opcache_invalidate($f) : '';
					}
				,	get_dir_contents()
				)));
			} else
			if ($do === 'arch') {
				require_once(NAMEPRFX.'.arch.php');
				$t = data_archive_rewrite();
			} else
			if ($do === 'room_list_reset') {
				$t = data_post_refresh(true);
			} else
			if ($do === 'img2subdir') {
				foreach (get_dir_contents($d = DIR_PICS) as $f) if (is_file($old = $d.$f)) {
					$new = get_pic_subpath($f, 1);
					$t .=
NL.(++$a)."	$old => $new	".($old === $new?'same':(rename($old, $new)?'OK':'fail'));
				}
if (TIME_PARTS && $a) time_check_point("done $a pics");
			} else
			if ($do === 'nginx') {
				$last = 0;
				$a = array();
				foreach (get_dir_contents() as $f) if (is_file($f) && stripos($f, $do) !== false) {
					$last = max($last, filemtime($f));
					$a[] = $f;
				}
				if ($last) exit_if_not_mod($last);
				foreach ($a as $f) if (strlen($x = trim(file_get_contents($f)))) {
					if (preg_match_all('~\$([_A-Z][_A-Z\d]*)~', $x, $match)) foreach (array_unique($match[1]) as $k) {
						if ($v = get_const($k) ?: $_SERVER[$k]) $x = str_replace('$'.$k, $v, $x);
					}
					$t .= "# Example: $f #
$x
# End of example. #";
				}
			} else
			if (substr($do, 0,3) === 'hta') {
				$t = rewrite_htaccess(substr($do, -5) === 'write');
			} else {
				$t = data_fix($do);
			}
			if (!$t) $t = $tmp_no_change;
			data_unlock($lk);
		}
	} else
	if (substr($q,0,4) === 'vars') {
		exit_if_not_mod();				//* <- never exits, to check HTTP_IF_MODIFIED_SINCE, etc
		$sort = (substr($q, -4) === 'sort');
		$headers = headers_list();
		$t =	'strip magic slashes = '.($gpc?'on':'off').NL
		.	'DATE_RFC822 = '	.gmdate(DATE_RFC822, T0).NL
		.	'DATE_RFC2822 = '	.gmdate('r', T0).NL
		;
		foreach (explode(',', 'headers,qroom,room_in_url,room,etc,_COOKIE,_ENV,_GET,_POST,_SERVER,_SESSION') as $k) if ($$k) {
			if ($sort && is_array($$k)) {
				if (isset($$k[0])) natsort($$k);
				else ksort($$k);
			}
			if ($v = trim(print_r($$k, true))) {
				$v = fix_encoding($v);
				$e = end($fix_encoding_chosen);
				$t .= "$k: $e = $v".NL;
			}
		}
	} else
	if ($q && array_key_exists($q, $tmp_mod_pages)) {
		data_lock($q, false);
		exit_if_not_mod(data_get_mod_log($q, 1));
		if ($t = data_get_mod_log($q)) {
			if ($q === 'users') {
				$page['content'] .= "
left_link = .?mod=users&id=
left = $tmp_mod_user_info
right = $tmp_mod_user_hint
flags = cgu
v,$u_num,u	v
$t";
			} else
			if ($q === 'reflinks') {
				$page['content'] .= "
flags = c
$t";
			}
			$page['data']['content']['type'] = $q;
			$lnk .= get_template_form(array('filter' => 1));
		}
		data_unlock($q);
	}
	if ($page['content'] || ($page['textarea'] = $t) || $lnk) {
		if ($page['content'] || $lnk) {
			$page['js']['mod']++;
			$page['js'][0]++;
		}
	} else $lnk = $tmp_empty;
	$page['task'] = "
<p$attr>$mod_page:</p>$lnk";
} else

//* archived threads ----------------------------------------------------------

if ($qd_arch) {
	require_once(NAMEPRFX.'.arch.php');
	$search = data_get_archive_search_terms();

//* archive threads list ------------------------------------------------------

	if ($room && ($thread_count = data_get_archive_count())) {
		exit_if_not_mod(data_get_archive_mtime());

		if (!$search) {
			$start = 0;
			$page['content'] = '
images = '.DIR_THUMB.'
image_ext = '.THUMB_EXT.'
page_ext = '.PAGE_EXT.'
on_page = '.(!R1 ? ($u_trd_per_page ?: TRD_PER_PAGE) : TRD_PER_PAGE.'
start = '.($start = max(0, $thread_count - TRD_PER_PAGE))).'
total = '.$thread_count.($u_key?'':'
last = <a href="'.$thread_count.'.htm">'.$thread_count.'</a><!-- static link for scriptless bots -->');
			$page['head'] = '
<link rel="prev" href="'.($start+1).PAGE_EXT.'">
<link rel="next" href="'.$thread_count.PAGE_EXT.'">';
			$page['data']['content']['type'] = 'archive pages';
		}
	} else

//* archive rooms list --------------------------------------------------------

	if ($visible = data_get_visible_archives()) {
		exit_if_not_mod($visible['last']);

		if (!$search) {
			if ($c = !$u_opts['count']) $page['content'] = "
$tmp_arch_last	$tmp_arch_count";
			foreach ($visible['list'] as $room => $n) $page['content'] .= ($c ? "
$n[last]	$n[count]	$room" : NL.NB.'	'.NB.'	'.$room);
			$room = '';
			$page['data']['content']['type'] = 'archive rooms';
		}
	} else $search = 0;

//* archive posts search ------------------------------------------------------

	if (is_array($search)) {
		$t = array('r' => sprintf(sprintf($tmp_regex_hint, HINT_REGEX_LINK, HINT_REGEX_FORMAT), $lang, $tmp_regex_hint_pat));
		if (!($search || $room)) $t[''] = $tmp_archive_hint;
		$page['task'] = get_template_form(
			array(
				'head' =>	$tmp_archive
			,	'select' =>	$tmp_archive_find_by
			,	'submit' =>	$tmp_archive_find
			,	'hint' =>	$t
			,	'min' =>	FIND_MIN_LENGTH
			)
		);
		if ($search) {
			$research = '';
			foreach ($search as $k => $v) {
				$t = $tmp_archive_find_by[$k];
				$t = $t['found by'] ?: $t['select'];
				$research .=
					($research?',':'')
				.	NL
				.	'<a name="'.$k.'">'
				.	($t ? "$t: " : '')
				.		'<span>'
				.			htmlspecialchars($v)
				.		'</span>'
				.	'</a>';
			}
			$page['task'] .= '
<p class="hint" id="research">'.indent($tmp_archive_found.$research).'</p>';
			if ($found = data_archive_find_by($search)) {
				$page['content'] = '
page_ext = '.PAGE_EXT.get_flag_vars(
					array(
						'flags' => array(
							'ac', array(
								1
							,	!$u_opts['count']
							)
						)
					,	'caps' => 0
					)
				).NL.$found;
				$page['data']['content']['type'] = 'archive found';
			}
		}
		$page['js'][0]++;
	}
	if (!$page['content']) $page['task'] .= $tmp_empty;
} else

//* draw test -----------------------------------------------------------------

if (($qd_opts || !$qdir) && GET_Q) {
	$qd_opts = 2;
	$n = get_draw_app_list(false);
	$page['icon'] = $n['name'];
	$page['task'] = '
<p>'.$tmp_draw_free.':</p>
<p class="hint">'.$tmp_draw_hint.'</p>'.$n['noscript'];
	$page['subtask'] = $n['embed'].'
<div class="task">'.indent('<p class="hint">'.indent($n['list']).'</p>').'</div>';
} else

if ($u_key) {

//* options -------------------------------------------------------------------

	if ($qd_opts) {
		$page['data']['content']['type'] = 'options';
		$s = '|';
		$t = ':	';
		$draw_app = implode(';', array(
			(array_search($u_draw_app, $cfg_draw_app) ?: 0)
		,	implode($s, $tmp_draw_app)
		,	implode($s, $cfg_draw_app)
		,	DRAW_APP_NONE
		,	'?draw_app=*'
		));
		$c = $d = '';
		foreach ($tmp_options_input as $i => $o)
		foreach ($o as $k => $l) {
			$r = abbr($k).'='.(
				$i === 'input'
				? ($$k ?: '='.(${'u_'.$k} ?: get_const(strtoupper($k))))
				: ($u_opts[$k]?1:'')
			);
			if ($i === 'admin') $l = '<span class="gloom">'.$l.'</span>';
			$c .= NL.$l.$t.$r;
		}
		$i = '
|<input type="submit" value="';
		$j = '
|<input type="button" value="';
		foreach (array(
			'out'	=> array($i, 'name="quit')
		,	'save'	=> array($j, 'id="unsave" data-keep="'.DRAW_PERSISTENT_PREFIX)
		,	'skip'	=> array($j, 'id="unskip')
		,	'pref'	=> array($i, 'name="'.O.'o')
		) as $k => $v) $d .= $v[0].$tmp_options_drop[$k].'" '.$v[1].'">';
		if (!$qdir) {
			if (GOD && NGINX) $page['content'] .= vsprintf('
||<b class="anno report">%s<br><a href=".?mod=files&do=nginx">%s</a></b>', $tmp_options_warning);
			$page['content'] .= '
||<b class="anno">'.$tmp_options_first.'</b>';
		}
		$page['content'] .= '
<form method="post">'.$d.'
</form><form method="post">'
.NL.$tmp_options_name.$t.$usernames[$u_num]
.NL.$tmp_options_qk.$t.'<input type="text" readonly value="'.$u_key.'" title="'.$tmp_options_qk_hint.'">
separator = '.$s.$c
.NL.$tmp_options_time.$t.date('e, T, P')
.NL.$tmp_options_time_client.$t.'<time id="time-zone"></time>'
.($u_flag ? NL.$tmp_options_flags.$t.implode(', ', $u_flag) : '')
.$i.$tmp_options_apply.'" id="apply">
</form>';
		$hid = ($qdir?' class="hid"':'');
		foreach ($tmp_rules as $head => $hint) {
			if (is_array($hint)) {
				$s = '';
				foreach ($hint as $i) $s .= NL.'<li>'.indent(get_template_hint($i)).'</li>';
				$s = NL."<ul$hid>".indent($s).'</ul>';
			} else	$s = NL.'<p class="hint">'.indent(get_template_hint($hint)).'</p>';
			$page['task'] .= NL."<p>$head</p>$s";
		}
		$page['js'][0]++;
	} else

//* rooms ---------------------------------------------------------------------

	if ($qd_room) {
		if ($room != $room_in_url) {
			$room = (strlen(trim($room, '.')) ? URLencode($room).'/'.$etc : '');
			header('HTTP/1.1 303 Fixed room name');
			header("Location: $room_list_href$room");
			exit;
		}
		if ($room) {
			if (FROZEN_HELL) {
				$page['task'] = $tmp_stop_room ?: $tmp_stop_all;
				goto template;
			}

//* task manipulation, implies that $room is set and fixed already ------------

			if (strlen($v = $query['report_post'])) $etc = $v; else
			if (strlen($v = $query['skip_thread'])) $etc = '-'.abs(intval($v)); else
			if (strlen($v = $query['check_task'])) {
				if ($v == 'keep' || $v == 'prolong') $etc = '-'; else
				if ($v == 'post' || $v == 'sending') $etc = '--';
			}
			if ($etc) {
				if ($etc[0] == '-') {
			//* show current task:
					$sending = (strlen($etc) > 1);
					if (!strlen(trim($etc, '-'))) {
						$t = data_check_my_task();
						die(
							'<!--'.date(TIMESTAMP, T0).'-->'
							.NL.'<meta charset="'.ENC.'">'
							.NL.'<title'.(
								is_array($t)
								? '>'.(
									$sending
									? $tmp_sending
									: $tmp_target_status[$t[0]].'. '.$tmp_time_limit.': '.format_time_units($t[1])
								)
								: (
									$sending
									? ' id="confirm-sending"'
									: ''
								).'>'.$tmp_target_status[$t]
							).'</title>'
							.NL.(
								($t = $target['task']) && $target['pic']
								? '<img src="'.get_pic_url($t).'" alt="'.$t.'">'
								: $t
							)
						);
					}
			//* skip current task, obsolete way by GET:
					$t = substr($etc, 1);
					list($a, $r) = get_room_skip_name($room);
					if ($q = get_room_skip_list($a)) {
						array_unshift($q, $t);
						$t = implode('/', $q);
					}
					$add_qk = "$a=$r/$t";
					$post_status = 'skip';
					goto after_posting;
				}

//* report form ---------------------------------------------------------------

				$page['task'] = get_template_form(
					array(
						'method' =>	'post'
					,	'name' =>	'report'
					,	'min' =>	REPORT_MIN_LENGTH
					,	'max' =>	REPORT_MAX_LENGTH
					,	'textarea' =>	($is_report_page = 1)
					,	'checkbox' =>	array(
							'name' => 'freeze'
						,	'label' => $tmp_report_freeze
						)
					)
				);
			} else {

//* active room task and visible content --------------------------------------

				foreach ($query as $k => $v) if (substr($k, 0, 4) == 'draw') {
					$draw_query = 1;
					break;
				}
				$y = $query['!'];
				$dont_change = ($draw_query || trim(str_replace(array('trd_arch', 'trd_miss'), '', $y), '&'));
				$skip_list = get_room_skip_list();

if (TIME_PARTS) time_check_point('inb4 aim lock');
				data_aim(
					!$u_opts['unknown']
				,	$skip_list
				,	$dont_change		//* <- after POST with error
				);
				$visible = data_get_visible_threads();
				data_unlock();
if (TIME_PARTS) time_check_point('got visible data, unlocked');

				exit_if_not_mod(max($t = $target['time'], $visible['last']));
				$task_time = ($t ?: T0);	//* <- UTC seconds
				$x = 'trd_max';
				if ($draw_query && !$target['task'] && (!$y || false === strpos($y, $x))) {
					if (data_is_thread_cap()) $query['!'] = ($y?$y.'!':'').$x;
					else $draw_free = 1;
				}
				$desc = ($target['pic'] || !($target['task'] || $draw_free));
				if ($vts = $visible['threads']) {
					$t = (
						MOD ? "
left = $tmp_mod_post_hint
right = $tmp_mod_user_hint"
						: (R1 || $u_flag['nor'] ? '' : "
left = $tmp_report_post_hint
right = $tmp_report_user_hint"
						)
					).'
images = '.ROOTPRFX.DIR_PICS.get_flag_vars(
						array(
							'flags' => array(
								'acgmp', array(
									$u_opts['active']
								,	!$u_opts['count']
								,	GOD
								,	MOD
								,	PIC_SUB
								)
							)
						,	'caps' => 3
						)
					);
					$page['content'] = $t.NL;
					$a = array();
					$b = '<br>';
if (TIME_PARTS) time_check_point('inb4 raw data iteration'.NL);
					foreach ($vts as $tid => $posts) {
						$tsv = '';
						foreach ($posts as $postnum => $post) {
							if ($t = $post['time']) {
								if ($u_opts['times']) {
									$l = explode($b, $t, 2);
									$l[0] = NB;
									$l = implode($b, $l);
								} else $l = $t;
							} else $l = NB;
							if ($t = $post['user']) {
								$r = explode($b, $t, 2);
								$uid = $r[0];
								$r[0] = (
									!$u_opts['names'] && array_key_exists($uid, $usernames)
									? $usernames[$uid]
									: NB
								);
								$r = implode($b, $r);
							} else $r = NB;
							$tabs = array(
								'color' => $u_opts['own']?0:$post['flag']
							,	'time' => $l
							,	'user' => $r
							,	'content' => $post['post']
							);
							if ($t = $post['used']) $tabs['used'] = $t;
							if (GOD) {
								$tabs['color'] .= '#'.$uid;
								if ($t = $post['browser']) $tabs['browser'] = $t;
							}
							if (is_array($r = $visible['reports'][$tid][$postnum])) {
								foreach ($r as $k => $lines) {
									$k = 'reports_on_'.($k > 0?'user':'post');
									$v = '';
									foreach ($lines as $time => $line) $v .= ($v?'<br>':'').$time.': '.$line;
									if ($v) $tsv .= NL.$k.' = '.$v;
								}
							}
							$tsv .= NL.(
								$postnum > 0 || $u_flag['nor'] || (R1 && !MOD)
								? ''
								: end(explode('/', $tid)).','
							).implode('	', $tabs);
						}
						$a[$tid] = $tsv;
if (TIME_PARTS) time_check_point('done trd '.$tid);
					}
					ksort($a);
					$page['content'] .= implode(NL, array_reverse($a));
if (TIME_PARTS) time_check_point('after sort + join');
					if (GOD) $filter = 1;
				} else if (GOD) $page['content'] = "
left = $tmp_empty
right = $tmp_empty
flags = vg

0,0	v	v";	//* <- dummy thread for JS drop-down menus
				if (MOD && $page['content']) $page['js']['mod']++;

				$t = $target['task'];
				if ($desc) {
					$page['task'] = get_template_form(
						array(
							'method' =>	'post'
						,	'name' =>	'describe'
						,	'min' =>	DESCRIBE_MIN_LENGTH
						,	'head' =>	$t ? $tmp_describe_this : $tmp_describe_new
						,	'hint' =>	$tmp_describe_hint.($u_flag['nop'] ? '\\'.$tmp_no_play_hint : '')
						,	'filter' =>	$filter
						,	'checkbox' => (
								$u_opts['kbox']
								?  array(
									'label' => $tmp_check_required
								,	'required' => 1
								)
								: ''
							)
						)
					);
					if ($t) {
						$src = (strpos($t, ';') ? get_pic_resized_path(get_pic_normal_path($t)) : $t);
						$page['task'] .= '
<img src="'.get_pic_url($src).'" alt="'.$t.'">';
					} else {
						$task_time = '-';
						$s = count($skip_list);
						$n = $target['count_free_tasks'];
						if ($s && !$n) $page['data']['task']['unskip'] = "$s/$n/$target[count_free_unknown]";
					}
				} else {
					$n = get_draw_app_list(true);
					$page['task'] = '
<p>'.indent(
	$t
	? $tmp_draw_this.':
<span id="task-text">'.$t.'</span>'
	: $tmp_draw_free.':'
).'</p>';
					$hint = '
<p class="hint">'.indent($n['list']).'</p>';
					if ($x = $n['noscript']) {
						$page['task'] .= $x;
						$page['subtask'] = $n['embed'].'
<div class="task">'.indent($hint).'</div>';
					} else {
						$w = explode(',', DRAW_LIMIT_WIDTH);
						$h = explode(',', DRAW_LIMIT_HEIGHT);
						$page['task'] .= $n['embed'].'
<p class="hint">'.sprintf(
							$tmp_draw_limit_hint
						,	$w[0], $h[0]
						,	$w[1], $h[1]
						,	DRAW_MAX_FILESIZE
						,	format_filesize(DRAW_MAX_FILESIZE)
						,	strtoupper(implode(', ', $cfg_draw_file_types))
						).'</p>'.$hint;
					}
				}
				if ($t || $desc) $page['data']['task']['t'] = $task_time;
				if ($t) $page['data']['task']['skip'] = intval($target['thread']);
				$page['data']['content']['type'] = 'threads';
				$page['js'][0]++;
			}
		} else {

//* active rooms list ---------------------------------------------------------

			if ($visible = data_get_visible_rooms()) {
				exit_if_not_mod($visible['last']);

				$t = !$u_opts['times'];
				$c = !$u_opts['count'];
				$s = ', ';
				$page['content'] = "
archives = $arch_list_href
separator = \"$s\"
".($c?"
$tmp_room_count_threads	$tmp_room_count_posts":'');
				foreach ($visible['list'] as $room => $n) {
					$mid = $room;
					if ($u_flag["mod_$room"]) $mid .= "$s(mod)";
					if ($u_room_default === $room) $mid .= "$s(home)";
					if ($a = $n['marked']) foreach ($a as $k => $v) $mid .= "$s$v$k[0]";
					if ($c) {
						$left = $n['threads now'].$s.$n['threads ever'];
						if ($v = $n['threads arch']) $left .= $s.$v.($t ? $s.$n['last arch'] : '');
						$right = $n['pics'].$s.$n['desc'].($t ? $s.$n['last post'] : '');
					} else {
						$left = ($n['threads arch']?'*':NB);
						$right = NB;
					}
					$page['content'] .= "
$left	$right	$mid";
				//* announce/frozen:
					if ($a = $n['anno']) {
						if (!$a['room_anno']) $page['content'] .= '	';
						foreach ($a as $k => $v) $page['content'] .= "	$tmp_announce[$k]: ".trim(
							preg_replace('~\s+~u', ' ',
							preg_replace('~<[^>]*>~', '',
							preg_replace('~<br[ /]*>~i', NL,
						$v))));
					}
				}
				$room = '';
			}
			$page['task'] = get_template_form(
				array(
					'method' =>	'post'
				,	'name' =>	$qredir
				,	'min' =>	ROOM_NAME_MIN_LENGTH
				,	'filter' =>	2
				)
			);
			$page['data']['content']['type'] = 'rooms';
			$page['js'][0]++;
		}
	} else

//* home page substitute ------------------------------------------------------

	if (!$room) {
		$room = (strlen(trim($u_room_default, '.')) ? $u_room_default.'/' : '');
		header('HTTP/1.1 303 To home room');
		header("Location: $room_list_href$room");
		exit;
	}
} else {

//* not registered ------------------------------------------------------------

	if ($etc) die('x');
	foreach ($cfg_dir as $k => $v) unset(${'qd_'.$k});
	$page['task'] = get_template_form(
		array(
			'method' =>	'post'
		,	'name' =>	ME
		,	'min' =>	USER_NAME_MIN_LENGTH
		)
	);
}

//* generate page, put content into template ----------------------------------

template:

define(S, '. ');
$room_title = ($room == ROOM_DEFAULT ? $tmp_room_default : "$tmp_room $room");
$page['title'] = (
	$mod_title
	? "$tmp_mod_panel - $mod_title".S.(
		$ymd && $room
		? $room_title.S
		: ''
	)
	: (
		$qd_opts == 1
		? $tmp_options.S
		: (
			$qd_arch
			? (
				$room
				? $room_title.S
				: ''
			).$tmp_archive.S
			: (
				$qd_room
				? (
					$room
					? (
						$is_report_page
						? $tmp_report.S
						: ''
					).$room_title.S
					: $tmp_rooms.S
				)
				: ''
			)
		)
	)
).$tmp_title.(
	$qd_opts == 2
	? S.$tmp_options_input['input']['draw_app']
	: ''
);

if (!$is_report_page) {
	define(A, NL.'<a href="');
	$short = !!$u_opts['head'];
	$a_head = array(
		'/' => $tmp_title
	,	'..' => $tmp_rooms
	,	'.' => $room_title
	,	'a' => $tmp_archive
	,	'*' => $tmp_archives
	,	'?' => $tmp_options
	,	'~' => $tmp_draw_test
	,	'#' => '&#9662; '.$tmp_mod_panel
	);
	foreach ($a_head as $k => &$v) $v = '">'.(
		$short
		? $k
		: $v.(substr($v, -1) == '.'?'':'.')
	).'</a>';

	if (GOD) {
		define(M, A.'.?mod');
		foreach ($tmp_mod_pages as $k => &$v) $mod_list .= M.'='.$k.'">'.$v.'</a><br>';
		$mod_link =
			'<u class="menu-head">'.indent(
				M.$a_head['#'].NL.
				'<u class="menu-top">'.
				'<u class="menu-hid">'.
				'<u class="menu-list">'.indent(
					$mod_list
				).'</u></u></u>'
			).'</u>';
	}

	$this_href = ($room?'..':'.');
	$room_list_link = A.(DIR_DOTS && $qd_room ? $this_href : $room_list_href).$a_head['..'];
	$arch_list_link = (
		$qd_arch || is_dir(DIR_ARCH)
		? A.(DIR_DOTS && $qd_arch ? $this_href : $arch_list_href).$a_head['*']
		: ''
	);
	if ($room) {
		$room_link = A.(DIR_DOTS && $qd_room ? '.' : "$room_list_href$room/").$a_head['.'];
		$arch_link = (
			$qd_arch || is_dir(DIR_ARCH.$room)
			? A.(DIR_DOTS && $qd_arch ? '.' : "$arch_list_href$room/").$a_head['a']
			: ''
		);
	}
	$page['header'] = (
		$u_key
		? array(
			A.ROOTPRFX.$a_head['/']
		.	($short?$room_list_link:'')
		.	$room_link
		.	($short?'':$arch_link)
		, 'ar' =>
			$mod_link
		.	($short?$arch_link:'')
		.	$arch_list_link
		.	($short?'':$room_list_link)
		.	A.(DIR_DOTS && $qdir && $qd_opts?'.':ROOTPRFX.DIR_OPTS.($room?"$room/":'')).$a_head['?']
		)
		: array(
			A.ROOTPRFX.$a_head['/']
		.	A.ROOTPRFX.'?draw_test'.$a_head['~']
		, 'ar' => (
				is_dir(DIR_ARCH)
				? A.ROOTPRFX.DIR_ARCH.$a_head['*']
				: ''
			)
		)
	);

	$footer = $links = $took = '';

	if (!$u_opts['names'] && defined('FOOT_NOTE')) {
		$links = vsprintf(FOOT_NOTE, $tmp_foot_notes);
	}
	if (!$u_opts['times'] && $u_key) {
		define(TOOK, $took = '<!--?-->');
		if (TIME_PARTS) {
			time_check_point('inb4 template');
			$took = '<a href="javascript:'.(++$page['js'][0]).',toggleHide(took),took.scrollIntoView()">'.$took.'</a>';
			foreach ($tcp as $t => $comment) {
				$t = get_time_elapsed($t);
				$t_diff = ltrim(sprintf('%.6f', $t - $t_prev), '0.');
				$t = sprintf('%.6f', $t_prev = $t);
				$comment = str_replace(NL, '<br>-', is_array($comment)?implode('<br>', $comment):$comment);
				$took_list .= NL."<tr><td>$t +</td><td>$t_diff:</td><td>$comment</td></tr>";
			}
		}
		$took = get_time_html().str_replace_first(' ', NL, sprintf($tmp_took, $took));
	}
	if (
		($a = (array)$cfg_link_schemes)
	&&	($s = strtolower($_SERVER['REQUEST_SCHEME'] ?? $_SERVER['HTTPS'] ?? $a[0]))
	) {
		if ($s === 'off') $s = 'http'; else
		if ($s === 'on') $s = 'https';
		if (($i = array_search($s, $a)) !== false) unset($a[$i]);
		foreach ($a as $k) {
			$j = "$k://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]";
			$took .= NL.'<a href="'.$j.'">'.$tmp_link_schemes[$k].'</a>';
		}
	}
	foreach (array(
		'l' => $took
	,	'r' => $links
	) as $k => $v) if (strlen($v)) $footer .= NL.'<span class="'.$k.'">'.indent($v).'</span>';

	if ($footer) $page['footer'] = '<p class="hint">'.indent($footer).'</p>'.(
		$took_list
		? NL.'<table id="took" style="display:none">'.indent($took_list).'</table>'
		: ''
	);
	if ($v = $query['!']) $page['report'] = $v;
	$page['anno'] = 1;
}

die(get_template_page($page));




//* POST: add/modify content *-------------------------------------------------

posting:

ob_start();

if ($u_key) {
	$post_status = (($_POST[ME] || $_POST[$qredir])?OK:'unkn_req');

	if (isset($_POST[$qredir])) goto after_posting;

//* options change ------------------------------------------------------------

	if (isset($_POST[$p = 'quit'])) {
		$post_status = 'user_quit';
		$u_key = $p;
	} else
	if (isset($_POST[$p = O.'o'])) {
		$post_status = 'user_opt';
		if (strlen($_POST[$p]) > 1) $u_opts = 'default';
		else {
			foreach ($cfg_opts_order as $i => $o)
			foreach ($o as $k) {
				$v = (isset($_POST[$p = O.abbr($k)]) ? $_POST[$p] : '');
				if ($i === 'input') ${"u_$k"} = $v;
				else $u_opts[$k] = $v;
			}
		}
	} else

//* admin/mod actions ---------------------------------------------------------

	if (isset($_POST['mod']) && MOD && (($qd_room && $room) || (GOD && ($query['users'] || $etc === '3')))) {
		$d = 'abcdefg';
		$k = array();
		foreach ($_POST as $i => $a) if (preg_match('~^m\d+_(\d+)_(\d+)_(\d+)$~i', $i, $m)) {
			$m[0] = $a;
			$act[$k[] = str_replace_first('_', $d[substr_count($a, '+')], $i)] = $m;
		}
		if ($act) {
			natsort($k);
			data_lock("room/$room");
			foreach (array_reverse($k) as $i) {
				$m = data_mod_action($act[$i]);	//* <- act = array(option name, thread, row, column)
				if ($post_status != 'unkn_res') $post_status = ($m?OK:'unkn_res');
			}
			data_unlock();
		}
	} else
	if (!$qd_room || !$room); else	//* <- no posting outside room

//* report problem in active room ---------------------------------------------

	if (isset($_POST['report']) && ($postID = $query['report_post'] ?: $etc) && (MOD || !(R1 || $u_flag['nor']))) {
		$post_status = 'no_path';
		if (preg_match(PAT_DATE, $postID, $r) && ($postID == $r[0])) {	//* <- r = array(t-r-c, t-r, thread, row, column)
			$post_status = 'text_short';
			if (mb_strlen($r[1] = trim_post($_POST['report'], REPORT_MAX_LENGTH)) >= REPORT_MIN_LENGTH) {
				data_lock("room/$room");
				$post_status = (
					data_log_report($r, $_POST['freeze'] || $_POST['check']) > 0
					? OK
					: 'unkn_res'
				);
				data_unlock();
			}
		}
	} else
	if ($etc); else			//* <- no "etc" posting without report

//* skip current task ---------------------------------------------------------

	if (isset($_POST['skip'])) {
		if (preg_match('~^\d+~', $_POST['skip'], $digits)) {
			$i = $digits[0];
			list($a, $r) = get_room_skip_name($room);
			if ($q = get_room_skip_list($a)) {
				array_unshift($q, $i);
				$i = implode('/', $q);
			}
			$add_qk = "$a=$r/$i";
			$post_status = 'skip';
		}
	} else

//* process new text post -----------------------------------------------------

	if (isset($_POST['describe'])) {
		$post_status = 'text_short';
		if (mb_strlen($x = $ptx = trim_post($_POST['describe'], DESCRIBE_MAX_LENGTH)) >= DESCRIBE_MIN_LENGTH) {
			$unlim = trim($_POST['describe']);
			$n = strlen($delim = '/');
			if (
				substr($unlim, 0, $n) == $delim
			&&	substr($unlim, -$n) == $delim
			&&	substr_count($x = trim($x, $spaced = " $delim "), $spaced)
			) {
				$x = '<i class="poem">'
				.	str_replace($spaced, '<br>',
					preg_replace("~\s+($delim\s+){2,}~", '<br><br>',
						trim($x, $spaced)
					))
				.'</i>';
			}
			$post_status = 'new_post';
		}
	} else

//* process new pic post ------------------------------------------------------

	if (isset($_POST['pic']) || isset($_FILES['pic'])) {
		$post_status = 'file_pic';
		$log = 0;
		data_aim();
		if ($upload = $_FILES['pic']) {
			$t = min($_POST['t0'] ?: T0, $target['time'] ?: T0).'000-'.T0.'000';
			$ptx = "time: $t
file: $upload[name]";
			if ($upload['error']) {
				$log = print_r($_FILES, true);
			} else {
				$x = $upload['type'];
				$file_type = strtolower(substr($x, strpos($x, '/')+1));
				if (in_array($file_type, $cfg_draw_file_types)) {
					$file_size = $upload['size'];
					$txt = "$t,file: $upload[name]";
				} else {
					$log = "File type $x not allowed.";
				}
			}
		} else {
			$post_data_size = strlen($post_data = $_POST['pic']);
			$txt = $ptx = ($_POST['txt'] ?: '0-0,(?)');
	//* metadata, newline-separated key-value format:
			if (false !== strpos($txt, NL)) {
				$a = explode(',', 'app,active_time,draw_time,open_time,t0,time,used');	//* <- to add to picture mouseover text
				$b = explode(',', 'bytes,length');					//* <- to validate
				$x = preg_split('~\v+~u', $txt);
				$y = array();
				$z = 0;
				foreach ($x as $line) if (preg_match('~^(\w+)[\s:=]+(.+)$~u', $line, $m) && ($k = strtolower($m[1]))) {
					if (in_array($k, $a)) $y[$k] = $m[2]; else
					if (in_array($k, $b)) $z = $m[2];
				}
				if ($z && $z != $post_data_size) {
					$post_status = 'file_part';
					$log = "$post_data_size != $z";
				} else {
					$t = min($y['t0'] ?: T0, $target['time'] ?: T0);
					$t = array($t.'000', T0.'000');
					$z = ($target ? "/$t[0]-$t[1]" : '');
					if ($x = $y['time']) {
						if (!preg_match('~^(\d+:)+\d+$~', $x)) {
							if (preg_match('~^(\d+)\D+(\d+)$~', $x, $m)) {
								if ($m[1] && $m[1] != $m[2]) $t[0] = $m[1];
								if ($m[2]) $t[1] = $m[2];
							}
							$x = "$t[0]-$t[1]";
						}
					} else {
						$a = explode('-', $y['open_time'] ?: '-');
						$b = explode('-', $y['draw_time'] ?: '-');
						if ($b[0] == $b[1]) $b[0] = 0;
						foreach ($t as $k => $v) $t[$k] = $b[$k] ?: $a[$k] ?: $v;
						$x = "$t[0]-$t[1]";
					}
					$t = $x.$z;
					$a = $y['app'] ?: '[?]';
					if ($x = $y['used']) $a .= " (used $x)";
					if ($x = $y['active_time']) $t .= "=$x";
					$txt = "$t,$a";
				}
			} else
	//* metadata, legacy CSV:
			if (preg_match('~^(?:(\d+),)?(?:([\d:]+)|(\d+)-(\d+)),(.*)$~is', $txt, $t)) {
				if ($t[2]) $txt = $t[2].','.$t[5];
				else {
					if (!$t[4]) $t[4] = T0.'000';
					if (!$t[3] || $t[3] == $t[4]) $t[3] = ($t[1] ?: $target['time']).'000';
					if (!$t[3]) $t[3] = $t[4];
					$txt = "$t[3]-$t[4],$t[5]";
				}
			}
			if (!$log) {
	//* check pic content: "data:image/png;base64,EncodedFileContent"
				$i = strpos($post_data, ':');
				$j = strpos($post_data, '/');
				$k = strpos($post_data, ';');
				$l = strpos($post_data, ',');
				$x = max($i, $j)+1;
				$y = min($i, $j)+1;
				$z = min($k, $l);
				$file_type = strtolower(substr($post_data, $x, $z-$x));
				$mime_type = strtolower(substr($post_data, $y, $z-$y));
				if (in_array($file_type, $cfg_draw_file_types)) {
					$file_content = base64_decode(substr($post_data, max($i, $j, $k, $l)+1));
					if (false === $file_content) {
						$log = "invalid content, $post_data_size bytes: ".(
							$post_data_size > REPORT_MAX_LENGTH
							? substr($post_data, 0, REPORT_MAX_LENGTH).'(...)'
							: $post_data
						);
					} else $file_size = strlen($file_content);
				} else {
					$log = "File type $mime_type not allowed.";
				}
			}
		}
		if ($log); else
		if (($x = $file_size) && $x > DRAW_MAX_FILESIZE) {
			$post_status = 'file_size';
			$log = $x;
		} else
		if (is_file($pic_final_path = get_pic_subpath(
			$fn = ($md5 = (
				$upload
				? md5_file($f = $upload['tmp_name'])
				: md5($file_content)
			)).'.'.(
				($png = ($file_type === 'png'))
				? 'png'
				: 'jpg'
			), 1
		))) {
			$post_status = 'file_dup';
			$log = $fn;
		} else {
	//* save pic file:
			if (!$upload && ($log = file_put_contents($f = $pic_final_path, $file_content)) != $x) {
				$x = 0;
				$post_status = 'file_put';
			} else
	//* check image data:
			if ($sz = getImageSize($f)) {
				unset($file_content, $post_data, $_POST['pic']);
				foreach ($tmp_whu as $k => $v)
				if ($a = (
					get_const('DRAW_LIMIT_'.$v)
				?:	get_const('DRAW_DEFAULT_'.$v)
				)) {
					list($a, $b) = preg_split('~\D+~', $a);
					$y = ($b ?: $a);
					$z = ${$tmp_wh[$k]} = $sz[$k];
					if (($a && $z < $a) || ($y && $z > $y)) {
						$x = 0;
						$post_status = 'pic_size';
						$log = "$sz[0]x$sz[1]";
						break;
					}
				}
				if ((($resize = ($w > DRAW_PREVIEW_WIDTH)) || $x < 9000) && $x > 0) {
					$post_status = 'pic_fill';
					$i = "imageCreateFrom$file_type";
					$log = imageColorAt($pic = $i($f), 0, 0);
					for ($x = $w; --$x;)
					for ($y = $h; --$y;) if (imageColorAt($pic, $x, $y) != $log) break 2;
				}
	//* invalid image:
			} else $x = 0;
	//* ready to save post:
			if ($x > 0) {
				if ($upload && !rename($f, $pic_final_path)) {
					$x = 0;
					$post_status = 'file_put';
				} else {
					if ($resize) {
						$fwh = $fn .= ";$w*$h, ";
						$fn .= format_filesize($file_size);
					} else if ($pic) {
						imageDestroy($pic);
					} else $log = "imageDestroy(none): $f";
	//* gather post data fields to store:
					$x = array($fn, trim_post($txt));
					if (LOG_UA) $x[] = trim_post($_SERVER['HTTP_USER_AGENT']);
					$post_status = 'new_post';
				}
			}
		}
	}

//* write new post to a thread ------------------------------------------------

	if ($post_status == 'new_post') {
		if (!$target) data_aim();
		$x = data_log_post($x);
	//	data_unlock();
		$t = array();
		if ($log = $x['fork']) $t[] = 'trd_miss';
		if ($log = $x['cap']) $t[] = 'trd_max'; else
		if (!$x['post']) {
			$t[] = 'unkn_res';
			$del_pic = $pic_final_path;
		}
		if (is_array($x = $x['arch']) && $x['done']) $t[] = 'trd_arch';
		if (count($t)) $post_status = implode('!', $t);
	} else {
		data_unlock();
		$del_pic = $f;
	}

//* after user posting --------------------------------------------------------

	if ($f = $del_pic) {
		if (is_file($f)) unlink($f);
		unset($pic_final_path);
	}
	if ($ptx) {
		if ($log) {
			$op = ' = {';
			$ed = NL.'}';
			$i = NL.'	';
			$t = '';
			if ($target || data_aim()) foreach ($target as $key => $val) $t .= "$i$key: $val";
			$ptx = preg_replace('~\v+~u', $i, trim($ptx));
			data_log_action("Denied $post_status: $log
Post$op$i$ptx$ed
Target$op$t$ed"
			);
		} else if (!$u_room_default) {
			$u_room_default = $u_opts['room'] = $room;
		}
	}
} else

//* register new user ---------------------------------------------------------

if (isset($_POST[ME]) && strlen($name = trim_post($_POST[ME], USER_NAME_MAX_LENGTH)) >= USER_NAME_MIN_LENGTH) {
	$post_status = (data_log_user($u_key = md5($name.T0.substr(M0,2,3)), $name)?'user_reg':'unkn_res');
}




//* redirect after POST -------------------------------------------------------

after_posting:

if (strlen($o = trim(ob_get_clean()))) data_log_action('POST buffer dump: '.$o);

if ($p = $post_status) foreach (array(
	'OK' => $tmp_post_ok
,	'NO' => $tmp_post_err
) as $k => $v) {
	if ($$k = array_key_exists($p, $v)) $msg = $v[$p];
	else $$k = ($p == (get_const($k) ?: $k));
}
if ($OK && isset($_POST['report'])) die(get_template_page(array(
	'head' => '<script>window.close();</script>'
,	'title' => $msg
,	'task' => $p
)));

header('HTTP/1.1 303 Refresh after POST: '.$p);

$d = (DIR_DOTS ? '' : ROOTPRFX.($qdir?"$qdir/":''));
$l = (
	(
		(strlen($room) && $room != $room_in_url)				//* <- move after rename
	||	(($v = $_POST[$qredir]) && strlen($room_dec = trim_room(URLdecode($v))))//* <- create new room
	)
	? ($d ?: ($room?'../':'')).URLencode($room_dec ?: $room).'/'
	: ($d?$d.($room?"$room/":''):'').($etc && $etc[0] != '-'?$etc:($d?'':'.'))
);
if ($OK) {
	if ($u_key) {
		$a = (
			QK_KEEP_AFTER_LOGOUT
			? preg_replace('~^[0-9a-z]+~i', '', $_COOKIE[ME] ?: '')		//* <- keep after quit
			: ''
		);
		if (isset($_POST[ME])) $a = "$u_key$a";					//* <- restore at enter
		else if ($u_key !== 'quit') {
			$a = array($u_key);
			if ($u_opts !== 'default') {
				foreach ($cfg_opts_order as $i => $o) if ($i === 'input') {
					foreach ($o as $k => $u) if (isset(${$n = "u_$u"})) {
						if (!in_array($k, $cfg_opts_text)) {
							if ($v = intval($$n)) $a[] = "$k:$v";
						} else
						if (strlen($raw = trim_room($$n))) {	//* <- OK for any text setting
							$enc = URLencode($raw);
							$v = (
								$enc === $raw
								? $raw
							//	: $enc			//* <- full URLencode is OK, but longer
								: 'base64:'.URLencode(base64_encode($raw))
							);
							$a[] = "$k:$v";
						}
					}
				} else {
					$v = array();
					$s = '';
					foreach ($o as $k) if (intval($u_opts[$k])) {
						$v[] = $k = abbr($k);
						if (!$s && strlen($k) > 1) $s = '.';
					}
					if (strlen($v = implode($s, $v))) $a[] = "$i:$v$s";
				}
			}
			$a = implode('/', $a);
		}
		$s = 'Set-Cookie: ';
		$x = '; expires='.gmdate(DATE_COOKIE, ($a ? T0 + QK_EXPIRES : 0)).'; Path='.ROOTPRFX;
		$a = ME.'='.$a;
		header("$s$a$x");
		if ($add_qk) header("$s$add_qk$x");
	}
} else {
	if ($p) $l .= '?!='.$p;
	foreach ((array)$query as $k => $v) if (substr($k, 0, 4) == 'draw') $l .= '&'.$k.(strlen($v)?'='.$v:'');
}

//* show pic processing progress ----------------------------------------------

$ri = 0;
if ($f = $pic_final_path) {

	function pic_opt_get_size($f) {
		global $ri, $tmp_no_change, $TO;
		$old = filesize($f);
		if ($ri) {
			echo format_filesize($old).$TO;
			flush();
		}
		optimize_pic($f);
		if ($old === ($new = filesize($f))) {
			if ($ri) echo $tmp_no_change;
			return '';
		} else {
			$f = format_filesize($new);
			if ($ri) echo $f;
			return $f;
		}
	}

	function pic_opt_get_time() {
		global $AT;
		return '</p>
<p>'.get_time_elapsed().$AT;
	}

	if ($u_opts['picprogress']) {
		ob_start();
	} else {
		if (false === strpos('.,;:?!', substr($msg, -1))) $msg .= '.';
		$AT = ' &mdash; ';
		$BY = ' x ';
		$TO = ' &#x2192; ';
		$ri = max(intval(POST_PIC_WAIT), 1);

	//* this is not working here, must set in php.ini:
	//	ini_set('zlib.output_compression', 'Off');
	//	ini_set('output_buffering', 'Off');

	//* this is for nginx and gzip:
		if (NGINX) {
			header('X-Accel-Buffering: no');
			header('Content-Encoding: none');
		}

	//* this is for the browser, some may be configured to prevent though:
		header("Refresh: $ri; url=$l");

		echo '<!doctype html>
<html lang="'.$lang.'">
<head>
	<meta charset="'.ENC.'">
	<title>'.$tmp_sending.'</title>
	<style>
		html {background-color: #eee;}
		body {background-color: #f5f5f5; margin: 1em; padding: 1em; font-family: sans-serif; font-size: 14px;}
		p {padding: 0 1em;}
		p:first-child {background-color: #ddd; padding: 1em;}
		p:last-child {background-color: #def; padding: 1em; margin-block-end: 0;}
	</style>
</head>
<body>
<p>'.get_time_html()."$AT$tmp_post_progress[starting].".pic_opt_get_time()."$tmp_post_progress[opt_full]: ";
	}
	$changed = pic_opt_get_size($f);

	if ($pic && $resize) {
		if ($changed) data_rename_last_pic($fn, $fwh.$changed);
		$x = DRAW_PREVIEW_WIDTH;
		$y = round($h/$w*$x);
		$z = filesize($f);
		if ($ri) echo pic_opt_get_time()."$tmp_post_progress[low_res]: $w$BY$h$TO$x$BY$y";
		$p = imageCreateTrueColor($x,$y);
		imageAlphaBlending($p, false);
		imageSaveAlpha($p, true);
		imageCopyResampled($p, $pic, 0,0,0,0, $x,$y, $w,$h);
		imageDestroy($pic);
		$i = "image$file_type";
		$i($p, $f = get_pic_resized_path($f));
		if ($ri) echo pic_opt_get_time()."$tmp_post_progress[opt_res]: ";
		pic_opt_get_size($f);

		if ($png && ($z < filesize($f))) {
			if ($ri) echo pic_opt_get_time()."$tmp_post_progress[low_bit]: 255";
			$c = imageCreateTrueColor($x,$y);
			imageCopyMerge($c, $p, 0,0,0,0, $x,$y, 100);
			imageTrueColorToPalette($p, false, 255);
			imageColorMatch($c, $p);
			imageDestroy($c);
			$i($p, $f);
			imageDestroy($p);
			if ($ri) echo pic_opt_get_time()."$tmp_post_progress[opt_res]: ";
			pic_opt_get_size($f);
		} else {
			imageDestroy($p);
		}
	}
	data_unlock();

	if ($ri) {
		echo pic_opt_get_time().$msg.'</p>
<p>'.sprintf($tmp_post_progress['refresh'], $l, format_time_units($ri)).'</p>
</body>
</html>';
	} else ob_end_clean();
}
if (!$ri) header("Location: $l");	//* <- printing content has no effect with Location header

?>
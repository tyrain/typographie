<?php

	/*
		Typographie, v1.0
		https://github.com/asleepwalker/typographie

		by Artyom "Sleepwalker" Fedosov, 2014
		http://me.asleepwalker.ru/
		mail@asleepwalker.ru
	*/

	class Typographie {

		private $_in;
		private $_out;
		private $_actions;

		public function __construct($in = 'plain', $out = 'plain') {
			$this->_in = $in;
			$this->_out = $out;
		}

		public function actions($actionlist) {
			$this->_actions = explode(',', $actionlist);
		}

		public function convert($raw) {
			if (($this->_in == 'html') && ($this->_out == 'plain')) {
				$raw = preg_replace('/[\n]*<br[\s\/]*>[\n]*/ui', "\n", $raw);
				$raw = preg_replace('/<p[^>]*>(.*?)<\/p>[\s]*/usi', "$1\n\n", $raw);
				$raw = strip_tags($raw);
			}
			elseif (($this->_in == 'plain') && ($this->_out == 'html')) {
				$raw = str_replace('<', '&lt;', $raw);
				$raw = str_replace('>', '&gt;', $raw);
				if (in_array('paragraphs', $this->_actions)) {
					$raw = preg_replace('/^(.+?)$/uim', "<p>$1</p>", $raw);
					$raw = preg_replace('/<\/p>\n<p>/ui', "<br>\n", $raw);
				} else $raw = preg_replace('/[\n]/ui', "<br>\n", $raw);
			}
			return $raw;
		}

		public function process($text) {

			$pieces = array();
			function preserve_html($pattern, &$pieces, $text) {
				return preg_replace_callback($pattern, function ($match) use (&$pieces) {
					$code = substr(md5($match[0]), 0, 8);
					$pieces[$code] = $match[0];
					return '{'.$code.'}';
				}, $text);
			}
			if ($this->_out == 'html') {
				$text = preserve_html('/<[\/]{0,1}p>/ui', $pieces, $text);
				if ($this->_in == 'html') {
					if (in_array('safehtml', $this->_actions))
						$text = preserve_html('/<(code|pre)[^>]*>.*?<\/\1>/uis', $pieces, $text);
					$text = preserve_html('/<[^>]+>/ui', $pieces, $text);
				}
			}

			$actions = array();

			// Спецсимволы
			if (in_array('specials', $this->_actions)) {
				$actions['/\{([\'"])}/']                       = '$1';
				$actions['/(\([cс]\))|(\{copy\})/ui']          = '©';
				$actions['/(\(r\))|(\{reg\})/ui']              = '®';
				$actions['/(\((тм|tm)\))|(\{(tm|trade)\})/ui'] = '™';
				$actions['/\{(ss|sect)}/']                     = '§';
				$actions['/\{(\*|deg)}/']                      = '°';

				$actions['/\{euro}/']                          = '€';
				$actions['/\{cent}/']                          = '¢';
				$actions['/\{pound}/']                         = '£';
				$actions['/\{(yen|yuan)}/']                    = '¥';

				$actions['/\{alpha\}/ui']                      = 'α';
				$actions['/\{beta\}/ui']                       = 'β';
				$actions['/\{gamma\}/ui']                      = 'γ';
				$actions['/\{delta\}/ui']                      = 'δ';
				$actions['/\{epsilon\}/ui']                    = 'ε';
				$actions['/\{theta\}/ui']                      = 'θ';
				$actions['/\{lambda\}/ui']                     = 'λ';
				$actions['/\{mu\}/ui']                         = 'μ';
				$actions['/\{nu\}/ui']                         = 'ν';
				$actions['/\{pi\}/ui']                         = 'π';
				$actions['/\{rho\}/ui']                        = 'ρ';
				$actions['/\{sigma\}/ui']                      = 'σ';
				$actions['/\{tau\}/ui']                        = 'τ';
				$actions['/\{phi\}/ui']                        = 'φ';
				$actions['/\{psi\}/ui']                        = 'Ψ';
				$actions['/\{omega\}/ui']                      = 'ω';
			}

			// Математические символы
			if (in_array('mathchars', $this->_actions)) {
				$actions['/\{!=}/']                            = '≠';
				$actions['/\{~}/']                             = '≈';
				$actions['/\{equal}/']                         = '≡';
				$actions['/\{<=}/']                            = '⩽';
				$actions['/\{=>}/']                            = '⩾';
				$actions['/\+-/']                              = '±';
				$actions['/\{-}/']                             = '–';
				$actions['/<->/']                              = '↔';
				$actions['/<=>/']                              = '⇔';
				$actions['/<-/']                               = '←';
				$actions['/<=/']                               = '⇐';
				$actions['/->/']                               = '→';
				$actions['/=>/']                               = '⇒';

				$actions['/\{\^1}/']                           = '¹';
				$actions['/\{\^2}/']                           = '²';
				$actions['/\{\^3}/']                           = '³';
				$actions['/\{1\/8}/']                          = '⅛';
				$actions['/\{1\/6}/']                          = '⅙';
				$actions['/\{1\/5}/']                          = '⅕';
				$actions['/\{1\/4}/']                          = '¼';
				$actions['/\{1\/3}/']                          = '⅓';
				$actions['/\{1\/2}/']                          = '½';
				$actions['/\{2\/5}/']                          = '⅖';
				$actions['/\{2\/3}/']                          = '⅔';
				$actions['/\{3\/8}/']                          = '⅜';
				$actions['/\{3\/5}/']                          = '⅗';
				$actions['/\{3\/4}/']                          = '¾';
				$actions['/\{4\/5}/']                          = '⅘';
				$actions['/\{5\/6}/']                          = '⅚';
				$actions['/\{5\/8}/']                          = '⅝';
				$actions['/\{7\/8}/']                          = '⅞';

				$actions['/\{part}/']                          = '∂';
				$actions['/\{any}/']                           = '∀';
				$actions['/\{exist}/']                         = '∃';
				$actions['/\{empty}/']                         = '∅';
				$actions['/\{infinity}/']                      = '∞';
				$actions['/\{belong}/']                        = '∈';
				$actions['/\{!belong}/']                       = '∉';
				$actions['/\{v}/']                             = '√';
				$actions['/\{v3}/']                            = '∛';
				$actions['/\{v4}/']                            = '∜';
				$actions['/\{ang}/']                           = '∠';
			}

			// Отступы в пунктуации
			if (in_array('punctuation', $this->_actions)) {
				if (in_array('dashes', $this->_actions)) $actions['/[-]{2,5}/'] = '—';
				$actions['/([ ]+[-—][ ]*)|([ ]*[-—][ ]+)/u']   = ' - ';
				$actions['/(?<=[.,!?:)])(?=[^ \n"\'.,;!?&:\]\)<{)])/u'] = ' ';
				$actions['/[ ]*(?=[.,;!?:])/u']                = '';
				$actions['/(?<=[.,])[\s]{0,1}[-—](?=[ ])/']    = '—';
			}

			// Градусы, минуты/футы, секунды/дюймы, ч.1
			if (in_array('angles', $this->_actions)) {
				$actions['/([\d.]+)\*/']                       = '$1°';
				$actions['/([\d.]+)\'/']                       = '$1′';
			}

			// Отступы вокруг спецсимволов
			if (in_array('specialspaces', $this->_actions)) {
				$actions['/([№§])[\s]*(?=[\d])/']              = '$1 ';
				$actions['/(?<=[\d])[\s]*(?=°[CСF])/u']        = ' ';
			}

			// Кавычки-ёлочки
			$actions['/(^|[\s>};\(\[-])"/']                    = '$1«';
			$actions['/"([\s-\.!,:;\?\)\]\n\r]|$)/']           = '»$1';
			$actions['/([^\s{])"([^\s}])/']                    = '$1»$2';

			// Двойные+ пробелы
			if (in_array('dblspace', $this->_actions))
				$actions['/[ ]{2,}/']                          = ' ';

			// Тире, минус, интервал
			if (in_array('dashes', $this->_actions)) {
				$actions['/(^|\n|["„«])--?(\s)/u']             = '$1—$2';
				$actions['/(\s)--?(\s)/']                      = ' —$2';
				$actions['/(?<=[\d])-(?=[\d])/']               = '–';
			}

			// Неразрывные пробелы
			if (in_array('nbsp', $this->_actions))
				$actions['/([\s][a-zа-яё]{1,2})[ ]/iu']        = '$1 ';

			// Символ троеточия
			if (in_array('hellip', $this->_actions))
				$actions['/[.]{2,5}/']                         = '…';

			// Выполняем операции замены
			foreach ($actions as $key=>$val)
				$text = preg_replace($key, $val, $text);

			// Вложенные кавычки
			if (in_array('inquot', $this->_actions))
				while (preg_match('/(«[^«»]*)«/mu', $text)) {
					$text = preg_replace('/(«[^«»]*)«/mu', '$1„', $text);
					$text = preg_replace('/(„[^„“«»]*)»/mu', '$1“', $text);
				}
			else {
				// Дублирующие кавычки сливаются в одни
				$text = preg_replace('/[«]+/', '«', $text);
				$text = preg_replace('/[»]+/', '»', $text);
			}

			// Градусы, минуты/футы, секунды/дюймы, ч.2
			if (in_array('angles', $this->_actions)) {
				$text = preg_replace('/(?<=»)([^«]+?[\d.]+)»/', '$1″', $text);
				if (strpos($text, '«') === false)
					$text = preg_replace('/([\d.]+)»/', '$1″', $text);
			}

			if (($this->_in == 'html') && ($this->_out == 'plain'))
				$text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');

			foreach ($pieces as $code => $content)
				$text = str_replace('{'.$code.'}', $content, $text);

			return $text;
		}
	};

?>
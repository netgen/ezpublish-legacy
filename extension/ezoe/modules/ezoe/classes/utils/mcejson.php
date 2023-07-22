<?php
/**
 * $Id: JSON.php 40 2007-06-18 11:43:15Z spocke $
 *
 * @package MCManager.utils
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

class Moxiecode_JSONReader
{
	final public const JSON_BOOL = 1;
	final public const JSON_INT = 2;
	final public const JSON_STR = 3;
	final public const JSON_FLOAT = 4;
	final public const JSON_NULL = 5;
	final public const JSON_START_OBJ = 6;
	final public const JSON_END_OBJ = 7;
	final public const JSON_START_ARRAY = 8;
	final public const JSON_END_ARRAY = 9;
    final public const JSON_KEY = 10;
    final public const JSON_SKIP = 11;

    final public const JSON_IN_ARRAY = 30;
    final public const JSON_IN_OBJECT = 40;
    final public const JSON_IN_BETWEEN = 50;
	private readonly int $_len;
	private readonly int $_pos;
	private null|bool|string|float|int $_value = null;
	private ?int $_token = null;
	private $_location = Moxiecode_JSONReader::JSON_IN_BETWEEN;
	private array $_lastLocations = [];
	private bool $_needProp = false;

	function __construct(private $_data) {
		$this->_len = strlen((string) $_data);
		$this->_pos = -1;
	}

	/**
	 * @deprecated Use Moxiecode_JSONReader::__construct() instead
	 * @param $data
	 */
	function Moxiecode_JSONReader($data) {
		self::__construct($data);
	}

	function getToken() {
		return $this->_token;
	}

	function getLocation() {
		return $this->_location;
	}

	function getTokenName()
 {
     return match ($this->_token) {
         Moxiecode_JSONReader::JSON_BOOL => 'JSON_BOOL',
         Moxiecode_JSONReader::JSON_INT => 'JSON_INT',
         Moxiecode_JSONReader::JSON_STR => 'JSON_STR',
         Moxiecode_JSONReader::JSON_FLOAT => 'JSON_FLOAT',
         Moxiecode_JSONReader::JSON_NULL => 'JSON_NULL',
         Moxiecode_JSONReader::JSON_START_OBJ => 'JSON_START_OBJ',
         Moxiecode_JSONReader::JSON_END_OBJ => 'JSON_END_OBJ',
         Moxiecode_JSONReader::JSON_START_ARRAY => 'JSON_START_ARRAY',
         Moxiecode_JSONReader::JSON_END_ARRAY => 'JSON_END_ARRAY',
         Moxiecode_JSONReader::JSON_KEY => 'JSON_KEY',
         default => 'UNKNOWN',
     };
 }

	function getValue() {
		return $this->_value;
	}

	function readToken() {
		$chr = $this->read();

		if ($chr != null) {
			switch ($chr) {
				case '[':
					$this->_lastLocation[] = $this->_location;
					$this->_location = Moxiecode_JSONReader::JSON_IN_ARRAY;
					$this->_token = Moxiecode_JSONReader::JSON_START_ARRAY;
					$this->_value = null;
					$this->readAway();
					return true;

				case ']':
					$this->_location = array_pop($this->_lastLocation);
					$this->_token = Moxiecode_JSONReader::JSON_END_ARRAY;
					$this->_value = null;
					$this->readAway();

					if ($this->_location == Moxiecode_JSONReader::JSON_IN_OBJECT)
						$this->_needProp = true;

					return true;

				case '{':
					$this->_lastLocation[] = $this->_location;
					$this->_location = Moxiecode_JSONReader::JSON_IN_OBJECT;
					$this->_needProp = true;
					$this->_token = Moxiecode_JSONReader::JSON_START_OBJ;
					$this->_value = null;
					$this->readAway();
					return true;

				case '}':
					$this->_location = array_pop($this->_lastLocation);
					$this->_token = Moxiecode_JSONReader::JSON_END_OBJ;
					$this->_value = null;
					$this->readAway();

					if ($this->_location == Moxiecode_JSONReader::JSON_IN_OBJECT)
						$this->_needProp = true;

					return true;

				// String
				case '"':
				case '\'':
					return $this->_readString($chr);

				// Null
				case 'n':
					return $this->_readNull();

				// Bool
				case 't':
				case 'f':
					return $this->_readBool($chr);

				default:
					// Is number
					if (is_numeric($chr) || $chr == '-' || $chr == '.')
						return $this->_readNumber($chr);

					return true;
			}
		}

		return false;
	}

	function _readBool($chr) {
		$this->_token = Moxiecode_JSONReader::JSON_BOOL;
		$this->_value = $chr == 't';

		if ($chr == 't')
			$this->skip(3); // rue
		else
			$this->skip(4); // alse

		$this->readAway();

		if ($this->_location == Moxiecode_JSONReader::JSON_IN_OBJECT && !$this->_needProp)
			$this->_needProp = true;

		return true;
	}

	function _readNull() {
		$this->_token = Moxiecode_JSONReader::JSON_NULL;
		$this->_value = null;

		$this->skip(3); // ull
		$this->readAway();

		if ($this->_location == Moxiecode_JSONReader::JSON_IN_OBJECT && !$this->_needProp)
			$this->_needProp = true;

		return true;
	}

	function _readString($quote) {
		$output = "";
		$this->_token = Moxiecode_JSONReader::JSON_STR;
		$endString = false;

		while (($chr = $this->peek()) != -1) {
			switch ($chr) {
				case '\\':
					// Read away slash
					$this->read();

					// Read escape code
					$chr = $this->read();
					match ($chr) {
         't' => $output .= "\t",
         'b' => $output .= "\b",
         'f' => $output .= "\f",
         'r' => $output .= "\r",
         'n' => $output .= "\n",
         'u' => $output .= $this->_int2utf8(hexdec((string) $this->read(4))),
         default => $output .= $chr,
     };

					break;

					case '\'':
					case '"':
						if ($chr == $quote)
							$endString = true;

						$chr = $this->read();
						if ($chr != -1 && $chr != $quote)
							$output .= $chr;

						break;

					default:
						$output .= $this->read();
			}

			// String terminated
			if ($endString)
				break;
		}

		$this->readAway();
		$this->_value = $output;

		// Needed a property
		if ($this->_needProp) {
			$this->_token = Moxiecode_JSONReader::JSON_KEY;
			$this->_needProp = false;
			return true;
		}

		if ($this->_location == Moxiecode_JSONReader::JSON_IN_OBJECT && !$this->_needProp)
			$this->_needProp = true;

		return true;
	}

	function _int2utf8($int) {
		$int = intval($int);

		switch ($int) {
			case 0:
				return chr(0);

			case ($int & 0x7F):
				return chr($int);

			case ($int & 0x7FF):
				return chr(0xC0 | (($int >> 6) & 0x1F)) . chr(0x80 | ($int & 0x3F));

			case ($int & 0xFFFF):
				return chr(0xE0 | (($int >> 12) & 0x0F)) . chr(0x80 | (($int >> 6) & 0x3F)) . chr (0x80 | ($int & 0x3F));

			case ($int & 0x1FFFFF):
				return chr(0xF0 | ($int >> 18)) . chr(0x80 | (($int >> 12) & 0x3F)) . chr(0x80 | (($int >> 6) & 0x3F)) . chr(0x80 | ($int & 0x3F));
		}
	}

	function _readNumber($start) {
		$value = "";
		$isFloat = false;

		$this->_token = Moxiecode_JSONReader::JSON_INT;
		$value .= $start;

		while (($chr = $this->peek()) != -1) {
			if (is_numeric($chr) || $chr == '-' || $chr == '.') {
				if ($chr == '.')
					$isFloat = true;

				$value .= $this->read();
			} else
				break;
		}

		$this->readAway();

		if ($isFloat) {
			$this->_token = Moxiecode_JSONReader::JSON_FLOAT;
			$this->_value = floatval($value);
		} else
			$this->_value = intval($value);

		if ($this->_location == Moxiecode_JSONReader::JSON_IN_OBJECT && !$this->_needProp)
			$this->_needProp = true;

		return true;
	}

	function readAway() {
		while (($chr = $this->peek()) != null) {
			if ($chr != ':' && $chr != ',' && $chr != ' ')
				return;

			$this->read();
		}
	}

	function read($len = 1) {
		if ($this->_pos < $this->_len) {
			if ($len > 1) {
				$str = substr((string) $this->_data, $this->_pos + 1, $len);
				$this->_pos += $len;

				return $str;
			} else
				return $this->_data[++$this->_pos];
		}

		return null;
	}

	function skip($len) {
		$this->_pos += $len;
	}

	function peek() {
		if ($this->_pos < $this->_len)
			return $this->_data[$this->_pos + 1];

		return null;
	}
}

/**
 * This class handles JSON stuff.
 *
 * @package MCManager.utils
 */
class Moxiecode_JSON {
	function __construct() {
	}

	/**
	 * @deprecated Use Moxiecode_JSON::__construct() instead
	 */
	function Moxiecode_JSON() {
		self::__construct();
	}

	function decode($input) {
		$reader = new Moxiecode_JSONReader($input);

		return $this->readValue($reader);
	}

	function readValue(&$reader) {
		$this->data = [];
		$this->parents = [];
		$this->cur =& $this->data;
		$key = null;
		$loc = Moxiecode_JSONReader::JSON_IN_ARRAY;

		while ($reader->readToken()) {
			switch ($reader->getToken()) {
				case Moxiecode_JSONReader::JSON_STR:
				case Moxiecode_JSONReader::JSON_INT:
				case Moxiecode_JSONReader::JSON_BOOL:
				case Moxiecode_JSONReader::JSON_FLOAT:
				case Moxiecode_JSONReader::JSON_NULL:
					switch ($reader->getLocation()) {
						case Moxiecode_JSONReader::JSON_IN_OBJECT:
							$this->cur[$key] = $reader->getValue();
							break;

						case Moxiecode_JSONReader::JSON_IN_ARRAY:
							$this->cur[] = $reader->getValue();
							break;

						default:
							return $reader->getValue();
					}
					break;

				case Moxiecode_JSONReader::JSON_KEY:
					$key = $reader->getValue();
					break;

				case Moxiecode_JSONReader::JSON_START_OBJ:
				case Moxiecode_JSONReader::JSON_START_ARRAY:
					if ($loc == Moxiecode_JSONReader::JSON_IN_OBJECT)
						$this->addArray($key);
					else
						$this->addArray(null);

					//$cur =& $obj;

					$loc = $reader->getLocation();
					break;

				case Moxiecode_JSONReader::JSON_END_OBJ:
				case Moxiecode_JSONReader::JSON_END_ARRAY:
					$loc = $reader->getLocation();

					if (count($this->parents) > 0) {
						$this->cur =& $this->parents[count($this->parents) - 1];
						array_pop($this->parents);
					}
					break;
			}
		}

		return $this->data[0];
	}

	// This method was needed since PHP is crapy and doesn't have pointers/references
	function addArray($key) {
		$this->parents[] =& $this->cur;
		$ar = [];

		if ($key)
			$this->cur[$key] =& $ar;
		else
			$this->cur[] =& $ar;

		$this->cur =& $ar;
	}

	function getDelim($index, &$reader) {
		switch ($reader->getLocation()) {
			case Moxiecode_JSONReader::JSON_IN_ARRAY:
			case Moxiecode_JSONReader::JSON_IN_OBJECT:
				if ($index > 0)
					return ",";
				break;
		}

		return "";
	}

	function encode($input)
 {
     return match (gettype($input)) {
         'boolean' => $input ? 'true' : 'false',
         'integer' => (int) $input,
         'float', 'double' => (float) $input,
         'NULL' => 'null',
         'string' => $this->encodeString($input),
         'array' => $this->_encodeArray($input),
         'object' => $this->_encodeArray(get_object_vars($input)),
         default => '',
     };
 }

	function encodeString($input) {
		// Needs to be escaped
		if (preg_match('/[^a-zA-Z0-9]/', (string) $input)) {
			$output = '';

			for ($i=0; $i<strlen((string) $input); $i++) {
				switch ($input[$i]) {
					case "\b":
						$output .= "\\b";
						break;

					case "\t":
						$output .= "\\t";
						break;

					case "\f":
						$output .= "\\f";
						break;

					case "\r":
						$output .= "\\r";
						break;

					case "\n":
						$output .= "\\n";
						break;

					case '\\':
						$output .= "\\\\";
						break;

					case '\'':
						$output .= "\\'";
						break;

					case '"':
						$output .= '\"';
						break;

					default:
						$byte = ord($input[$i]);

						if (($byte & 0xE0) == 0xC0) {
							$char = pack('C*', $byte, ord($input[$i + 1]));
							$i += 1;
							$output .= sprintf('\u%04s', bin2hex((string) $this->_utf82utf16($char)));
						} if (($byte & 0xF0) == 0xE0) {
							$char = pack('C*', $byte, ord($input[$i + 1]), ord($input[$i + 2]));
							$i += 2;
							$output .= sprintf('\u%04s', bin2hex((string) $this->_utf82utf16($char)));
						} if (($byte & 0xF8) == 0xF0) {
							$char = pack('C*', $byte, ord($input[$i + 1]), ord($input[$i + 2]));
							$i += 3;
							$output .= sprintf('\u%04s', bin2hex((string) $this->_utf82utf16($char)));
						} if (($byte & 0xFC) == 0xF8) {
							$char = pack('C*', $byte, ord($input[$i + 1]), ord($input[$i + 2]));
							$i += 4;
							$output .= sprintf('\u%04s', bin2hex((string) $this->_utf82utf16($char)));
						} if (($byte & 0xFE) == 0xFC) {
							$char = pack('C*', $byte, ord($input[$i + 1]), ord($input[$i + 2]));
							$i += 5;
							$output .= sprintf('\u%04s', bin2hex((string) $this->_utf82utf16($char)));
						} else if ($byte < 128)
							$output .= $input[$i];
				}
			}

			return '"' . $output . '"';
		}

		return '"' . $input . '"';
	}

	private function _utf82utf16($utf8) {
		if (function_exists('mb_convert_encoding'))
			return mb_convert_encoding((string) $utf8, 'UTF-16', 'UTF-8');
  return match (strlen((string) $utf8)) {
      1 => $utf8,
      2 => chr(0x07 & (ord($utf8[0]) >> 2)) . chr((0xC0 & (ord($utf8[0]) << 6)) | (0x3F & ord($utf8[1]))),
      3 => chr((0xF0 & (ord($utf8[0]) << 4)) | (0x0F & (ord($utf8[1]) >> 2))) . chr((0xC0 & (ord($utf8[1]) << 6)) | (0x7F & ord($utf8[2]))),
      default => '',
  };
	}

	private function _encodeArray($input) {
		$output = '';
		$isIndexed = true;

		$keys = array_keys($input);
		for ($i=0; $i<count($keys); $i++) {
			if (!is_int($keys[$i])) {
				$output .= $this->encodeString($keys[$i]) . ':' . $this->encode($input[$keys[$i]]);
				$isIndexed = false;
			} else
				$output .= $this->encode($input[$keys[$i]]);

			if ($i != count($keys) - 1)
				$output .= ',';
		}

		return $isIndexed ? '[' . $output . ']' : '{' . $output . '}';
	}
}

?>

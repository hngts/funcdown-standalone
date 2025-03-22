<?php /// Funcdown Standalone ("multispace") variant
declare (strict_types = 1);
/*:Licence -> https://hngts.com/?mkp=licence~fncd
  ^: ~ See https://hngts.com/?mkp=fncd~manual for more details.
*/

namespace {
  ///
  new class (E: 'UTF-8') {
    ///
    public function __construct (string $E) {
      /*: Mandatory Charset Encoding Setup

          Defines few very handy constants;
          Sets charset to utf-8 wherever known.
          The stuff with charset might become obsoleted.

      */

      foreach ([

        0 => 'default_charset',
        1 => 'mb_internal_encoding',
        2 => 'mb_http_output',

        'EOL' => PHP_EOL,
        'NSP' => "\134",
        'DSP' => DIRECTORY_SEPARATOR,
        'PSP' => PATH_SEPARATOR,

        'CFG' => '.conf.d',
        '_' => str_repeat ('_', 4)

      ] as $index => $value):
        ((is_int ($index))
        ? (($index === 0)
          ? ((ini_get ($value) === $E) or ini_set ($value, $E))
          : (($value() === $E) or $value ($E)))
        : (!defined ($index) and define ($index, $value))
      ); unset ($value, $index);
      endforeach;
    }

  };

  function e (&$a, mixed $b, bool $c = false, mixed $d = null): bool {
    /*: .. and or equals with whatever you put!
      This little function can change the way You looked and wrote
      code so far. Pay attention to its usage below. And not only that.
      As much simple as it is, it will tell You which variables You should
      never put to reference.
    */ $a = $b; return ((!$c) ? ($a === $b) : ($a === $d));
  }

}

namespace H\Root {
  ///

  use \SplFileObject as SFobject; ///
  use \SplTempFileObject as STFobject; ///
  use \FilesystemIterator as FSiterator; /// 
  use \H\OctalPunctuation as P; ///

  trait replacement_compressor {
    /// Hardcoder's crud_engine foundation implementation.

    protected const RPL_SET = [
      /// Major replacement holder

      '_' => // HTML Raw/xml representation of risky (user/guest provided) characters
      [
      0 => [  // Real chars
        0 => P::C['amp'], // '&',
        1 => P::C['lt'],  // '<',
        2 => P::C['gt'],  // '>',
        3 => P::C['qt'],  // '"',
        4 => P::C['ap'],  // "'",
        5 => P::C['bl'],  // '(',
        6 => P::C['br'],  // ')',
        7 => P::C['c'],   // ':',
        8 => P::C['eq'],  // '=',
        9 => P::C['qm'],  // '?',
        10 => P::C['a'],  // '@',
        11 => P::C['sl'], // '[',
        12 => P::C['sr'], // ']',
        13 => P::C['bt'], // '`',
        14 => P::C['cl'], // '{',
        15 => P::C['b'],  // '|',
        16 => P::C['cr'], // '}',
        17 => P::C['ss'], // '/'
        18 => P::C['cm'], // ','
        19 => P::C['esc'],// '\'
        20 => P::C['ps'], // '+'
        21 => P::C['p']   // '%'
      ],
      1 => [  // XML decimal entities
        0 => '&#38;',
        1 => '&#60;',
        2 => '&#62;',
        3 => '&#34;',
        4 => '&#39;',
        5 => '&#40;',
        6 => '&#41;',
        7 => '&#58;',
        8 => '&#61;',
        9 => '&#63;',
        10 => '&#64;',
        11 => '&#91;',
        12 => '&#93;',
        13 => '&#96;',
        14 => '&#123;',
        15 => '&#124;',
        16 => '&#125;',
        17 => '&#47;',
        18 => '&#44;',
        19 => '&#92;',
        20 => '&#43;',
        21 => '&#37;',
      ],
      2 => [ // Special, !HALF-printable and non-standard!, unique char-replacements
        0 => "\360\237\231\265\363\240\200\246",
        1 => "\363\240\200\274\360\223\200\200",
        2 => "\363\240\200\276\360\223\200\240",
        3 => "\363\240\200\242\360\222\221\242",
        4 => "\363\240\200\247\360\220\240\232",
        5 => "\363\240\200\250\357\270\265",
        6 => "\363\240\200\251\357\270\266",
        7 => "\363\240\200\272\357\270\266",
        8 => "\363\240\200\275\352\240\265",
        9 => "\363\240\200\277\352\230\217",
        10 => "\363\240\201\200\341\242\206",
        11 => "\363\240\201\233\342\201\205",
        12 => "\363\240\201\235\342\201\206",
        13 => "\363\240\201\240\340\245\223",
        14 => "\363\240\201\273\342\216\261",
        15 => "\342\224\213\363\240\201\274",
        16 => "\363\240\201\275\357\270\270",
        17 => "\363\240\200\257\360\237\231\274",
        18 => "\342\271\201\363\240\200\254",
        19 => "\363\240\201\234\360\237\231\275",
        20 => "\363\240\200\253\357\254\251",
        21 => "\363\240\200\245\360\226\254\273",
      ]],
      'ns' => [ // Bypass major punct chars in ctype_*'s
        P::C['esc'],P::C['ss'],P::C['c'],P::C['s'],P::C['bt'],P::C['amp'],
        P::C['qm'],P::C['sl'],P::C['sr'],P::C['bl'],P::C['br'],P::C['cl'],
        P::C['cr'],P::C['eq'],P::C['ps'],P::C['as'],P::C['e'],P::C['a'],
        P::C['b'],P::C['t'],P::C['cm'],P::C['qt'],P::C['ap'],
        '.','#','%','$','€','£','^'
      ],
      'bs' => [ // Sealed/unsealed base64_
        [ '=','/','+',  '-','_','.',  '*'
      ],[ "\342\244\272","\342\232\233","\342\214\230",
          "\342\236\253","\342\236\264","\342\234\226", "\342\232\231"
      ]],
      'cy' => [ // Cyrillic (Српски) base64 encode/decode
        'qwertyuiopasdfghjklzxcvbnm=',
        'љњертзуиопасдфгхјклжџцвбнмђ'
      ]
    ];

    protected function transform_encode ($i, string $s):string {
      /// Custom sanitize and unsanitize of special chars

      // No integer = No result = No panic.
      if (!is_int ($i)) return $s;
      $x = ['',''];
      switch ($i) {
        case 1: // Sanitize (htmlentities() alike)
          $x = [ self::RPL_SET['_'][0],
            self::RPL_SET['_'][$i]
          ]; break;
        case 2: // Unsanitize (html_entity_decode() alike)
          $x = [ self::RPL_SET['_'][1],
            self::RPL_SET['_'][0]
          ]; break;
        case 3: // Bypass major punct chars in ctype_*'s
          $x = [self::RPL_SET['ns'],['']]; break;
        case 4: // Just like htmlspecialchars()
          $x = [ array_slice (self::RPL_SET['_'][0], 0, 5),
            array_slice (self::RPL_SET['_'][1], 0, 5)
          ]; break;
        case 5: // Just like htmlspecialchars_decode()
          $x = [ array_slice (self::RPL_SET['_'][1], 0, 5),
            array_slice (self::RPL_SET['_'][0], 0, 5)
          ]; break;
        case 6: // Sealed base64_
          $x = [ self::RPL_SET['bs'][0],
            self::RPL_SET['bs'][1]
          ]; break;
        case 7: // Unsealed base64_
          $x = [ self::RPL_SET['bs'][1],
            self::RPL_SET['bs'][0]
          ]; break;
        case 8: // Hard_seal
          $s = $this-> {__FUNCTION__} (2, $s); // Make sure it's not encoded.
          $s = $this-> {__FUNCTION__} (9, $s); // Make sure it's risky.
          $x = [ self::RPL_SET['_'][0], self::RPL_SET['_'][2] ];
        break;
        case 9: // Hard_UNseal-to-risky
          $x = [ self::RPL_SET['_'][2],
            self::RPL_SET['_'][0]
          ]; break;
        case 10: // Hard_UNseal-to-safe
        return $this-> {__FUNCTION__} (1,
            $this-> {__FUNCTION__} (9, $s));
          break;
        case 11:
          // Ideal for javascript takeover of textnodes
          return htmlentities ($s, ENT_HTML5);
        break;
        case 12: case 13: // base64_cyrlic_fashion

          $x = [
            mb_str_split ( self::RPL_SET['cy'][0]
              . mb_strtoupper (self::RPL_SET['cy'][0])
            ),
            mb_str_split ( self::RPL_SET['cy'][1]
              . mb_strtoupper (self::RPL_SET['cy'][1])
            )
          ];

          if ($i === 12) // cyEnCode
            return $this-> string_reverse (str_replace
              ($x[0], $x[1], base64_encode ($s)));
          else if ($i === 13) // cyDeCode
            return base64_decode (str_replace
              ($x[1], $x[0], $this-> string_reverse ($s)));

        break;
        default: break;
      }
      return
        str_replace ($x[0], $x[1], $s);
    }

    protected function _compress (mixed $x, int $level = 42, int $gz = 0): mixed {
      /// Serialize, compress, encode and seal data
      /// $gz: 0 = *compress; 1 = deflate; 2 = encode
      ${ __FUNCTION__ } = (@($this-> gzmatch (__FUNCTION__, $gz)) ((NSP . 'serialize') ($x), 9));
      $fn = [ NSP . 'base64_encode', NSP . 'bin2hex' ]; return match ($level) {
        0 => $this-> transform_encode (6, ($fn[0]($_compress))), // HardSeal over base64
        1 => $fn[0]($_compress), // Compression-> encode
        2 => $fn[1]($_compress), // Compression-> bin2hex
        42 => $_compress, // Compressed-> Serialized
        default => $x // No change
      };
    }

    protected function _decompress (string $x, int $level = 42, int $gz = 0):mixed {
      /// Unseal, decode, deserialize than decompress data
      /// $gz: 0 = *decompress; 1 = inflate; 2 = decode
      $f = [ NSP . 'unserialize', $this-> gzmatch (__FUNCTION__, $gz),
        NSP . 'base64_decode', NSP . 'hex2bin']; return match ($level) {
        0 => $f[0] (@$f[1] ($f[2] ($this-> transform_encode (7, $x)))), // HardUNSeal over base64
        1 => $f[0] (@$f[1] ($f[2] ($x))), // decompression-> from_encoded-> from_serialized
        2 => $f[0] (@$f[1] ($f[3] ($x))), // decompression-> from_hex-> from_serialized
        42 => $f[0] (@$f[1] ($x)), // Basic decompression-> from_serialized
        default => $x // No change
      };
    }

    private function gzmatch (string $caller, int $match = 0): string {
      /// Returns proper gz compression method
      return NSP . 'gz' . (match ($match) {
        1 => [ 'deflate', 'inflate' ],
        2 => [ 'encode', 'decode' ],
        default => ['compress', 'uncompress']
      })[(($caller === '_compress') ? 0 : 1)];
    }

  }

  trait application_class_obstructor {
    ///

    use replacement_compressor; ///

    public function string_reverse ($string): string {
      /// Multibyte String Reverse
      return implode (array_reverse (mb_str_split ($string)));
    }

    public function enc (int $i, string $s): string {
      /// Public sanitize and unsanitize
      return $this-> transform_encode ($i, $s);
    }

    public function cdc (string $_, mixed $data, int $level = 42, int $gz = 0): mixed {
      /// compress/encode/deflate|decompress/decode/inflate data
      return match ($_) {
        'c' => @$this-> _compress ($data, $level, $gz)
        , 'dc' => @$this-> _decompress ($data, $level, $gz)
        , default => $data
      };
    }

    public function string_filter_rule (string $string, array $PunctBypass): bool {
      /// Detect valid XML tag naming rule and eventually emit bad signal

      if (!ctype_print ($string)) return false;
      else
      {
        $a = mb_str_split ($string);
        foreach ($a as $b => $c)
        {
          if ($b === 0 && !ctype_alpha ($c)) {

            $a[$b] = null;
            break;
          }
          else if (ctype_punct ($c)
            && !in_array ($c, $PunctBypass)) {

            $a[$b] = null;
            break;
          }
          unset ($c, $b);
        }

        $a = trim (implode ($a));
        return ($a === trim ($string));
      }
    }

    public function no_unix_control_chars (string $a): string {
      /// Remove unix/shell control characters
      return preg_replace ('~[^\P{Cc}\r\n\t]+~u', self::U['ea'], $a);
    }

    public function no_comments (string $x = "\057\057", string $a = '', array $merge = []): string {
      /// Remove '$x+? ' line comments from strings

      $a = explode (EOL, $a);
      if (count ($a) >= 1) {

        $diff = null;
        $merged = array_merge (["\176", "\134", ' '], $merge);
        foreach ($a as $int => $b) {
          foreach ($merged as $sample) {
            $p = mb_strpos ($b, "$x$sample");
            if ($p !== false) {
              $a[$int] = mb_substr ($b, 0, $p);
              $diff = $int;
              break;
            } unset ($p, $sample);
          }

          if (is_int ($diff) && trim ($a[$diff]) === '') {
            unset ($a[$diff]); $diff = null;
          } unset ($b, $int);
        }
      }

      return implode (EOL, $a);
    }

    public function no_block_comments (string $a, string $r = ' '): string {
      /// Remove C-style block comments from strings
      return preg_replace ('!/\*.*?\*/!s', $r, $a);
    }

    public function one_line (string $a, bool $TooUgly = false): string {
      /// Entire string to one line - ugly or uglier.

      return preg_replace ('/\s+/',
        ((!$TooUgly) ? ' ' : ""),
        (($TooUgly) ? $a : trim ($a))
      );
    }

    public function dirOr (
      string $dp,
      int $p = 0755,
      bool $recursive = true,
      mixed $context = null
    ): bool {
      /// Check if directory exists, eventually makes one, confims the outcome.
      if (!is_dir ($dp)) mkdir ($dp, $p, $recursive, $context);
      return is_dir ($dp);
    }

    public function joinDsp (...$fragments): string {
      /// This method exists only because of windblows "OS"-es.
      return implode (DSP, $fragments);
    }

    public function flatten (string $suspect, int $mb = 0): string {
      /*: Trim and (mb_)strtolower suspect.
          Any other $mb than 1 will not be multibyte.
      */ return trim (((($mb === 1) ? '\\mb_' : '')
      . 'strtolower') ($suspect));
    }

    public function getConstant (string $constant, $class = null): mixed {
      /// Return UPPERCASED named constant value if any found.
      $datClass = ($class ?? $this); $constant = mb_strtoupper ($constant);
      return @(new \ReflectionClass ($datClass))-> {__FUNCTION__} ($constant);
    }

    public function filesystem_temp_point (string $appname): string {
      /// Creates unique proposal place for storing vital data from various extensions
      return implode ('-', [ $appname, $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'] ]);
    }

    public function highlight_string (
      string $valid_php_code = ''
      , string $Php = 'None'
      , bool $NoPreWrap = true
      , bool $enl = false
      , bool $space = true
      , bool $custom = true
    ): string {
      /*: For documentation purposes.

        Before usage of this function,
        alongside appropriate css afterwards,
        something like this is needed.
        - Once!:

        foreach ([ "bg", "comment", "default", "html", "keyword", "string" ]
          as $value): \ini_set ("highlight.{$value}", "highlight-{$value}");
            unset ($value);
        endforeach;

      */

      if (e ($Q, explode (' ', "\074\077php \077\076"))) {

        e ($el, (($enl) ? EOL  : ''));

        !str_starts_with (ltrim ($valid_php_code), $Q[0]) and e (
          // So that one doesn't need to write php open tags at all
          $valid_php_code, $Q[0] . $el . (($space) ? '  ' : '')
          . ltrim ($valid_php_code)
        );

        !str_ends_with (rtrim ($valid_php_code), $Q[1]) and e (
          // So that one doesn't need to write php close tags at all
          $valid_php_code, rtrim ($valid_php_code) . "{$el} {$Q[1]}"
        );

        e ($code, (NSP . __FUNCTION__) (
          Funcdown-> lintercept ($valid_php_code), true
        )) and e ($valid_php_code, null);

      }

      $custom and e ($code 
      , str_replace ([
          '<code style="color: ',
          '<span style="color: ',
          '<span class="highlight-html">'. EOL,
          '</span>' . EOL . '</span>'. EOL . '</code>',
          '</span>'. EOL . '</code>',
          '<br />', '&nbsp;'
        ], [
          '<code class="',
          '<span class="',
          '<span class="highlight-html">',
          '</span></span></code>',
          '</span></code>',
          EOL, ' '
        ], 
      $code));

      $NoPreWrap and (
        (str_starts_with ($code, '<pre>')
        && str_ends_with ($code, '</pre>'))
        and e ($code, mb_substr ($code, 5, -6))
      );

      e ($PhpTags, [
        0 => '<span class="highlight-default">?&gt;</span>',
        1 => '<span class="highlight-default">?&gt;'. EOL .'</span>'
        , '&lt;?php&nbsp;'
        , '?&gt;' . EOL
        , '?&gt;'
        , '&lt;?php'
      ]);

      return str_replace ((match ($Php) {
          'Open' => $PhpTags[0],     // Just open php tag
          'None' => $PhpTags,        // Just php source without php tags
          default => ''              // As is.
        }
      ), '', $code);
    }

    public function fsIterator (string $directory): FSiterator {
      /// Why would anyone use scandir or similar ?
      return new FSiterator ($directory, FSiterator::SKIP_DOTS);
    }
    
    public function stfObject (
      string $filename = '',
      string $mode = "r",
      bool $useIncludePath = false,
      mixed $context = null,
      int $maxMemory = -1
    ): STFobject|SFobject {
      /*: Wrapper for built-in SplTempFileObject

        Manipulate with files and directories in detailed way,
        using memory wrapper if needed, as this Spl part will provide

        parent::SplFileInfo - Details about file/dir/link ...
        for:
            SplFileObject - Treat file as an object.
            SplTempFileObject - use memory wrapper for temp files.
      */
      return (trim ($filename) === '') ? new STFobject ($maxMemory)
      : new SFobject ($filename, $mode, $useIncludePath, $context);
    }

    public function memory_put_contents (
      ?STFobject &$stfo,
      string &$data,
      ?int &$bytesIn = -1,
      bool $flyCalc = false
    ): bool {
      /*: Similar to file_put_contents only uses direct
          php://memory|temp instead of file:// ..
          Generates $stfo Object, $bytesIn integer and destroys "$data" !

          @stfo = Dynamic SplTempFileObject Instance
          @data = String to put into memory
          @bytesIn - -1 means as much as allowed. No memory limit
          @flyCalc - if false, uses bytesIn or infinity as limit;
                   - if true, calculates memory limit off the data length;
      */

      e ($stfo, $this-> stfObject (maxMemory:(($flyCalc)
      ? mb_strlen ($data) : ($bytesIn ?? -1))))
      and $stfo-> ftruncate(0);

      foreach (explode (EOL, $data) as $line => $content) {
        $stfo-> fwrite ($content . EOL);
        unset ($content, $line);
      } $data = null;

      e ($bytesIn, $stfo-> ftell()) and $stfo-> rewind();
      return is_int ($bytesIn);
    }
    
    public function memory_get_contents (
      STFobject $Mem, int $from = 0,
      ?int $to = null
    ): string {
      /// Retrieves either whole contents or
      /// offset ($From <> $to))portion from memory.      
      $new = ''; foreach ($Mem as $line) $new .= $line; unset ($line); 
      $Mem-> rewind(); return mb_substr ($new, $from, $to);
    }
    
    //~ Statics
    
    public static function generate_eval_string (
      ?string &$check = null,
      bool $NullCheck = true
    ): string {
      /*: Generates supposedly safe string for later-on eval('uation').
          This method expects that string ( preasumably 'file_get_contents')
          is mix of php code and anything else! 
      */

      $EvIal = null;            //~ C-Octal below.
      $Q = "\074\077";          //~ LessThan + QuestionMark
      $Ack = "\342\220\206:";   //~ Standard PHP tags found
      $Spit = "\342\220\202:";  //~ Short Echo tags found
      $Mark = "\342\220\232\342\220\233"; 
      //~ ^^^^^ .. forces Bomb to explode
      
      /* <- prepend one '/'
      var_dump ($Ack, $Spit, $Mark)&exit;
      // Break string into array  */
      $Bomb = array_filter (explode ($Mark,
        str_replace (
        [ "{$Q}php", "{$Q}\075", "\077\076" ], 
        [ "$Mark$Ack", "$Mark$Spit", $Mark ], 
      $check)));
      
      //~ $check is referenced. 
      //~ Will be wasted - if!. 
      if ($NullCheck) $check = $EvIal;
      foreach ($Bomb as $n => $str) {
        $Test = trim ($str); $EvIal .= match (true) {
          default => 'echo '. var_export ($str, true) . ';'
            //~ ^^ This is any other than PHP code text. 
          , (str_starts_with ($Test, $Ack)) => str_replace ($Ack, '', $str)
            //~ ^^ This is for string in between regular php tags.
          , (str_starts_with ($Test, $Spit)) => 'echo '. str_replace ($Spit, '', $str) . ';'
            //~ ^^ This is for string in between 'short-echo' tags.
        } . EOL; unset ($Bomb[$n], $n, $str, $Test);
      } unset ($Bomb); //~ Destroy Bomb. Just because.
      return $EvIal;
    }
    
    public static function opcache_invalidate (string $File, bool $del = false): void {
      /// Removes dead temp file and invalidate it from opcode_cache if cached.
      (function_exists ('opcache_get_status') && is_array (opcache_get_status())
      && opcache_is_script_cached ($File)) and opcache_invalidate ($File, true);
        $del and (!file_exists ($File) or unlink ($File));
    }

  }

}

namespace H {
  ///

  interface OctalPunctuation {
    /// Hardcoder's foundation stones.
    /// Programming control characters.

    const C = [ /// All important, backend punct. chars in C-octal escaped fashion.

      'e' => "\041",      // Exclamation !
      'qt' => "\042",     // Quotation "
      'amp' => "\046",    // Ampersand &
      'ap' => "\047",     // Apostrophe '
      'bl' => "\050",     // Brace parenthesis Left (
      'br' => "\051",     // Brace parenthesis Right )
      'as' => "\052",     // Asterisk *
      'ps' => "\053",     // Plus sign +
      'cm' => "\054",     // Comma ,
      'hm' => "\055",     // Hypen-minus -
      'ss' => "\057",     // Solidus /
      'c' => "\072",      // Colon :
      's' => "\073",      // Semicolon ;
      'lt' => "\074",     // Less than <
      'eq' => "\075",     // Equals sign =
      'gt' => "\076",     // Greater than >
      'qm' => "\077",     // Question Mark ?
      'a' => "\100",      // AT @
      'sl' => "\133",     // Square Bracket Left [
      'esc' => "\134",    // \ <- REAL ESCAPE 
      'sr' => "\135",     // Square Bracket Right ]
      'bt' => "\140",     // Grave accent; BackTick `
      'cl' => "\173",     // Curly Bracket Left {
      'b' => "\174",      // Bar |
      'cr' => "\175",     // Curly Bracket Right }
      't' => "\176",      // Tilde ~
      'p' => "\045",      // Percent %

    ];

    const U = [ /// Some specific System Characters - in C-octal escaped fashion.

      'tm' => "\342\204\242",        // TRADEMARK ™
      'sea' => "\342\206\230",       // SOUTH EAST ARROW ↘
      'lw' => "\342\254\220",        // LEFTWARDS ARROW WITH TIP DOWNWARDS ⬐
      'ea' => "\342\245\261",        // EQUALS SIGN ABOVE RIGHTWARDS ARROW ⥱
      'l' => "\302\253",             // LEFT-POINTING DOUBLE ANGLE QUOTATION MARK «

      'tnode_start' => "\313\273",   // RUNIC LETTER LONG-BRANCH-OSS O ˻
      'tnode_end' => "\313\271",     // RUNIC LETTER OE ˹

    ];

    const MINUTE = 60; ///
    const HOUR = (self::MINUTE * 60); ///

  }

  class Application extends \StdClass implements OctalPunctuation {
    /*:*/ use \H\Root\application_class_obstructor; /*:*/
  }
}

  /*
    The above three namespace blocks are dependancy part of Hng-ToolSet.
  */

namespace H\bag\funcdown {
  /// Library for the Funcdown Extension

  use \H\OctalPunctuation as P; ///

  trait Workers {
    /// Funcdown parser compound

    private function death_signature (string $sig_text): string {
      /// Dashed signature for the (have plain text mode in mind) main message.
      return $sig_text . EOL . str_repeat ('-', mb_strlen ($sig_text)) . EOL;
    }

    private function e_code_not_false (string $suspect): never {
      ///

      $this-> strict_rules();
      if (!$this-> silent_errors)
        print_r ($suspect);
      exit;
    }

    private function strict_rules (string $suspect = 'martyr', string|null $identifier = null): void {
      /// Display categorized error/warning messages with custom text (if any).

      $addition = $this-> $suspect;
      if (is_array ($addition)) $addition = implode (' ', $addition);

      $this-> log_identifier = ((is_string ($identifier)) ? $identifier : 'funcdown extension');
      $traceslice_debug = $this-> traceslice_debug (debug_backtrace(), $addition);
      $this-> log_destruct();

      if (!$this-> silent_errors) {
        header ('X-Powered-By: Syntax Watcher for PHP-Funcdown');
        header ('Content-Type: text/plain; charset=utf-8')&die (
          $this-> death_signature (
          '** Death of this request is caused by [ '. __METHOD__ .' ]'
        ) . $traceslice_debug);
      }
    }

    private function traceslice_debug (array $debug_backtrace, $addition = null): string {
      /// DEATH messenger's second hand.

      $draw = []; array_shift ($debug_backtrace);
      $debug_backtrace = array_reverse ($debug_backtrace);
      for ($c = 0; $c < count ($debug_backtrace); $c++)
      foreach ($debug_backtrace[$c] as $index => $item)
      {
        if (!is_scalar ($item)) unset ($debug_backtrace[$c][$index]);
        else
        {
          $n = (count ($draw) + 1);
          $file = $debug_backtrace[$c]['file'];
          $line = $debug_backtrace[$c]['line'];
          $type = ($debug_backtrace[$c]['type'] ?? '');
          $function = $debug_backtrace[$c]['function'];
          $class = ($debug_backtrace[$c]['class'] ?? '');
          $proc_type = (($index === 'type'
            && $type !== '') ? 'function' : 'method'
          );

          $$proc_type = ("$class{$type}$function");
          if (!array_key_exists ($file, $draw))
          {
            $draw[$file] = (" file_$n :" . P::U['ea'] ." $file".
              EOL . '  ' . P::U['sea'] . EOL);
          }
          else
          {
            $ptn = (" {$proc_type}_name"); // procedure_type_name
            $draw[$file] .= "  $ptn: {$$proc_type} @ LINE: $line". EOL;
            break;
          }
        }

        unset ($item, $index);
      }

      unset ($c, $debug_backtrace);
      $draw = EOL . implode (EOL, array_values ($draw));
      $mssgtxt = $addition . EOL .' '. P::U['sea'] . EOL .
        '  '. self::PROPERTY['e_mssg'][$this-> e_code];

      if ($this-> error_logging && property_exists ($this, 'log_identifier')) {

        $ServerCoreTest = ((!class_exists ('\\H\\NextGenerationToolset')) ? false : \H\Api::do()-> read ('server_core'));
        $logfile_basedir = ((!is_array ($ServerCoreTest)) ? [ 'appool' => $this-> temp_path ] : $this-> cdc ('dc', $ServerCoreTest, 2));

        $this-> log_identifier = [
          'logfile' => "{$logfile_basedir['appool']}/application_log/hng_funcdown_errors",
          'lfcontent'=> ("[" . FUNCDOWN_ATOM_REQUEST . "] problem detected by {$this-> log_identifier}; ".
            $addition . EOL . $draw . EOL )
        ];
      }

      return ($mssgtxt . (($this-> null_expose) ? '' : EOL . EOL
        . EOL . "{$this-> death_signature ('trace_calls:')} {$draw}"));
    }

    private function log_destruct(): void {
      /// Appends error message to php log file. Destroys message afterwards.

      if ($this-> error_logging && property_exists ($this, 'log_identifier')
      && is_array ($this-> log_identifier) && (count ($this-> log_identifier) === 2)) {

        e ($dname, dirname ($this-> log_identifier['logfile']))
          and $this-> dirOr ($dname); unset ($dname);
        if (file_put_contents ($this-> log_identifier['logfile'],
          $this-> no_unix_control_chars ($this-> log_identifier['lfcontent'])
        , FILE_APPEND | LOCK_EX)) unset ($this-> log_identifier);
      }
    }

    private function selective_defaults(): void {
      /// Resets all heavy properties, and counters as well
      /// (Memory consumption + raw backend processed data) = taken care of.
      /// Content is outputted when trigger becomes `release`(d).

      is_null (self::$microint) or e (self::$microint, null);
      is_null (self::$mcd_transport) or e (self::$mcd_transport, null);

      foreach (self::PROPERTY as $pname => $value) {
        (property_exists ($this, "$pname") && !in_array (
          $pname, [ 'dtd_check', 'tab_depth' ], true
        )) and e ($this-> $pname, $value);
        unset ($value, $pname);
      }
    }

    private function alter_linter_counter (string $input): void {
      /// Three-major + Bar counter

      foreach (array_combine
      (array_keys ($this-> linter_count),
      [ P::C['b'],
        [ 'l' => P::C['bl'], 'r' => P::C['br'] ],
        [ 'l' => P::C['cl'], 'r' => P::C['cr'] ],
        [ 'l' => P::C['sl'], 'r' => P::C['sr'] ]
      ]) as $lc_index => $char)
      $this-> linter_count[$lc_index] = (
        (is_array ($char)) ? [
          'l' => mb_substr_count ($input, $char['l']),
          'r' => mb_substr_count ($input, $char['r'])
        ] : mb_substr_count ($input, $char)
      ); unset ($char, $lc_index);
    }

    private function linter_signal_spark(): void {
      /// Seeks for bad signs in one shot via linter_count property

      $blank = []; $bar = ($this-> linter_count['bar'] > 0);
      $check_test = \array_slice (\array_keys ($this-> linter_count), 1);
      foreach ($check_test as $type)
      {
        $brace_type = [
          'l' => ($this-> linter_count[$type]['l'] > 0),
          'r' => ($this-> linter_count[$type]['r'] > 0),
          'identical' => ($this-> linter_count[$type]['l'] === $this-> linter_count[$type]['r'])
        ];

        if (!$brace_type['identical']) {
          $this-> e_code = 'equals_not';
          $this-> martyr = 'BRACKET ' . P::U['ea'] . " `$type` unequal.";
          break;
        }
        else {
          if (!$brace_type['l'] && !$brace_type['r'])
            $blank[$type] = true;
        }

        unset ($brace_type, $type);
      }

      if ($this-> e_code === 'equals_not')
        return;

      $cblank = count ($blank);
      if (!$bar && $cblank === 3) {
        $this-> e_code = 'no_funcdown';
        $cause = ((mb_strpos ($this-> martyr, DSP) !== false) ? 'INVALID FILEPATH' : 'PROVIDED INPUT');
        $this-> martyr = "{$cause} " . P::U['ea'] . " {$this-> martyr}";
        return;
      }
    }

    private function blueprint_populate(): void {
      /// Fill the nasty array with reckognized tags and their values and roles.

      $bp = [];
      $lc4 = [ P::C['b'], P::C['cr'], P::U['tnode_start'], P::U['tnode_end']]; # 4 allowed last punct chars
      $chars = [P::C['bl'], P::C['br'], P::C['cl'], $lc4[1] ];
      foreach ($this-> valid_structure as $nt => $food)
      {
        [$n, $tab] = explode(':', $nt);
        $fc = mb_substr ($food, 0, 1);
        $lc = mb_substr ($food, -1);

        if (!$this-> clean_blueprint ($n, $fc, $lc, $food, $lc4))
        {
          $this-> e_code = 'fncd_invalid';
          $bp = $food; break;
        }
        else
        {
          [ $role, $shrink ] = match ($lc) { // Let's see what last character has to say ..
            $lc4[1] => [ 'STRING', null ],
            $lc4[2] => [ 'PARENT', -1 ],
            $lc4[0] => [ 'SELF', -1 ],
            $lc4[3] => [ 'END', -1 ]
          };

          if (!is_null ($shrink)) $food = mb_substr ($food, 0, $shrink);
          $nat = array_values (array_filter (explode (' ', trim (str_replace ($chars, ' ', $food)))));
          if (!$this-> string_filter_rule ($nat[0], ['.',':','-','_']))
          {
            if (mb_strlen ($nat[0]) !== 1)
            {
              $this-> e_code = 'fncd_invalid';
              $bp = "{$n}:{$food}";
              break;
            }
          }

          $cnat = count ($nat); // Name, Attributes, Text - that is - max 3 entries per array.
          if ($cnat <= 3)
          {
            $bp[$n] = self::XML_BLUEPRINT;
            $bp[$n]['tabs'] = $this-> tab_setter ($tab);
            $bp[$n]['role'] = $role; $bp[$n]['type'] = (
              ($nat[0] === P::C['t']) ? 'TEXT' : (($nat[0] === P::C['as'])
              ? 'DTD'  : (($nat[0] === P::C['e']) ? 'COMMENT' : 'TAG' ))
            );

            $bp[$n]['name'] = (($this-> tags_expand)
              ? (self::N_EXPAND[$nat[0]] ?? $nat[0])
              : $nat[0]
            );

            if ($bp[$n]['role'] !== 'END')
            {
              switch ($cnat)
              {
                default: case 1: break;
                case 2: $bp[$n][((in_array ($bp[$n]['role'],
                ['PARENT', 'SELF' ], true)) ? 'attr':'text')] = $nat[1]; break;
                case 3: $bp[$n]['attr'] = $nat[1]; $bp[$n]['text'] = $nat[2]; break;
              }
            }
          }
          else
          {
            $this-> e_code = 'fncd_invalid';
            $bp = "{$n}:{$food}";
            break;
          }

        }

        unset ($n,$tab,$fc,$lc,$tcbp,$nt,$food);
      }

      if (!is_array ($bp) && $this-> e_code !== false) {

        $rlineT = 'RAW ENCODED BUGGY SPOT';
        $slineT = 'SUSPECT TAG';
        $bp = explode (P::C['c'], $bp);

        if (isset($bp[1])) {
          $uc = 'SYNTAX ERROR';
          $a = $slineT;
          $a1 = $rlineT;
          $b = $bp[1];
        }
        else {
          $uc = 'GEORGE BUSH ERROR';
          $a = $rlineT;
          $a1 = $slineT;
          $b = 'unknown';
        }

        $this-> martyr = "$uc " . P::U['ea']
          . " $a: {$bp[0]}; $a1: {$b}";
        return;
      }
      else {
        $this-> structure_info = $bp;
      }
      unset ($bp);

    }

    private function clean_blueprint ($line, $first_char, $last_char, $tag_content, $lc4): bool {
      /// Dismisses/handles all remaining errors before tag details format occurs

      $length = mb_strlen ($tag_content);
      if ($length < 2) return false; // This `length` scenario is totally inacceptible.
      else if ($length === 2) // This scenario is for single-letter tags acting like END role type, without arguments.
        return (ctype_alpha ($first_char) && in_array ($last_char, array_slice ($lc4, -2), true));
      else if ($length > 2 ) // This is where things get a bit more interesting.
      {
        if (ctype_alpha ($first_char) && in_array ($last_char, $lc4, true))
        { // If first and last characters are compliant
          $this-> alter_linter_counter ($tag_content); // Re-Populate linter counter for each line
          $bar = $this-> linter_count['bar']; // How many of these `|`  .. ?
          $lcc = ( $this-> linter_count['curly']['l'] + $this-> linter_count['curly']['r'] ); // Sum total number of { and } found
          $lcp = ( $this-> linter_count['parenthesis']['l'] + $this-> linter_count['parenthesis']['r'] ); // Sum total number of ( and ) found
          if ($bar > 0 && $bar !== 1) return false; // There can be only one self-closed tag per line
          foreach ([$lcc, $lcp] as $pctcount): if($pctcount > 0 && $pctcount !== 2 && $pctcount < 2):
            // There can be only one pair of parenthesis w/out only one pair of curly braces - per line.
            $retval_abort = false; break; endif;
          endforeach; return ($retval_abort ?? true);
        }
        else // First and last characters are not compliant .. ?
        {
          // If first char isn't one of three ~!* abort.
          if (!in_array ($first_char, [P::C['t'], P::C['e'], P::C['as']], true))
            return false;
          else // Abort if the second and the last char aren't correct ones.
            return (mb_substr ($tag_content,1,1) === P::C['cl'] && $last_char === $lc4[1]);
        }
      }
    }

    private function string_code (string $string) {
      /// What's the funcdown input? Filepath or a direct/array-converted string ?
      /// TODO COPY EDITED METHOD WHEN DONE

      clearstatcache();
      if (!is_array (@file ($string))) {
        $this-> string_input_type = 'martyr';
        $this-> martyr = $string;
      }
      else
        $this-> string_input_type = 'file';

      if ($this-> string_input_type !== 'martyr')
      { // Our string argument is a file_path .. ?
        $rps = (@realpath ($string));
        if ($rps !== false && is_string ($rps) &&
        (!is_dir ($string) || is_link ($string)) && file_exists ($rps))
        {
          $this-> martyr = (($rps !== $string) ? $rps : $string);
          $ps = pathinfo ($this-> martyr); $e = $ps['extension'];
          switch ($e)
          {
            default: $this-> e_code = 'extension';
              $strict_rules_msg = mb_strtoupper ($this-> e_code).
              " ERROR " . P::U['ea'] . " filename should have `.fncd` {$this-> e_code}, but `.$e` found.";
            break;
            case 'fncd': $strict_rules_msg = false; break;
            case 'phtml':
              $exp = explode ('.', $ps['filename']); $exp = end ($exp);
              if ($exp === 'fncd') $strict_rules_msg = false;
              else {
                $this-> e_code = 'extension';
                $strict_rules_msg = mb_strtoupper ($this-> e_code).
                  " ERROR " . P::U['ea'] . " PHTML filename can have .{$e} {$this-> e_code}, ".
                  "but not without .fncd (*.fncd.{$e}) where `{$exp} + {$e}` found.";
              }

            break;
          }

          if (!$strict_rules_msg && $this-> e_code === false) {
            $fgc = file_get_contents ($this-> martyr);
          }
          else {
            if ($this-> e_code !== false
            && is_string ($strict_rules_msg)) {
              $this-> martyr = $strict_rules_msg;
              if (!$this-> silent_errors)
                return print_r ($string, true);
            }
          }
        }
      }

      if ($this-> inputunique === null)
      { // At this point, input_content is definitelly - string. MUST be.
        // And inputunique SHOULD NOT co-exists without input_content.
        $input_content = ($fgc ?? $this-> martyr);
        $this-> inputunique = sha1 (implode
         (':', [ mb_strlen ($input_content), $input_content ]
        ));
      }

      $this-> starcut_examine ($input_content, $Ex);
      $this-> backend_string_code ($Ex); unset ($Ex);
      e ($this-> martyr, $this-> 
        normalize_escaped_backend 
          ($this-> martyr));
    }

    private function backend_string_code (?string &$input_content = null): void {
      /// Deal with mixed funcdown/php code ##~

      // Search for PHP source code wrappers and for short echo substitutions.
      if ($this-> backend_notgood) { //
        $input_content = $this-> wipe_backend_strings (
          str_replace (self::PHPS[0], self::PHPS[1], $input_content));
          if (isset ($this-> aidpick)) {
            $this-> aidpick = null;
            unset ($this-> aidpick);
          }
      }

      $php = [];

      if ($this-> avoid_phpxml)
      { /*
          Make sure that there is no mix of real php/xml with substitution
          tags anywhere, if avoid_phpxml property is not false but true.
        */
        foreach ([
          self::PHPS[1]['a'], self::PHPS[1]['e'],
          self::C['lt'] . self::C['qm'], self::PHPS[1]['z']
        ] as $original_xml_alike) {

          if (mb_substr_count ($input_content, $original_xml_alike) > 0) {
            $this-> e_code = 'php_tags';
            $this-> martyr = 'PHP ERROR ' . P::U['ea']
              . " Don't use original PHP/xml tags within funcdown source.";
            break;
          }
        }

        if (is_string ($this-> e_code)) return;
      }
      else {
        foreach (array_keys (self::PHPS[1]) as $substitution)
        $php[] = mb_substr_count ($input_content, self::PHPS[1][$substitution]);
      }

      foreach (array_keys (self::PHPS[0]) as $substitution)
        $php[] = mb_substr_count ($input_content, self::PHPS[0][$substitution]);
        unset ($substitution); $phpint = implode (':', $php);

      if (in_array ($phpint, [
        '0:0:0', // <= no mix of codes possible
        '0:0:0:0:0:0' // <= mix of codes is possible.
      ], true))
      { // No backend code found !
        if (isset ($this-> aidpick)):
        require_once $this-> aidpick;
        unset ($this-> aidpick); endif;
        e ($this-> martyr, $input_content)
          and e ($input_content, null);
      }
      else {

        // If phpint is all zero's , it means that there is
        // no PHP source code alongside funcdown source,
        // where in scenario below, that clearly isn't the case.

        if (isset ($php[5])) [$a, $b, $c, $d, $e, $f] = $php; else [$a, $b, $c] = $php;
        if ($c !== ($a + $b) || (isset ($f) && $f !== ($d + $e))) {
          //~ ^~ simple math for the open/close php tags

          e ($this-> e_code, 'php_tags')
          and e ($this-> martyr, 'PHP ERROR ' . P::U['ea']
            . ' The number of PHP_CODE tags/substitutions found in '
            . (($this-> string_input_type !== 'file')
            ? 'argument from last method call '
            : "'{$this-> martyr}' ")
            . 'is incorrect.'
          ); //!
          return;
        }
        else
        {
          ob_start();
            // There is php code and it seems like valid syntax inside.
            // Proceed. `Evil`-uate (eval()) with or without require.
            // May God have mercy on my developer's soul.

          e ($bp, [
            $this-> filesystem_temp_point ('funcdown'),
            $this-> inputunique
          ]) and e ($temp_file_path, (($this-> temp_path !== "")
            ? $this-> joinDsp ($this-> temp_path, $bp[0], $bp[1])
            : $this-> joinDsp ($_SERVER['DOCUMENT_ROOT'],
              'public', 'tmp', $bp[0], $bp[1])) . '.php'
          ) and e ($dname, dirname ($temp_file_path))
          and $this-> dirOr ($dname)
          and e ($input_content,
          str_replace (
            array_values (self::PHPS[0]),
            array_values (self::PHPS[1]),
            $input_content
          ));

          if ($this-> evialuate)
          { // eval opcode from memory. Faster but perhaps unpredictable ?
            foreach (get_defined_vars() as $index => $variable) {
              if (!in_array ($index, [ 'input_content' ], true))
              unset ($$index); unset ($variable, $index);
            }

            if (isset ($this-> aidpick)): require_once $this-> aidpick; unset ($this-> aidpick);
            endif; eval (self::generate_eval_string ($input_content, true)); unset ($input_content);
            $this-> martyr = ob_get_clean();
          }
          else // Standard, safe and recommended PHP opcode evaluation
          {
            if (file_put_contents ($temp_file_path, $input_content, LOCK_EX)) {

              e ($input_content, $temp_file_path);
              foreach (get_defined_vars() as $index => $variable) {
                if (!in_array ($index, [ 'input_content', 'dname' ], true))
                  unset ($$index);
                unset ($variable, $index);
              }

              // Not Ev(i)al include
              (function (&$filepath, $directory) {
                if (isset ($this-> aidpick)):
                require_once $this-> aidpick;
                unset ($this-> aidpick); endif;
                (require $filepath) and (
                  e (static::$temp_path_nanny, [
                    $directory, array_slice (scandir ($directory), 2)
                  ]) and static::opcache_invalidate ($filepath, true)
                ); $filepath = null;
              }) ($input_content, $dname);
              unset ($dname, $input_content);
              $this-> martyr = ob_get_clean();
              // ^^ File parsed and packed back
              // into variable as pure fncd string.
            }
            else {

              echo  __METHOD__ . ' says: Bad config. '
              . 'Incorrect filesystem read/write permissions.';
              die (ob_end_clean());

            }
          }
        }
      }
      unset ($phpint, $php);
    }

    private function starcut_examine (string &$x, &$new): void { /*:
        SuperStar aid files handler.
        When using funcdown from within separated file,
        one can put dynamic PHP opcode for the particular
        request behind one single file and invoke it like: *{:myFile}
        or alsongside dtd selection: *{5:myFile}
        - just the basename prefix of the file -
        In the above scenario: $this-> aid_path . '/myFile.aid.php'
        would be the real path to corresponding aid file.
      */

      e ($Mc, fn($a): string => match ($a) {
        /// Picks correct basename
        default => ((!ctype_alpha ($a))
          ? '' : "$a.aid.php"
        ), 'self' => 'default.aid.php'
      }) and e ($new, $x) and e ($x, null);
      if (e ($Bomb, explode (EOL, $new)))
      foreach ($Bomb as $line => $data)
      {
        e ($Test, $this-> one_line ($data, true));
        if ($Test !== '' && str_ends_with ($Test, P::C['cr'])
          && str_starts_with ($Test, P::C['as'] . P::C['cl'])
        ) {
          e ($Check, mb_substr ($Test, 2, -1));
          if (str_contains ($Check, P::C['c'])) {

            e ($p, mb_strpos ($Check, P::C['c']))
            and e ($a, mb_substr ($Check, 0, $p))
            and e ($b, mb_substr ($Check, ($p + 1)));

            if ($b !== '') {
              !property_exists ($this, 'aidpick') and e ($this-> aidpick, $Mc($b));
              if ($this-> aidpick === '') unset ($this-> aidpick);
              else {
                e ($this-> aidpick, $this-> joinDsp ($this-> aid_path, $this-> aidpick))
                and e ($dname, dirname ($this-> aidpick)) and $this-> dirOr ($dname);
                if (!file_exists ($this-> aidpick)) unset ($this-> aidpick);
              }
            }

            $Bomb[$line] = ((!in_array ($a, ['5', 'X'], true))
            ? '!{}' : "*{{$a}}"); unset ($p, $a, $b);
            $new = implode (EOL, $Bomb); break;
          } unset ($Check);
        } unset ($Test);
      } unset ($data, $line, $Bomb);
    }

    private function wipe_backend_strings (string $backend_string): string {
      /// Return string with no php tag wrappers and no content in between
      $E = P::C['esc']; return preg_replace_callback_array ([
        "~$E<$E?php(.*?)$E?$E>~imsu" => fn($c) => '',
        "~$E<$E?$E=(.*?)$E?$E>~imsu" => fn($b) => 'no_backend',
        "~$E<$E?(.*?)$E?$E>~imsu" => fn($a) => ''
      ], $backend_string);
    }

    private function normalize_escaped_backend (string $input_string): string {
      /// Returns escaped php code tags to readable php code tags

      $p = P::C['lt']; $q = P::C['qm'];
      $g = P::C['gt']; $e = P::C['eq'];
      $c = P::C['c'];  $L = P::C['cl'];
      $R = P::C['cr']; $E = P::C['esc'];
      $pOp = "$p$q"; $pCl = "$q$g";
      $fCl = "$L$L";$fCr = "$R$R";

      return str_replace ([

        0 => "$E$p$E{$q}php",      // \<\?php
        1 => "$E$p$E$q$E$e",       // \<\?\=
        2 => "$E$p$E$q",           // \<\?
        3 => "$E$L$E$L$c",         // \{\{:
        4 => "$E$L$E$L$e",         // \{\{=
        5 => "$E$q$E$g",           // \?\>
        6 => "$c$E$R$E$R",         // :\}\}

      ], [

        0 => "{$pOp}php",
        1 => "$pOp$e",
        2 => $pOp,
        3 => "$fCl$c",
        4 => "$fCl$e",
        5 => $pCl,
        6 => "$c$fCr"

      ], $input_string);

    }

    private function structure_digest (array &$raw): void {
      /// Normalize funcdown catch and set tab count for pretty-parsed source.
      /// Afterwards, 'valid_structure' property gets determined.

      e ($out, []);
      foreach ($raw as $n => $string) {
        e ($out, array_merge ($out,
          explode (' ', str_replace (
          [ P::U['tnode_start'], P::U['tnode_end'] ],
          [ P::U['tnode_start'] . ' ', P::U['tnode_end'] . ' ' ],
          $this-> one_line ($string, true)))
        )); unset ($string, $n);
      }

      e ($raw, [])
      and e ($tabs, $this-> tab_depth)
      and e ($out, array_filter ($out));
      foreach ($out as $n => $stable) {
        e ($poke,  mb_substr ($stable, -1));
        (($poke === P::U['tnode_start']) ? $tabs++
          : (($poke === P::U['tnode_end']) ? $tabs--
            : $tabs
          )
        );

        e ($this-> tab_depth, (($tabs > 0) ? ($tabs - 1) : $tabs));
        $raw["$n:{$this-> tab_depth}"] = $stable;
        unset ($stable, $out[$n], $n);
      } unset ($out);

      e ($this-> valid_structure, $raw)
      and e ($raw, null); // reference gets destroyed
      //~ print_r ($this-> valid_structure); exit;
    }

    private function input_conclusion (string $input): array {
      /// Marks all right `]` characters correctly and drops empty lines

      if (mb_strpos ($input, "*)];") !== false) {
        $this-> e_code = 'fncd_invalid';
        $this-> martyr = 'BAR ' . P::U['ea']
          . " is missing for the self-closing tag. Total misses: ".
        mb_substr_count ($input, "*)];");
      }

      $d_tag = []; // BLOCK-WRAPPER open/close `d`addy_tag.
      $e = explode (P::C['s'], $input);
      foreach ($e as $n => $str)
      {
        $ftag = mb_strpos ($str, P::C['sl']);
        $stag = mb_strpos ($str, P::C['sr']);
        if ($ftag !== false)
        {
          $r = explode (' ', $e[$n]);
          foreach ($r as $q => $l)
          {
            $first_char = mb_substr ($r[$q],0,1);
            $last_char = mb_substr ($r[$q],-1);

            if ($last_char === P::C['sl'])
              $d_tag[] = trim (($first_char === P::C['bl'])
                ? $r[($q - 1)] : mb_substr ($r[$q], 0,-2)
              );

            unset ($last_char, $first_char, $l,$q);
          } unset($r);

          $e[$n] = str_replace ((P::U['l'] . P::C['sl'] .' '), (' '. P::U['tnode_start'] .' '), $e[$n] );
        }

        if ($stag !== false) {
          $marker = array_pop ($d_tag);
          $e[$n] = str_replace (P::C['sr'], ($marker .' '. P::U['tnode_end']), $e[$n] );
        }

        $e[$n] = trim (str_replace ([ P::C['cl'], P::C['b'] ],
          [(' '. P::C['cl']), (' '. P::C['b'])], $e[$n]));
        unset ($stag, $ftag, $str, $n);
      }

      unset ($d_tag);
      return array_values
      (array_filter ($e));

    }

    private function semicolon_starcoded (string $starcoded): string {
      /// Mark starcoded string with semicolons wherever needed.

      return str_replace ([
          0 => P::C['t'] . ' ' . P::C['cl']
          , 1 => P::C['e'] . ' ' . P::C['cl']
          , 2 => P::C['s'] . P::C['sl']
          , 3 => P::C['bl']
          , 4 => P::C['sl']
        ]
        , [
          0 => P::C['t'] . P::C['cl']
          , 1 => P::C['e'] . P::C['cl']
          , 2 => P::C['sr'] . P::C['s']
          , 3 => ' ' . P::C['bl']
          , 4 => P::U['l'] . P::C['sl'] . ' '
        ]
        , $this-> one_line (str_replace ([
            0 => P::C['cr']
            , 1 => P::C['sr']
            , 2 => P::C['b']
          ], [
            0 => P::C['cr'] . P::C['s']
            , 1 => P::C['sr'] . P::C['s']
            , 2 => ' ' . P::C['b'] . P::C['s']
          ], $starcoded), true)
      );
    }

    private function attributes (string $input_string): mixed {
      /// Multi-purpose attributes handler

      $first_explode_char = self::C['cm']; $safe_array = [
        self::C['cm'] => self::RPL_SET['_'][2][18]
      ];

      if (!$this-> find_micro_attributes && !$this-> micro_attributes_parse) {
        //~ $first_explode_char = self::C['cm'];
        $input_string = $this-> starcode ('reveal', $input_string);
      }
      else {
        if (!$this-> micro_attributes_parse && $this-> find_micro_attributes) {
          $first_explode_char = self::C['b'].' ';
          foreach (['b' => 15, 'bt' => 13 ] as $x => $i)
          $safe_array[self::C[$x]] = self::RPL_SET['_'][2][$i];
        }
      }

      $check = array_filter (explode ($first_explode_char, self::syschars_escaped
          (mb_str_split (trim ($input_string)), $safe_array)));

      if ($this-> find_micro_attributes)
      {
        $tc_z = trim ($check[0]);
        $first = $tc_z[0]; $last = mb_substr ($tc_z, -1);
        $micro_check = explode (self::C['bt'], mb_substr
        ($tc_z, 1, (($last === self::C['b']) ? -1:null)));
        $this-> microtextnode_configured = [
          0 => implode (' ', array_slice ($check, 1)),
          1 => array_filter ($micro_check)
        ];
      }

      if (!$this-> find_micro_attributes)
      {
        foreach ($check as $n => $content)
        {
          $check[$n] = trim ($content);
          $espace = explode (' ', $check[$n]);
          $r_aname = array_shift ($espace);
          $aname = ((!$this-> attr_expand) ? $r_aname
            : $this-> short_attribute_name ($r_aname)
          );

          /*##~ VALID PUCNCTUATION CHARACTERS FOR ATTRIBUTE NAMES ~##*/
          if (!$this-> string_filter_rule ($aname, ['.',':','-','_']))
          {
            $this-> e_code = 'fncd_invalid';
            $this-> martyr = 'XML STANDARDS '. P::U['ea']
              . " `$r_aname` is not valid attribute name.";
            break;
          }
          else
          {
            $espace = implode (' ', $espace);
            if (isset ($espace[0]))
            {
              $wrap = null;
              $esz = $espace[0];
              $c = [ P::C['as'], P::C['qt'], P::C['ap'] ];

              if ($this-> quotes_only)
              {
                $wrap = $c[1];
                if (in_array ($esz, $c, true)) {
                  $espace = mb_substr ($espace, 1);
                  if ($esz === $c[0]) $wrap = null;
                  if ($esz === $c[2]) $wrap = $c[2];
                }
              }
              else {
                switch ($esz) {
                  case $c[0]: $espace = mb_substr ($espace, 1);
                  break;
                  case $c[1]: case $c[2]:
                    $wrap = $esz;
                    $espace = mb_substr ($espace, 1);
                  break;
                }
              }
            }
            else
            {
              if ($aname === 'data-attribute-error')
              {
                $this-> e_code = 'fncd_invalid';
                $this-> martyr = 'XML STANDARDS ' . P::U['ea']
                  . " `$r_aname` is not valid attribute name.";
                break;
              }
            }

            // String for the `tongue`, risky.
            [$primary, $key, $twin] = $this-> filter_whitelisted_attributes ($aname);
            if (mb_strlen (trim ($espace)) > 0)
            {
              $safe = $this-> tongue_make_text_twin ($primary, $key, $twin, $espace, 42);
              $w = ($wrap ?? ''); $espace = "$w$safe$w";
            } else $espace = false;
          }

          if (!$this-> e_code && $espace !== false)
          // This is the place where it's determined whether attribute remains naked, without value, or not.
          $check[$n] = ( $aname . ((mb_strlen ($espace) === 2 && in_array ($espace[0], ['"',"'"], true)) ? ''
          : (self::C['eq'] . $espace))); unset ($content, $n);
        }

        if ($this-> e_code !== false) {
          $this-> strict_rules();
          if (!$this-> silent_errors) return print_r ($input_string, true);
        } else return implode (' ', $check);
      }
      return null;

    }

  }

  trait Microdown {
    /// MicroDown `bag`

    private function microdown_handler(): void {
      /// Nicely fix all shite within $this-> tongue microdown entries

      if ($this-> tongue['microdown'] === [] && isset ($this-> tongue['selection']['mt']) && (
        is_array ($this-> tongue['selection']['mt']) && $this-> tongue['selection']['mt'] !== []
      ))
      {
        $eol = self::EOL_MASK;
        $twin = array_flip (self::L_MARKS);
        $suspect = implode ($eol, $this-> tongue['selection']['mt']);
        $seek_and_destroy = $this-> transplace_microdown ($suspect, $twin['sm']);
        if (isset ($this-> bus_temp))
        {
          $this-> tongue['microdown'] = $this-> html_microformat ('bus_temp');
          $this-> tongue['selection']['mt'] = (@array_combine (
            array_keys ($this-> tongue['selection']['mt']),
              explode ($eol, $seek_and_destroy)
          ));

          $this-> iron_tongue();
        }
      }
    }

    private function transplace_microdown (string $suspect, string $twin): string {
      /// Find and replace all matches in a string

      if (!\is_null ($suspect) && \is_string ($suspect)) {
        $mcd = $this-> parse_microdown ($suspect, self::C, 0, $twin);
        $this-> bus_temp = static::$mcd_transport; return $mcd;
      }
    }

    private function parse_microdown (string $input, array $p, int $microint, string $twin): string {
      /// A bit more `divided` preg_replace_callback implementation

      self::$microint = $microint;

      $allTags = str_replace ([ $p['hm'], $p['e'] ],
        [ "{$p['esc']}{$p['hm']}", "{$p['esc']}{$p['e']}" ],
          implode ('', array_merge (self::MICRODOWN['SCT'],
          array_keys (self::MICRODOWN[0])
        ))
      );


      if ($this-> microBlack !== []) {
        $allTags = str_replace (array_keys ($this-> microBlack), '', $allTags);
      }

      $pattern = $p['t'].$p['sl'] . $allTags . $p['sr'] . $p['ps'] . $p['esc']
        . $p['ss'] . $p['esc'] . $p['ss'] . $p['bl'] . '.' . $p['as'] . $p['qm']
        . $p['br'] . $p['esc'] . $p['ps'] . $p['t']
        . 'imsu';

      return preg_replace_callback ($pattern, function ($x) use ($p, $twin) {
          $char = $this-> callback_method_microstring ($x, $p, $twin);
          self::$microint++; return $char . self::$microint . $char;
        },
        self::syschars_escaped (
          mb_str_split ($input),
          [ $p['ps'] => self::RPL_SET['_'][2][20] ]
        )
      );

    }

    private function callback_method_microstring (?array $x, array $p, string $twin): string {
      /// This method runs within preg_replace_callback - within callback function.

      if (is_array ($x) && isset ($x[1]))
      {
        $new = []; $ss = $p['ss'].$p['ss'];
        $e = explode ($ss, $x[0]); $tags = mb_str_split ($e[0]);

        foreach ($tags as $n => $element) { // $element(s) as detected tag(s)
          if (!isset ($this-> microBlack[$element])) {
            $new[] = str_replace (array_keys (self::MICRODOWN[0]),
            array_values (self::MICRODOWN[0]), $element);
          } unset ($element, $n);
        }

        // Transfer important values for microdown first
        static::$mcd_transport[(self::$microint + 1)] = [ $x[1], 'T' => $new ];
        unset ($new, $tags, $p, $e);
        return $twin;
      }
    }

    private function html_microformat (string $property): array {
      /// microdown_handler helper

      if (property_exists ($this, "$property"))
      {
        $a = $this-> $property;
        unset ($this-> $property);

        foreach ($a as $c => $matherial)
        {
          $a[$c] = $this-> microtextnodes ($matherial);
          unset ($matherial, $c);
        }

        return $a;
      }
    }

    private function microtextnodes ($matherial): array {
      /// html_microformat loop helper

      $check = $matherial[0];
      $open = mb_substr_count ($check, self::C['a']);
      $close = mb_substr_count ($check, self::C['b'].' ');
      if ($open > 0 && $close > 0 && $check[0] === self::C['a'])
      {
        $this-> find_micro_attributes = true;
        $this-> attributes ($check);

        if (property_exists ($this, 'microtextnode_configured')) {
          $matherial[0] = array_shift ($this-> microtextnode_configured);
          $matherial['A'] = $this-> microtextnode_configured[0];
          $this-> microtextnode_configured = null;
          unset ($this-> microtextnode_configured);
        }
      }
      return $matherial;
    }

    private function iron_tongue(): void {
      /// Final ironing over the language ($this-> tongue) holder

      if (!isset ($this-> tongue['selection']['sm']))
        $this-> tongue['selection']['sm'] = [];

      $this-> find_micro_attributes = false;
      $this-> micro_attributes_parse = true;
      $microdown = $this-> tongue['microdown'];

      $a = self::C['lt']; $b = self::C['ss'];
      $z = self::C['gt']; $zz = (" $b$z");
      $twin = array_flip (self::L_MARKS)['sm'];
      $tag_special = fn($tag) => \in_array ($tag, ['--'], true);

      for ($c = 1; $c <= count ($microdown); $c++)
      {
        if (isset ($microdown[$c]))
        {
          //~ Nasty `+` sign replacement fix ?
          $microdown[$c][0] = str_replace (
            self::RPL_SET['_'][2][20],
            self::RPL_SET['_'][0][20],
            $microdown[$c][0]
          );

          if (isset ($microdown[$c]['T']))
          {
            $tags = $microdown[$c]['T'];
            $etags = array_reverse ($tags);  // End tags
            $textnode = $microdown[$c][0];
            $attributes = ($microdown[$c]['A'] ?? null);

            if ($attributes !== null) {
              foreach ($attributes as $n => $x)
              {
                $microdown[$c]['A'][$n] = $this-> attributes ($x);
                unset ($x, $n);
              }
              unset ($attributes);
            }

            $html_puzzle = '';
            foreach ($tags as $n => $x)
            {
              $args = ($microdown[$c]['A'][$n] ?? 42);

              if ($tag_special($x)) {
                $inner = "!{$x} ";
                $outter = '';
              }
              else {
                $inner = $x;
                $outter = ((in_array ($inner, self::E_SELFCLOSE, true)
                  && $this-> dtd_check !== '5')
                  ? $zz : $z
                );
              }

              $html_puzzle .= $a.$inner . (
                (!$tag_special($x) && $args !== 42)
                  ? " {$args}" : ''
              ) . $outter;
            }

            $html_puzzle .= "$twin$c$twin";
            $this-> tongue['selection']['sm'][$c] = $textnode;

            foreach ($etags as $n => $x)
            {
              if (!in_array ($x, self::E_SELFCLOSE, true)) {
                $html_puzzle .= (
                  ($tag_special($x))
                  ? " $x$z"
                  : "$a$b$x$z"
                );
              }
              unset ($x, $etags[$n], $n);
            }

            unset ($etags);
            $microdown[$c] = $html_puzzle;
            unset ($html_puzzle);

          }
        }
      }

      $this-> tongue['microdown'] = $microdown;
      unset ($microdown, $this-> find_micro_attributes,
        $this-> micro_attributes_parse
      );

    }

  }

  trait Native {
    /// Funcdown `bag`

    private function quick_filter (string $p): void {
      /// Removes uneccessary white-space over file-array alike arrays

      $this-> $p = explode (EOL, $this-> $p); foreach ($this-> $p as $n=>$line)
      if (\trim ($line) === "") unset ($this-> $p[$n]); $this-> $p = \implode (EOL, $this-> $p);

    }

    private function wrapCdata(): array {
      /// Returns clean CDATA tags
      return (($this-> enforce_cdata) ? [
        0 => substr (self::CDATA[0], 2, -2),
        1 => substr (self::CDATA[1], 2, -2),
      ] : [ '', '' ]);
    }

    private function tab_setter ($tabcount): string {
      /// Tab char multiplier
      $tabs = ((int)$tabcount); return (($tabs > 0)
        ? str_repeat ("\t", $tabs) : '');
    }

    private function format_puzzle(): void {
      /// Generates puzzle matherial out of valid structured list

      if (is_array ($this-> structure_info))
      {
        $this-> puzzle = ''; $a = self::C['lt'];
        $s = self::C['ss']; $b = self::C['gt'];
        $SbB = (($this-> dtd_check === 'X') ? " {$s}{$b} " : "$b " );

        foreach ($this-> structure_info as $int => $x)
        {
          $SpecialCase = ($this-> dtd_check !== 'X' && in_array ($x['name'], self::FORMAT_BYPASS, true));
          $textnode = (($x['text'] === '**') ? '' : $this-> text_extract ($x['text'], $x['name']));
          $eol_tabs = (($this-> source_squeeze) ? '' : (($SpecialCase) ? '' : EOL . $x['tabs']));

          if ($x['name'] === P::C['as']) { // *
            if ($x['type'] === 'DTD' && $x['role'] === 'STRING') {
              $hk = $this-> starcode ('reveal', $x['text']);
              $this-> puzzle .= $a
                . (($hk === '**')
                  ? self::HEADER[$this-> dtd_check]
                  : ((in_array ($hk, [5,'5','X'], true))
                    ? (self::HEADER[$hk] ?? (
                        self::HEADER[$this-> dtd_check] ?? self::HEADER['5']
                      )
                    ) : $hk
                  )
                ) . "$b "
              . EOL;
            }
          }
          else if ($x['name'] === P::C['e']) { // !

            if ($x['type'] === 'COMMENT' && $x['role'] === 'STRING') {
              if (!$this-> source_squeeze)
              $this-> puzzle .= "{$a}{$x['name']}-- $textnode --{$b} $eol_tabs";
            }
          }
          else if ($x['name'] === P::C['t']) { // ~
            if ($x['type'] === 'TEXT')
            $this-> puzzle .= $textnode;
          }
          else {
            $make_more_progress = true;
          }

          if (isset ($make_more_progress) && $make_more_progress)
          { //
            if ($x['type'] === 'TAG')
            {
              $this-> find_micro_attributes = false;
              $this-> micro_attributes_parse = false;
              $attributes = (($x['attr'] === '**') ? null
              : ' '. $this-> attributes ($x['attr']));
              $SpecialEnd = (($SpecialCase) ? '' : " $eol_tabs");

              if ($x['role'] === 'PARENT') $this-> puzzle .= "{$a}{$x['name']}{$attributes}{$b}$SpecialEnd";
              else if ($x['role'] === 'STRING') {
                $empty01 = ['',''];
                $cDATA = (($this-> source_squeeze && $this-> dtd_check !== 'X')
                  ? $empty01 : (($this-> enforce_cdata) ? self::CDATA:$empty01));
                $tnode = ((in_array ($x['name'], array_slice (self::TEXT_BYPASS, 0, -1), true) && !empty ($textnode))
                  ? "{$cDATA[0]}$eol_tabs{$textnode}$eol_tabs{$cDATA[1]}" : $textnode);
                $this-> puzzle .= "$eol_tabs{$a}{$x['name']}{$attributes}{$b}$tnode{$a}{$s}{$x['name']}{$b}$SpecialEnd";
              }
              else if ($x['role'] === 'SELF') $this-> puzzle .= "$eol_tabs{$a}{$x['name']}{$attributes}{$SbB}$eol_tabs";
              else if ($x['role'] === 'END') $this-> puzzle .= "$eol_tabs{$a}{$s}{$x['name']}{$b} $eol_tabs";
            }
          }
        }

        $this-> quick_filter ('puzzle');
      }
    }

    private function markup_puzzle_done(): void {
      /// Markup-puzzle resolver

      $eol = self::EOL_MASK;
      $microhtml = end ($this-> tongue);
      $microtext = ($this-> tongue['selection']['sm'] ?? []);
      $microkeys = array_keys ($microhtml); // For when eol - ing back
      $twins = array_flip (self::L_MARKS);
      $Cdata = $this-> wrapCdata();

      static::$markup = $this-> puzzle;
      foreach (['selection', 'bypass'] as $branch)
      {
        if (\is_array ($this-> tongue[$branch]))
        {
          foreach ($this-> tongue[$branch] as $relative_key => $marks)
          {
            $twin = $twins[$relative_key];
            if ($relative_key === 'mt') // main text
            {
              $make = [];
              if (\is_array ($marks))
              {
                foreach ($marks as $c => $val)
                {
                  $twinmark = $twin . $c . $twin;
                  [$tag, $number] = explode (P::C['c'], $c);
                  $make[$twinmark] = ((in_array ($tag, self::FORMAT_BYPASS, true))
                    ? $marks[$c]
                    : $this-> transform_encode (9, $val)
                  );

                  if ($this-> dtd_check === 'X') {
                    if (mb_strpos ($make[$twinmark], $twins['sm']) === false) {
                      $make[$twinmark] = $Cdata[0]
                        . $this-> transform_encode (2, $make[$twinmark])
                        . $Cdata[1];
                    }
                  }

                  unset ($twinmark, $val, $marks[$c], $c, $number, $tag);
                }
              }

              $this-> markup_replace (make: $make, Normalize: false);
              $make = []; $twin = $twins['sm'];

              if ($microhtml !== [] && $microtext !== [])
              {
                array_walk ($microhtml, function (&$microhtml, $n) {
                  $type = gettype ($microhtml);
                  if (is_array ($microhtml)) {
                    $microhtml[$n] = $microhtml[0];
                  }
                });


                $c = count ($microhtml);
                $m_html = (($c === 1)
                  ? array_shift ($microhtml)
                  : implode ($eol, $microhtml)
                );

                foreach ($microtext as $c => $val)
                {
                  $twinmark = $twin . $c . $twin;
                  $val = $this-> transform_encode (9, $val);
                  if (!str_ends_with ($val, ' ^')) $encodeInt = 1;
                  else {
                    $encodeInt = 2;
                    $val = mb_substr ($val, 0, -2);
                  }

                  $make[$twinmark] = $this-> transform_encode ($encodeInt, $val);
                  if ($this-> dtd_check === 'X') {
                    $make[$twinmark] = $Cdata[0]
                      . $make[$twinmark]
                      . $Cdata[1];
                  }
                  unset ($twinmark, $encodeInt, $val, $c);
                }

                if (is_array ($make) && $make !== [])
                {
                  $m_html = (@array_combine ($microkeys,
                    array_values (explode ($eol, str_replace (
                      array_keys ($make), array_values ($make),
                      $m_html
                    )))
                  ));

                  $make = [];
                  if (is_array ($m_html))
                  foreach ($m_html as $c => $html)
                  {
                    $twinmark = $twin . $c . $twin;
                    $make[$twinmark] = $html;
                    unset ($twinmark, $html, $m_html[$c], $c);
                  }

                  unset ($m_html);
                  $this-> markup_replace ($make);
                }

              }
            }
            else
            {
              $make = [];
              foreach ($marks as $c => $val)
              {
                $twinmark = $twin.$c.$twin;
                if ($relative_key !== 'tb') { // NOT `t`ranslation `b`ypass

                  if ($relative_key === 'ab') {
                    if (str_ends_with ($val, ' ' . P::C['p'])) {
                      $val = mb_substr ($val, 0, -2);
                      $this-> isolated_decoded_fixed ($val, $marks[$c]);
                    }
                  }
                  else {
                    $marks[$c] = $this-> transform_encode (1, $val);
                  }
                }

                $make[$twinmark] = $marks[$c];
                unset ($twinmark, $val, $marks[$c], $c);
              }

              $this-> markup_replace ($make);
            }
          }

        }
        else
          break; // <- very important. Omit and uR dead.
      }


      unset ($microtext, $microhtml);
    }

    private function isolated_decoded_fixed ($val, &$fix): void {
      /// Fix rawurldecoded string for proper _GET

      $fix = str_replace ([
        "%3F", "%3D", "%26", "%23", '%3A', "%2F"
      ], [
        P::C['qm'], P::C['eq'], "&#38;", '#', ':', P::C['ss']
      ], rawurlencode ($val));
    }

    private function markup_replace (array $make, bool $Normalize = true): void {
      /// Marks vs their real values from $this-> tongue `inputunique` property

      if ($Normalize) foreach ($make as $key => $value) {
        if (str_ends_with ($value, ' ^'))
          $make[$key] = $this-> transform_encode (2, mb_substr ($value, 0, -2));
        unset ($value, $key);
      }

      static::$markup = str_replace (array_keys ($make),
        array_values ($make), static::$markup);
    }

    private function text_extract (string $starcoded, string $tag_name): string {
      /// Extracts all found text for the tongue property

      $unsafe = $this-> starcode ('reveal', $starcoded);
      $i = ( (in_array ($tag_name, array_keys (array_flip (
        array_merge (self::TEXT_BYPASS, self::FORMAT_BYPASS,
        [ self::C['as'], self::C['e'] ])
      )), true)) ? 1 : 0 );

      return
        $this-> tongue_make_text_twin ((($i === 1)
          ? 'bypass' : 'selection'),
          array_values (self::L_MARKS)[$i],
          array_keys (self::L_MARKS)[$i],
          $unsafe, $tag_name
      );
    }

    private function filter_whitelisted_attributes (string $attribute_name): array {
      /// Search for language dependant attributes and separate them.

      $lmkeys = array_keys (self::L_MARKS);
      $lmvals = array_values (self::L_MARKS);
      if (in_array ($attribute_name, self::ATP, true))
      {
        $primary = 'selection';
        [$key, $twin] = match ($attribute_name) {
          self::ATP[0] => [$lmvals[4], $lmkeys[4]],
          self::ATP[1] => [$lmvals[5], $lmkeys[5]],
          self::ATP[2] => [$lmvals[6], $lmkeys[6]],
          self::ATP[3] => [$lmvals[7], $lmkeys[7]],
          default => ['','']
        };
      }
      else {
        $primary = 'bypass';
        $key = $lmvals[2];
        $twin = $lmkeys[2];
      }

      return [ $primary, $key, $twin ];

    }

    private function short_attribute_name (string $suspect):string {
      /// From single char to attribute name
      if (mb_strlen ($suspect) === 1)
      {
        $suspect = (self::A_EXPAND[$suspect] ?? ((!ctype_alpha ($suspect)) ? 'data-attribute-error'
          : $suspect) // <- SVG and elements alike, do have non-global single-char for attributes
        );
      }

      return $suspect;
    }

    private function tongue_make_text_twin ($primary, $key, $twin, $unsafe, $xname):string {
      /// Injects NON-SOURCECODE text into tongue property

      // Bring back string back to normal.
      $unsafe = $this-> transform_encode (9, $unsafe);

      // Find `%` prefix modifier
      $alignTabs = ($xname !== 42 && (
        str_starts_with ($unsafe, P::C['p'] . ' ')
        || str_starts_with ($unsafe, P::C['p'] . EOL)));

      // Find `!` suffix modifier
      $forceEncode = str_ends_with ($unsafe, ' ' . P::C['e']);

      // If `!` suffix modifier is found ..
      (!$forceEncode) or // .. remove it immediately.
        e ($unsafe, mb_substr ($unsafe, 0, -2));

      // If `%` prefix modifier is found ..
      ($alignTabs) and // .. adjust tabulators immediately.
        $this-> tabulators_evaporate ($unsafe, $this-> ident_eol);

      ($forceEncode || in_array ($xname, self::FORMAT_BYPASS, true))
        and // if suffix was ! - make string safe for presentation, xml decimal encoded
          e ($unsafe, $this-> transform_encode (1, $unsafe));

      (!isset ($this-> tongue[$primary][$key]))
        and // Create new key for the tongue
          e ($this-> tongue[$primary][$key], []);

      $ck = "{$xname}:" . (count ($this-> tongue[$primary][$key]) + 1);
      $this-> tongue[$primary][$key][$ck] = $unsafe; // Make it unique
      unset ($unsafe, $forceEncode); return $twin . $ck . $twin;
    }

    private function tabulators_evaporate (string &$unsafe, int $Tab): void {
      /// Get rid of the extra space on the left in preformatted text

      $counter = [];
      $Bomb = explode (EOL, mb_substr ($unsafe, 2));
      foreach ($Bomb as $n => $line) {
        $test = trim ($line);
        $int = strspn ($line, ' ');
        if ($test === '') $counter[$n] = 'x';
        else if ($int > $Tab) $counter[$n] = $int;
        unset ($line, $n);
      }

      if ($counter !== []) {
        $minCount = min ($counter);
        if (is_numeric ($minCount)) {
          $takeaway = ($minCount - $Tab);
          foreach ($counter as $n => $line) {
            if ($line !== 'x') $Bomb[$n] = mb_substr ($Bomb[$n], $takeaway);
            unset ($line, $n);
          }
        }
      }
      unset ($Tab, $counter);
      $unsafe = implode (EOL, $Bomb);

    }
  }

  trait PairCode {
    /// Transportation based 'bag'

    private static function inner_preg_closure (string $value, string $type):string {
      /// This is callback type role method

      $sdb = [ 'single', 'double', 'backtick' ];
      switch ($type)
      {
        case $sdb[0]: $value = P::C['ap'] . $value . P::C['ap']; break;
        case $sdb[1]: $value = P::C['qt'] . $value . P::C['qt']; break;
        case $sdb[2]: $value = P::C['bt'] . $value . P::C['bt']; break;
        case 'curly': case 'curlySafe': $carry = [ P::C['cl'], P::C['cr'] ]; break;
        case 'bracket': $carry = [ P::C['sl'], P::C['sr'] ]; break;
        case 'pthzs': $carry = [ P::C['bl'], P::C['br'] ]; break;
        case 'ltgt': $carry = [ P::C['lt'], P::C['gt'] ]; break;
        default: break;
      }

      $carrier = isset ($carry); $As = P::C['as'];
      $isSafeAlready = ($carrier && mb_strlen ($value) > 2
      && str_starts_with ($value, $As) && str_ends_with ($value, $As));
      if (!$isSafeAlready) $value = $As . base64_encode ($value) . $As;
      return (($carrier && !in_array ($type, $sdb, true)) ? implode ($value, $carry) : $value);
    }

    private function action_mask (string $job, $suspect): string|array {
      /// Performs control character masking/escaping over 'broken' strings

      $r = self::RPL_SET['_']; $o = $r[0]; $r = $r[2];
      $a = match ($job) {
        'curly' => [ $o[14] => $r[14], $o[16] => $r[16], $o[20] => $r[20] ],
        'curlySafe' => [ $o[14] => $r[14], $o[16] => $r[16], $o[1] => $r[1], $o[2] => $r[2], $o[7] => $r[7] ],
        'strings' => [ $o[3] => $r[3], $o[4] => $r[4], $o[13] => $r[13] ],
        'bracket' => [ $o[11] => $r[11], $o[12] => $r[12] ],
        'pthzs' => [ $o[5] => $r[5], $o[6] => $r[6] ],
        'ltgt' => [ $o[1] => $r[1], $o[2] => $r[2] ],
        default => null
      }; unset ($r, $o);
      return ((!is_array ($a) || !is_string ($suspect)) ? $suspect
      : self::syschars_escaped (mb_str_split ($suspect), $a));
    }

    private static function syschars_escaped (array $broken, array $cset, string $esc = P::C['esc']):string {
      /*: ~ Str_split_multibyte escape key iterator-masquerade

        $broken = array splitted by single UTF-8 char.
        $cset example: [',' => null, '=' => null ]
        $esc = "\"

      */

      $aCsetKeys = array_keys ($cset);
      foreach ($broken as $n => $char) {
        $previous = ($n - 1);
        if ($char !== $esc && isset ($broken[$previous])
        && in_array ($char, $aCsetKeys, true)) {
          if ($broken[$previous] === $esc) {
            $broken[$n] = $cset[$char];
            $broken[$previous] = '';
          }
        }
        unset ($char, $n, $prev);
      }

      return implode ($broken);
    }

    private function starcode (string $action, $suspect, $job = 'strings'): string {
      /// Encodes or decodes string and wraps it with the star * character

      $suspect = $this-> action_mask ($job, $suspect);

      if ($action === 'mask')
      { // Preg find and base64_encode selection

        if ($job === 'strings')
        {
          return preg_replace_callback_array ([
            '~\'(.*?)\'~imsu' => fn($sq) => self::inner_preg_closure ($sq[1], 'single'),
            '~\"(.*?)\"~imsu' => fn($dq) => self::inner_preg_closure ($dq[1], 'double'),
            '~\`(.*?)\`~imsu' => fn($bt) => self::inner_preg_closure ($bt[1], 'backtick')
          ], $suspect);
        }
        else
        {
          $c = match ($job)
          {
            default => [],
            'ltgt' => [ self::RPL_SET['_'][0][1], self::RPL_SET['_'][0][2] ],
            'pthzs' => [ self::RPL_SET['_'][0][5], self::RPL_SET['_'][0][6] ],
            'bracket' => [ self::RPL_SET['_'][0][11], self::RPL_SET['_'][0][12] ],
            'curly' => [ self::RPL_SET['_'][0][14], self::RPL_SET['_'][0][16] ],
            'curlySafe' => [
              implode ([
                P::C['esc'] . self::RPL_SET['_'][0][14]
                , P::C['esc'] . self::RPL_SET['_'][0][7]
                , str_repeat (P::C['esc'] . self::RPL_SET['_'][0][2], 3)
              ]) . P::C['esc'] . 's'
              ,
              implode ([
                P::C['esc'] . 's' . str_repeat (P::C['esc'] . self::RPL_SET['_'][0][1], 3)
                , P::C['esc'] . self::RPL_SET['_'][0][7]
                , P::C['esc'] . self::RPL_SET['_'][0][16]
              ])
            ],
          };

          return (($c === []) ? $suspect
            : preg_replace_callback (((($job === 'curlySafe')
              ? "~{$c[0]}(.*?){$c[1]}(\s|\n|\r)" : "~\\{$c[0]}(.*?)\\{$c[1]}") . '~imsu'),
              fn($a) => self::inner_preg_closure ($a[1], $job), $suspect)
          );

        }

      }
      else if ($action === 'reveal')
      { // Bring back encoded string to normal

        $As = P::C['as'];
        $victim = trim ($suspect);
        $squeeze = $this-> one_line ($suspect, true);
        $starpos = mb_strpos ($victim, $As);

        if (!isset ($retVal))
        {
          if (mb_strlen ($victim) === mb_strlen ($squeeze)
          && in_array ($victim, ['true','false','null'], true)) {
            $retVal = $victim;
          }
          elseif ($starpos !== false)
          {

            $substring = mb_substr ($victim,1,-1);
            $e = explode ("$As.$As", $substring);

            if (!isset ($e[1])) {
              $retVal = @base64_decode ($substring);
            }
            else
            {
              foreach ($e as $n => $enc) $e[$n] = base64_decode ($enc);
              $retVal = implode ('.', $e);
            }

          }
        }

        if (isset ($retVal)) {
          return $this-> transform_encode (9, $retVal);
        }

      }
    }

  }

  trait Encapsulate {
    /// Backend workers for the public methods

    private function funcdown_atom_request (string|int $doctype): void {
      /// Ini Configuration parser + init setter
      define (strtoupper (__FUNCTION__), date (DATE_ATOM))
      and $this-> dtd_check ($doctype)-> {'toggle_defaults' . _}();
    }

    private function toggle_defaults____(): void {
      /// (Re)Sets runtime member values back to initial runtime values

      foreach (self::read_conf ('toggle') as $name => $bool) {
        $this-> $name = ((bool) $bool);
        unset ($bool, $name);
      }

      e ($stall, self::read_conf ('stall')) and e ($this-> ident_eol, $stall['ident_eol'])
      and e ($this-> micro_blacklist, trim ($stall['micro_blacklist'])) and $this-> microBlack();
      e ($this-> aid_path, $stall['aid_path']) and e ($this-> tongue_path, trim ($stall['tongue_path']));

      e ($stall, trim ($stall['temp_path']))
      and e ($this-> temp_path, ((!is_dir ($stall)) ? sys_get_temp_dir() : $stall));
      is_dir ($this-> tongue_path) or e ($this-> tongue_path, $this-> temp_path);
      unset ($stall);
    }

    private function toggle____ (string $property, $toggle): void {
      /// SingleshotToggler for the boolean properties

      if (property_exists ($this, $property)
      && in_array ($property, self::SWITCHES, true)) {
        $check = $this-> $property;
        if (is_bool ($check)) {
          $this-> $property = (($toggle === 42)
            ? (($check !== false) ? false : true)
            : ((bool) $toggle)
          );
        }
      }
    }

    private function set____ (string $member, mixed $data): void {
      /// Usual but not casual, logical setter.
      if (property_exists ($this, $member)
        && !in_array ($member, self::SWITCHES)
      ) $this-> $member = $data;
      if ($member === 'micro_blacklist')
        $this-> microBlack();
    }

    private function parse____ (string $apifunc, string|array $input): void {
      /// Argument-based crossroad.

      //~ $this-> selective_defaults(); ##~ Is selective_defaults necessary here ?
      $keyplayer = ((!is_string ($input)) ? ((!is_array ($input)) ? 'input_typeof'
      : (($input === []) ? 'empty_a' : 'array' )) : 'string' );

      if (!in_array ($keyplayer, [ 'array', 'string' ], true)) {
        $this-> e_code = $keyplayer; ob_start(); var_dump ($input); $input = $this-> one_line (ob_get_clean());
        $this-> martyr = 'FOOD-PREFERENCE ERROR ' . P::U['ea'] . " unsupported parse argument -> $input";
        $this-> strict_rules(); if (!$this-> silent_errors) return;
      }
      else {
        $method_name = implode ('_', [$apifunc, 'fncd', $keyplayer]);
        $this-> $method_name ($input);
      }
    }

    private function parse_fncd_array (array $array): void {
      /// Funcdown array (PHP-native) govern/parse action.
      $this-> parse_fncd_string ($this-> atos ($array));
    }

    private function parse_fncd_string (string $string): void {
      /// Funcdown string govern/parse action.
      /// TODO

      $this-> string_code ($string);

      !$this-> e_code or $this-> e_code_not_false ($string);

      if ($this-> martyr !== null) {
        $starcoded = $this-> starcode (
        'mask', $this-> starcode ('mask',
            $this-> starcode ('mask',
            $this-> martyr,
            'curlySafe'),
          'curly'),
        'pthzs');
      }

      if (isset ($starcoded))
      {
        $this-> alter_linter_counter ($starcoded);
        $this-> linter_signal_spark();

        if (!is_string ($this-> e_code) && !is_array ($this-> valid_structure)) {

          $starcoded = $this-> semicolon_starcoded ($starcoded); // Add semicolon to each reckognition
          $starcoded = $this-> input_conclusion ($starcoded); // Clean up.
          $this-> structure_digest ($starcoded); // $starcoded should be NULL from this point
        }
        else {
          !$this-> e_code
            or $this-> e_code_not_false ($string); // Check for errors again
        }
      }

      if ($starcoded === null) {

        $this-> blueprint_populate(); // Create matherial for the Blueprint Generation
        !$this-> e_code or $this-> e_code_not_false ($string); // Check for errors again

        FinallySafeToGenerateContent: {

          /* <-- prepend/remove one /

            var_export ($this);
            print EOL; print_r ($this-> tongue);

          //*/

          $this-> format_puzzle();
          $this-> microdown_handler();
          $this-> markup_puzzle_done();

          /* <-- prepend/remove one /

            var_export ($this-> puzzle);
            print EOL; print_r ($this-> tongue);
            exit;

          //*/
        }

      }

      !$this-> null_expose
        or $this-> selective_defaults();
    }

    private function atos (array $a): string {
      /// Recursive `A`rray `TO` `S`tring iterator

      $AtoS = '';
      foreach ($a as $Kholder => $Guess)
      {
        $getKtype = gettype ($Kholder);
        if ($getKtype === 'string')
        {
          $Bomb = explode (P::C['c'], $Kholder);
          if (count ($Bomb) > 1) {
            $Prefix = array_shift ($Bomb);
          }

          $Bomb = explode (P::C['a'],
            implode (P::C['c'], $Bomb)
          );

          $cb = count ($Bomb);

          if ($cb > 1) {
            $Tagnode = array_shift ($Bomb);
            $attributes = ' ' . P::C['bl']
              . $this-> lintercept____ (implode (P::C['a'], $Bomb), 'A')
              . P::C['br'] . ' ';
          }
          else if ($cb === 1) {
            if ($Bomb[0] === P::C['bt']) {
              if (!property_exists ($this, 'filepath_fragment')) {
                $this-> filepath_fragment = $Guess;
              }
              $Tagnode = '';
              $attributes = '';
            }
            else {
              $Tagnode = $Bomb[0];
              $attributes = '';
            }
          }
          unset ($Bomb);

        }
        else
        {
          $Tagnode = P::C['e'];
          $attributes = '';
        }

        unset ($getKtype);

        $AtoS .= $Tagnode . $attributes;

        if (is_array ($Guess))
        {
          $AtoS .= P::C['sl'] . ' ' . (
            ($Guess !== []) ? $this-> atos ($Guess) // <- recursion
            : P::C['e'] . P::C['cl'] . "$Kholder" . P::C['cr'] . ' '
          ) . P::C['sr'] . ' ';
        }
        else
        {
          $MixV = match (gettype ($Guess)) {
            'NULL', 'boolean' => 'BAR',
            'string', 'integer', 'double' => 'COMPLICATED',
            default => null
          };

          if ($MixV === 'BAR') $AtoS .= P::C['b'] . ' ';
          else if ($MixV === 'COMPLICATED') {

            if (isset ($this-> filepath_fragment) && file_exists ($this-> filepath_fragment)) {
              $AtoS .= file_get_contents ($this-> filepath_fragment);
              $this-> filepath_fragment = null; unset ($this-> filepath_fragment);
            }
            else {

              if (in_array ($Tagnode, ['J', 'script'], true) && empty ($Guess)) {
                $AtoS .= P::C['cl'] . P::C['cr'] . ' ';
              }
              else
              {
                $AtoS .= ((empty ($Guess))
                  ? P::C['cl'] . P::C['cr'] . ' '
                  : " {$this-> inward_outward_sf ('inward_safe')} "
                    . str_replace ($this-> inward_outward_sf ('outward_safe')
                    , $this-> inward_outward_sf ('outward_fake'), "{$Guess}"
                  ) . " {$this-> inward_outward_sf ('outward_safe')} "
                );
              }
            }

          }
          else {
            $Atos .= '';
          }

          unset ($MixV);
        }

        unset ($Bomb, $Guess, $Kholder);
      }

      return $AtoS;
    }

    private function expose____ (int $index): mixed {
      /// Expose major content values by numerical index

      $property = match ($index) {
        default => null,
        1 => 'dtd_check',
        2 => 'martyr',
        3 => 'valid_structure',
        4 => 'structure_info',
        5 => 'puzzle',
        6 => 'tongue',
        7 => 'inputunique',
        8 => 'e_code'
      };

      return is_null ($property)
      ? false: $this-> $property;
    }

    private function lintercept____ (string $content, string $chars, bool $addcslashes = true): string {
      /// Character interceptor

      $decision = ((mb_strlen ($chars) === 1)
        ? [ $chars ] : mb_str_split ($chars)
      );

      foreach ($decision as $c)
      {
        switch ($c) // $c as char
        {
          default:
          case 'T': // 'T'ext, main

            $c = [[
                P::C['cl'] , P::C['cr']
              ],
              [P::C['esc'] . P::C['cl']
              , P::C['esc'] . P::C['cr']]
            ];

          break;
          case 'A': // 'A'ttributes in general

            $c = [[ P::C['cl'], P::C['br']],
              [P::C['esc'] . P::C['cl'],
               P::C['esc'] . P::C['br']
            ]];

            !$this-> quotes_only and (array_push ($c[0], P::C['bt'])
              and array_push ($c[1], (P::C['esc'] .P::C['bt'])));


          break;
          case 'M': // 'M'icrodown

            $c = [
              [ P::C['b'], P::C['ps'] ],
              [ P::C['esc'] . P::C['b'],
                P::C['esc'] . P::C['esc'] . P::C['ps']
              ]
            ];

          break;
          case 'B': // 'B'acktick only

            $c = [
              [P::C['bt']],
              [P::C['esc'] . P::C['bt']]
            ];

          break;
          case 'C': // 'C'omma only

            $c = [
              [P::C['cm']],
              [P::C['esc'] . P::C['cm']]
            ];
            $addcslashes = false;
          break;
        }

        $content = (!$addcslashes)
          ? str_replace ($c[0], $c[1], $content)
          : addcslashes ($content, implode ($c[0]));
        unset($c);
      }

      return $content;
    }

    private function release____ (string|int $member = 0): mixed {
      /// Returns parsed markup, raw backend funcdown state
      /// Or forces visitor to download generated markup file

      if ($member !== 0)
      {
        if (is_int ($member)) return $this-> expose____ ($member); // Expose property
        elseif ($member === 'download') $this-> giveaway____ (false); // Download file
        else
        {
          $this-> e_code = 'release_e';
          $this-> martyr = 'FOOD-PREFERENCE ERROR '
          . P::U['ea'] . " unsupported parse argument -> $member";
          $this-> strict_rules();
          if (!$this-> silent_errors) exit;
        }
      }
      else
      {
        $this-> selective_defaults();
        // Is or it isn't - Scalar and not string
        ob_start();
          echo ((!is_string (static::$markup))
            ? ((is_object (static::$markup)
                || is_array (static::$markup)
                || !is_scalar (static::$markup)
              ) ? print_r (static::$markup, true)
              : var_export (static::$markup, true)
            ) : static::$markup // <- If string
          ); static::$markup = null;

        return $this-> normalize_escaped_backend (
          ob_get_clean()
        );
      }
    }

    private function giveaway____ ($basename): never {
      /// Force download of generated markup file.

      $ext = (($this-> dtd_check === 'X') ? 'xml' : 'html');
      $content = $this-> release____ (0);
      $bname = (($basename !== false && is_string ($basename))
      ? $basename : 'fncd_giveaway_' . sha1 ($content));
      $filename = $this-> joinDsp ($this-> temp_path, "$bname.$ext");
      if (file_put_contents ($filename, $content)) {
        unset ($ext, $content); foreach ([
          'Content-Description' => 'File Transfer',
          'Content-Type' => 'application/octet-stream',
          'Content-Disposition' => 'attachment; filename="' . basename ($filename) . '"',
          'Expires' => '0', 'Cache-Control' => 'must-revalidate', 'Pragma' => 'public',
          'Content-Length' => filesize ($filename),
        ] as $HeaderName => $HeaderValue) {
          header ("{$HeaderName}: {$HeaderValue}");
          unset ($HeaderValue, $HeaderName);
        }

        readfile ($filename);
        unlink ($filename);
      }
      unset ($filename);
      exit;
    }

    private function mtof____ (
      string $source
      , bool $skeptic
      , bool $normalize
      , bool $dtd
      , int $lang
    ): string {
      /// Converts from Markup Language to funcdown code

      ($lang < 0 || $lang > 1) and die ( __FUNCTION__
        . ' says: ERROR! `$lang` can be 0 or 1.'
      );

      $Doc = new \DOMDocument;
      $source = trim ($source); \libxml_use_internal_errors (true);
      $Doc-> {'load' . ((is_file ($source)) ? 'HTMLfile' : 'HTML')} ($source);
      $this-> MarkupToFuncdownRecursive ($Doc, $skeptic, $Iterated);
      \libxml_clear_errors();

      if ($lang === 0) {
        $SetX5 = 'X';
        if (str_starts_with ($Iterated, 'html[body['))
          $Iterated = mb_substr ($Iterated, 10, -2);
      }
      else {
        $SetX5 = 5;
      }

      if ($normalize) {
        $Iterated = str_replace (['[~{', '}]'
        ], [ P::C['cl'], P::C['cr'] ], $Iterated);
      }
      return (($dtd) ? "*{{$SetX5}}" : '') . $Iterated;

    }

    private function MarkupToFuncdownRecursive (\DOMNode $domNode, bool $skeptic, ?string &$Concat): void {
      /// Recursive converter from VALID HTML or XML file or string to Funcdown String.

      foreach ($domNode-> childNodes as $node) {

        $TestValue = trim ("{$node-> nodeValue}");
        // if TestValue is empty there is no direct text child.
        $ParentNode = $node-> parentNode-> localName ?? 'none';

        if (in_array ($node-> nodeType, [1, 3, 8], true)) {

          if ($node-> nodeType === 1)
            $Concat .= $node-> nodeName;

          if ($node-> hasAttributes()) {
            $Concat .= ' ('; $Impl = [];
            foreach ($node-> attributes as $attr) {
              array_push ($Impl, $attr-> nodeName . ' '
              . $this-> lintercept____ ($attr-> nodeValue, 'AC')
              . (($skeptic && !in_array ($attr-> nodeName, self::ATP))
              ? ' !':'')); unset ($attr);
            }

            $Concat .= implode (',', $Impl) . ')';
            unset ($Impl);
          }

          if (in_array ($node-> nodeName, self::E_SELFCLOSE, true))
            $Concat .= '| ';

          if ($ParentNode !== 'none') {

            if (in_array ($node-> nodeName, array_merge (
              [ '#text', '#comment' ], self::TEXT_BYPASS), true)
            ) {

              if ($TestValue !== '' ) {
                $M = match ($node-> nodeName) {
                  'textarea', 'script', 'style' => '', '#comment' => '!', default => '~'
                }; $Concat .= "{$M}{{$this-> lintercept____ ($TestValue, 'T')}}";
              }
              else
              if (in_array ($node-> nodeName, self::TEXT_BYPASS, true)) {
                $Concat .= '{}';
              }
            }
          }

          if ($node-> hasChildNodes() && !in_array ($node-> nodeName, self::TEXT_BYPASS, true)) {
            $Concat .= '['; $this-> MarkupToFuncdownRecursive ($node, $skeptic, $Concat); $Concat .= ']';
          }
        }

        unset ($ParentNode, $TestValue, $node);
      }
    }

    private function inward_outward_sf (string $safewrapper):string {
      /// Helper method for Array To String ($this-> atos) safe wrappers

      return match ($safewrapper) {
        'outward_fake' => str_repeat (P::C['lt'], 3). P::C['esc']. P::C['c']. P::C['cr']
        , 'outward_safe' => str_repeat (P::C['lt'], 3). P::C['c']. P::C['cr']
        , 'inward_safe' => P::C['cl']. P::C['c'] . str_repeat (P::C['gt'], 3)
        , default => ''
      };
    }

    private static function TPnanny(): void {
      /// Remove dead temp files upon destruct.
      if (is_array (static::$temp_path_nanny) && static::$temp_path_nanny[1] !== []) {
        foreach (static::$temp_path_nanny[1] as $file) {
          $suspect = (static::$temp_path_nanny[0] . DSP . $file);
          if (is_file ($suspect) || ((@stat ($suspect)) !== false)) {
            ((time() - (@filemtime ($suspect))) >= self::HOUR)
              and self::opcache_invalidate ($suspect, true);
          } unset ($file, $suspect);
        }
      }

      static::$temp_path_nanny = null;
    }

    private function microBlack() {
      /// microBlacklist setter
      $this-> {__FUNCTION__} = ((!empty ($this-> micro_blacklist)
        && ctype_alpha ($this-> micro_blacklist))
        ? array_flip (mb_str_split ($this-> micro_blacklist))
        : []
      );
    }

    private function FlexplateFLexDot (string $martyr): void {
      /*: The following is for Flexplate CMS that utilizes funcdown and has nothing to do with funcdown
          functionallity it self. Hence, should not be placed out of this block scope, no matter what.
      */ (defined ('Flexplate') && property_exists (Flexplate, 'Flexdot') && is_object (Flexplate-> Flexdot))
      and Flexplate-> Flexdot-> HandleFakeFiles ($this-> $martyr, Flexplate-> Flexdot-> inputGfu-> SetPass);
    }

  }

  trait PublicApi {
    ///

    public function set (string $member, mixed $data): self {
      /// If member exists, change its value and return self
      $this-> {__FUNCTION__ . _} ($member, $data);
      return $this;
    }

    public function parse (array|string $input, bool $retVal = true): ?self {
      /// Chews content, be it raw array or funcdown string or filepath.
      /// Optionally returns populated object with usable property values
      $this-> {__FUNCTION__ . _} (__FUNCTION__, $input);
      return (($retVal) ? $this : null);
    }

    public function toString (array $a): string {
      /// Public array_to_string mode.
      return $this-> atos ($a, false);
    }

    public function toJsonString (array $a): false|string {
      /// Public funcdown_array_to_JSON_string mode.
      return json_encode
      ($a, JSON_PRETTY_PRINT
        |JSON_UNESCAPED_UNICODE
        |JSON_UNESCAPED_SLASHES
        |JSON_FORCE_OBJECT
      );
    }

    public function jsonToArray (string $jsonObj): null|array {
      /// Public JSON_to_funcdown_array mode.
      return json_decode ($jsonObj, null, 2147483647, JSON_OBJECT_AS_ARRAY);
    }

    public function mtof (
      string $source
      , bool $skeptic = true
      , bool $normalize = false
      , bool $dtd = false
      , int $lang = 1
    ): string|array {
      /// $source is markup string or file path to markup code
      /// $skeptic true will encode special characters to xml decimal entities
      /// $dtd true will prepened document type definition tag, based on $lang
      /// $normalize true will try to make html or xml white-space-free as much as possible
      /// $lang 0 is xml, lang 1 is html5
      return $this-> {__FUNCTION__ . _} ($source, $skeptic, $normalize, $dtd, $lang);
    }

    public function lintercept (string $suspect, string $chars = 'T', bool $addslashes = true): string {
      /// Escapes desired characters by a single char denotation.
      return $this-> {__FUNCTION__ ._} ($suspect, $chars, $addslashes);
    }

    public function expose (mixed $property = null): mixed {
      /// Display private backend API property
      return $this-> {__FUNCTION__ ._} ($property);
    }

    public function release (string|int $member = 0): mixed {
      /// Returns either final parsed markup
      /// Or calls the expose method, Depending on a $member value
      return $this-> {__FUNCTION__ ._} ($member);
    }

    public function giveaway (string|false $basename = false): never {
      /// Force content download
      $this-> {__FUNCTION__ ._} ($basename);
    }

    public function toggle (string $prop, int|bool $toggle = 42): self {
      /// Setter method. Unless $toggle is boolean true or false ..
      /// if true - it will be false and vice-versa - on demand.
      $this-> { __FUNCTION__ . _ } ($prop, $toggle);
      return $this;
    }

    public function dtd_check (string|int $doctype): self {
      /// Set document type definition.
      /// This determines how self-closing tags look like.
      $this-> { __FUNCTION__ } = "$doctype";
      return $this;
    }

    public function toggle_defaults(): self {
      /// Set default properties
      $this-> {__FUNCTION__ . _}();
      return $this;
    }

    public function objectPropertyFor (mixed &$generated, bool $assign = false): self {
      /// Acts as an transfer holder.

      $name = __FUNCTION__; if ($assign) {
        $this-> $name = $generated;
      }
      else {
        if (!is_null ($this-> $name)) {
          $generated = $this-> $name;
          $this-> $name = null;
        }
      }
      return $this;
    }

    public function eCodeCheck(): string {
      /// Tells if some sytax error happened.
      return (($this-> e_code === false) ? 'ok'
        : self::PROPERTY['e_mssg'][$this-> e_code]
      );
    }

  }

  trait PrConst {
    /// Stone-steady/ready words, characters and lists

    private const VERSION = '1.0b'; /*:

        Translates to "1.0 beta" version.

        The next version might be 1.1b or! "1.0" .. who knows ?
        If 1.0 (official - stable releaase), milestone reached!
    */


    /* ##-> Editable/expandable-ready constants START Below <-## */


    private const ATP =  /// Attributes `A`lt, `T`itle, `P`laceholder as whitelisted language-related value holders.
    [ 'alt', 'title', 'placeholder', 'data-before' ];

    private const TEXT_BYPASS =
      /// Tags that should not get into consideration for multilanguage features.
      /// This is, in other words, blacklist for the language-related purposes.
    [ 'style', 'script', 'textarea' ];

    private const FORMAT_BYPASS = /// Tags that should have persistent line-breaks with original formatting.
      /// That is, these will not disobey pretty html only in XML mode, will be drawn as any other element.
    [ 'pre', 'code', 'textarea' ];

    private const N_EXPAND = /// Tag name expansion.
    [ // You may Expand this array to fit Your needs

      'R' => 'html', // R as ROOT node
      'H' => 'head',
      'M' => 'meta',
      'T' => 'title',
      'L' => 'link',
      'J' => 'script',
      'B' => 'body',
      'D' => 'div',
      'N' => 'nav',
      'P' => 'pre',
      'C' => 'code',
      'F' => 'form',
      'I' => 'input',
      'A' => 'article',
      'S' => 'section',
      'AS' => 'aside',
      'HD' => 'header',
      'FT' => 'footer',
      'BT' => 'button',
      'LL' => 'label',
      'TT' => 'textarea',

    ];

    private const A_EXPAND = /// Attribute name expansion
    [ // If You Expand this, make sure that index is ONE character only

      '#' => 'id',
      '%' => 'href',
      '.' => 'class',
      '!' => 'title',
      '@' => 'content',
      'A' => 'alt',
      'S' => 'src',
      'F' => 'form',
      'T' => 'type',
      'N' => 'name',
      'V' => 'value',
      'C' => 'style',
      'H' => 'hidden',
      'R' => 'readonly',
      'E' => 'contenteditable',

    ];

    private const E_SELFCLOSE = /// List of known self-closing HTML tags
    [
      'br',
      'hr',
      'col',
      'wbr',
      'img',
      'area',
      'base',
      'input',
      'embed',
      'link',
      'meta',
      'param',
      'source',
      'track',
      'circle',
    ];

    private const MICRODOWN = /// For the microdown meta-syntax
    [

      0 => // Tag name expansion
      [

        'A' => 'abbr',
        'B' => 'bdi',
        'C' => 'code',
        'D' => 'data',
        'K' => 'kbd',
        'L' => 'label',
        'M' => 'mark',
        'N' => 'dfn',
        'O' => 'bdo',
        'S' => 'span',
        'T' => 'time',
        'V' => 'var',
        'I' => 'input',
        'J' => 'script',
        '_' => 'hr',
        'W' => 'wbr',
        P::C['hm'] => 'br', // '-' =>  'br'
        P::C['e'] => P::C['hm'].P::C['hm'] //  '!' => '<!--  -->'

      ],
      'SCT' => // Single Char Tags.
      [

        0 => 'a',
        1 => 'b',
        2 => 'i',
        3 => 'p',
        4 => 's',
        5 => 'u',
        6 => 'q'

      ]

    ];


    /*\ ##-> END OF Editable/expandable-ready constants <-## \*/
    /*\ ##-> Non-Editable (DO NOT EDIT) constants below <-## \*/

    private const PROPERTY = /// ALL private, non-static object properties.
    [
      'e_mssg' => // Short Error Code messages
      [
        'input_typeof' => 'Funcdown can operate ONLY over well formed `funcdown-complement` strings and arrays.',
        'extension' => 'Name for the external funcdown file, must have either ".fncd" or ".fncd.phtml" ending.',
        'no_funcdown' => 'Please provide correct funcdown syntax or correct path to it. Thank You for Your understanding. :) ',
        'fncd_invalid' => 'Inacceptible funcdown code found. No further from here, unless all errors - fixed.',
        'equals_not' => 'Syntax error. Each bracket, be it left "(", "{", "[" (or right), must have its own (unescaped) opposite.',
        'release_e' => 'Method call error. Release method accepts different arguments.',

        'empty_a' => 'Using funcdown power over an EMPTY array is a waste of everything. '. EOL .
          '  Please, provide funcdown-complement array. ',

        'php_tags' => 'Properly open/close all funcdown substitutions for all PHP open/close tags.'. EOL .
          '  Avoid original PHP/xml ones in order to prevent further emit of backend errors.',

      ],
      'linter_count' => // You don't want to touch this ..
      [
        'bar' => 0,
        'parenthesis' => null,
        'curly' => null,
        'square' => null,
      ],
      'tab_depth' => 0,
      'dtd_check' => '5',
      'inputunique' => null,
      'e_code' => false, // You definitely don't want to touch this ..
      'martyr' => null,

        ## -> FOR Dev. purposes

      'valid_structure' => null,
      'structure_info' => null,
      'puzzle' => null,
      'tongue' =>
      [

        'selection' => [],              // For the targeted language
        'bypass' => [],                 // For text that is language independant
        'microdown' => []               // Microdown targeted language-pipe-context

      ]

    ];

    private const SWITCHES = /// For the `toggle` method. Each switch entry value should|must exist as object property as well.
    [

      0 => 'quotes_only',
      1 => 'tags_expand',
      2 => 'attr_expand',
      3 => 'enforce_cdata',
      4 => 'backend_notgood',
      5 => 'evialuate',
      6 => 'avoid_phpxml',
      7 => 'tongue_generator',
      8 => 'source_squeeze',
      9 => 'null_expose',
      10 => 'silent_errors',
      11 => 'error_logging'

    ];

    private const XML_BLUEPRINT = /// Common, xml-alike DTD's blueprint
    [
      'name' => null,         // Node/Tag name
      'type' => null,         // 'DTD' | 'TAG' | 'TEXT' | 'COMMENT'
      'role' => null,         // 'PARENT' | 'STRING' | 'SELF' | 'END'
      'tabs' => null,         // How many tab indents
      'text' => '**',         // Printable content
      'attr' => '**'          // Attributes

    ];

    private const L_MARKS = /// Language Marks
    [
      "\341\200\274" => 'mt',                   // 0 ြ            Main text
      "\352\251\265" => 'tb',                   // 1 ꩵ          Text bypass (do not translate)
      "\342\253\266" => 'ab',                   // 2 ⫶      attribute bypass (do not translate)
      "\342\201\202" => 'sm',                   // 3 ⁂     String Micro

      // Attribute values that have important language roles below
      "\352\200\210" => 'at',                   // 4 ꀈ      <img alt=text
      "\360\220\212\230" => 'tt',               // 5 𐊘      <* title=text
      "\340\271\233" => 'pt',                   // 6 ๛      <input placeholder=text
      "\342\246\232" => 'db',                   // 7 ⦚       * data-before=text

    ];

    private const CDATA = /// For CSS and Javascript within HTML
    [
      0 =>  P::C['ss'] . P::C['as'].
            P::C['lt'] . P::C['e'].
            P::C['sl'] . 'CDATA'. P::C['sl'].
            P::C['as'] . P::C['ss']

    , 1 =>  P::C['ss'] . P::C['as'].
            P::C['sr'] . P::C['sr'].
            P::C['gt'] . P::C['as'].
            P::C['ss']
    ];

    private const PHPS = /// PHP Substitutions + realones
    [
      0 => [
        'a' => "\173\173\72",
        'e' => "\173\173\75",
        'z' => "\72\175\175"
      ],
      1 => [
        'a' => "\74\77\160\150\160",
        'e' => "\74\77\75",
        'z' => "\77\76"
      ]
    ];

    private const HEADER = /// Document Type Definitions
    [
      '5' =>  "\41\104\117\103\124\131\120\105\40\150\164\155\154",
      'X' =>  "\77\170\155\154\40\166\145\162\163\151\157\156\75\42"
            . "\61\56\60\42\40\145\156\143\157\144\151\156\147\75\42"
            . "\125\124\106\55\70\42\77"
    ];

    private const EOL_MASK = "\307\202\341\233\244\307\202";
      /// Line breaker/binder, during str_replacement(s) iteration

  }

  trait Member {
    /*:*/

    /* - Public members
    */

      public mixed $objectPropertyFor = null;
        /// Transfer Holder

    /* - Private worker members
    */

      private int|string $dtd_check = '5';
        /// Be it XML or HTML5.
        /// If `X`ml, self-closing tags will have ending like this: ' />'

      private ?string $martyr = null;
        /// $string argument's - first - end.

      private ?array $valid_structure = null;
        /// Raw funcdown content, starcoded, ironed - in an array

      private ?array $structure_info = null;
        /// Valid structure's, polished, semi-readable derrivate

      private null|string|array $puzzle = null;
        /// Generated HTML5 | XML markup puzzle-code

      private array $tongue = /// Extracted text from parsed source
      [
        'selection' => [],              // For the targeted language
        'bypass' => [],                 // For text that is language independant
        'microdown' => []               // Microdown targeted language-pipe-context
      ];


      private ?string $inputunique = null;
        /// sha1 sum of the current funcdown parse string

      private false|string $e_code = false;
        /// If this property remains false, funcdown syntax is correct and header() --> die() are bypassed;

      private int $tab_depth = 2;
        /// How many tabs for nested tags/strings

      private array $linter_count = /// Linter inspection basis
      [
        'bar' => 0,
        'parenthesis' => null,
        'curly' => null,
        'square' => null,
      ];


    /* - Private Static, transportation members
    */

      private static ?array $temp_path_nanny = null;
        /// Funcdown will delete lost-zombie files found within - upon `__destruct`

      private static ?array $mcd_transport = null;
        /// Transfers data between closured callbacks and standard methods

      private static ?string $markup = null;
        /// Funcdown requested result (as generated XML/HTML) is stored/finished here.

      private static ?int $microint = null;
        /// This integer is responsible for the microdown index precision



    /* - Runtime Properties
    */
        // Boolean members

      private bool $quotes_only;
        /// If true, no matter how You write attribute values, all of them will be quoted. This is neat especially for XML files.

      private bool $tags_expand;
        /// If true, uppercased single or double chars will be expanded as tag names.

      private bool $attr_expand;
        /// If true, predefined characters will be expanded as attribute names.

      private bool $enforce_cdata;
        /// If true, predefined characters will be expanded as attribute names.

      private bool $backend_notgood;
        /// If true, all php code as well as fphp code will be wiped/removed.
        /// As long as this option remains false, `$this-> avoid_phpxml` will have some meaning.

      private bool $evialuate;
        /// If true, all detected php code will be eval()'d directly from memory
        /// instead of using regular/default buffer captured with `require` construct.
        /// note: Ppl say that eval is evil and it is last "sane" option one would use.

      private bool $avoid_phpxml;
        /// If true, funcdown will panic when original php tags are found within it's source file, thus allowing only fphp.

      private bool $tongue_generator;
        /// If false, funcdown will not write/save content for translations. Some other library may. -- V2 >= feature.

      private bool $source_squeeze;
        /// If this is false, source code will be as much as it can be - polite.
        /// Single-lined it will be, as much as it can be, otherwise.

      private bool $null_expose;
        /// If true, $this-> expose() method will give nothing except for the 0 value that will `get_object_vars` and display 'em as list.
        /// If true, all properties except private static ones, will have their default value after the request has been processed.

      private bool $silent_errors;
        /// No screen messages (but still writes to log file) on error - if true.

      private bool $error_logging;
        /// If true, funcdown will write error/warning messages to a file.

        // STALL config members

      private int $ident_eol = 0;
        /// How much spaces to trim to left side of the source code area.

      private ?string $micro_blacklist = null;
        /// Blacklisted microdown tags.

      private ?array $microBlack = null;
        /// Derrived array out of micro_blacklist string.

      private ?string $temp_path = null;
        /// Will be populated later, provided by .ini file or self RUNTIME_CONFIGURATION constant.

      private ?string $aid_path = null;
        /// Will be populated later, provided by .ini file or self RUNTIME_CONFIGURATION constant.

      private ?string $tongue_path = null;
        /// Will be populated later, provided by .ini file or self RUNTIME_CONFIGURATION constant. -- V2 >= feature.
  }

  trait Load {
    ///

    use Workers
      , Microdown
      , Native
      , PairCode
      , Encapsulate
      , PublicApi
      , PrConst
      , Member;
      /// combine ALL traits
  }

}

namespace H\scope {
  /*: Hngts Class nest.
  */

  class Funcdown extends \H\Application {
    ///
    use \H\bag\funcdown\Load;
      /// Implements entire funcdown library

    public function __construct (int|string $doctype = '5') {
      /*:*/ $this-> funcdown_atom_request ($doctype);
    }

    public function __destruct() {
      /*:*/ self::TPnanny();
    }
    
    // Embeded Configuration
    
    private static function read_conf (string $K): mixed {
      /// Fetch proper runtime property or else.
      return ((e ($C, self::RUNTIME_CONFIGURATION))
        ? ((str_contains ($K, ':') && ([$in, $s] = explode (':', $K)))
          ? ($C[$in][$s] ?? ($C[$in] ?? $K)) 
          : ($C[$K] ?? $K)
        ) : []
      );
    }

    public const NAMED_FUNCDOWN_GLOBAL = true;
      /// One Default Instance as Named Constant ?
    
    private const RUNTIME_CONFIGURATION = ///
    [
      'toggle' => [
        'quotes_only' => true,
        'tags_expand' => true,
        'attr_expand' => true,
        'enforce_cdata' => true,
        'backend_notgood' => false,
        'evialuate' => false,
        'avoid_phpxml' => false,
        'tongue_generator' => false,
        //~ '; ^^ Has no usage, not yet implemeneted.
        'source_squeeze' => false,
        'null_expose' => false,
        'silent_errors' => false,
        'error_logging' => true,
      ],
      'stall' => [
        'ident_eol' => 2,
        'micro_blacklist' => '',
        'aid_path' => '',
        'temp_path' => '',
        //~ ; Default/fallback temporal directory for when php code is inside funcdown.
        //~ ; Empty value will result in getting system temp dir.
        'tongue_path' => '',
      ],
    ];

  }
  
  defined ('Funcdown') or (
    !Funcdown::NAMED_FUNCDOWN_GLOBAL or
      define ('Funcdown', new Funcdown)
  );
  
  return true;
}

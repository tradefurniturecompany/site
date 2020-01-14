<?php
/*-----------------------------------------------------------------------------+
| MagneticOne                                                                  |
| Copyright (c) 2008 MagneticOne.com <contact@magneticone.com>                 |
| All rights reserved                                                          |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "license.txt"|
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE   |
| AT THE FOLLOWING URL: http://www.magneticone.com/store/license.php           |
|                                                                              |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE  |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  MAGNETICONE |
| (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING          |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").    |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT  |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING,  |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY  |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS  |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS  |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND  |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS  |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE  |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE. |
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.       |
|                                                                              |
| The Developer of the Code is MagneticOne,                                    |
| Copyright (C) 2006 - 2016 All Rights Reserved.                            |
+-----------------------------------------------------------------------------*/

namespace Cart2Cart\Login\Helper\Encryption\Lib;

class PasswordHash
{
  /**
   * @var string
   */
  private $itoa64;

  /**
   * @var int
   */
  private $iterationCountLog2;

  /**
   * @var string
   */
  private $portableHashes;

  /**
   * @var string
   */
  private $randomState;

  /**
   * @param $iterationCountLog2
   * @param $portableHashes
   */
  function __construct($iterationCountLog2, $portableHashes)
  {
    $this->itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    if ($iterationCountLog2 < 4 || $iterationCountLog2 > 31)
      $iterationCountLog2 = 8;
    $this->iterationCountLog2 = $iterationCountLog2;

    $this->$portableHashes = $portableHashes;

    $this->randomState = microtime();
    if (function_exists('getmypid'))
      $this->randomState .= getmypid();
  }


  /**
   * @param $count
   * @return string
   */
  function get_random_bytes($count)
  {
    $output = '';
    if (is_readable('/dev/urandom') &&
      ($fh = @fopen('/dev/urandom', 'rb'))
    ) {
      $output = fread($fh, $count);
      fclose($fh);
    }

    if (strlen($output) < $count) {
      $output = '';
      for ($i = 0; $i < $count; $i += 16) {
        $this->randomState =
          md5(microtime() . $this->randomState);
        $output .=
          pack('H*', md5($this->randomState));
      }
      $output = substr($output, 0, $count);
    }

    return $output;
  }

  /**
   * @param $input
   * @param $count
   * @return string
   */
  function encode64($input, $count)
  {
    $output = '';
    $i = 0;
    do {
      $value = ord($input[$i++]);
      $output .= $this->itoa64[$value & 0x3f];
      if ($i < $count)
        $value |= ord($input[$i]) << 8;
      $output .= $this->itoa64[($value >> 6) & 0x3f];
      if ($i++ >= $count)
        break;
      if ($i < $count)
        $value |= ord($input[$i]) << 16;
      $output .= $this->itoa64[($value >> 12) & 0x3f];
      if ($i++ >= $count)
        break;
      $output .= $this->itoa64[($value >> 18) & 0x3f];
    } while ($i < $count);

    return $output;
  }

  /**
   * @param $input
   * @return string
   */
  function gensalt_private($input)
  {
    $output = '$P$';
    $output .= $this->itoa64[min($this->iterationCountLog2 +
      ((PHP_VERSION >= '5') ? 5 : 3), 30)];
    $output .= $this->encode64($input, 6);

    return $output;
  }

  /**
   * @param $password
   * @param $setting
   * @return string
   */
  function crypt_private($password, $setting)
  {
    $output = '*0';
    if (substr($setting, 0, 2) == $output)
      $output = '*1';

    $id = substr($setting, 0, 3);

    if ($id != '$P$' && $id != '$H$')
      return $output;

    $countLog2 = strpos($this->itoa64, $setting[3]);
    if ($countLog2 < 7 || $countLog2 > 30)
      return $output;

    $count = 1 << $countLog2;

    $salt = substr($setting, 4, 8);
    if (strlen($salt) != 8)
      return $output;

    if (PHP_VERSION >= '5') {
      $hash = md5($salt . $password, TRUE);
      do {
        $hash = md5($hash . $password, TRUE);
      } while (--$count);
    } else {
      $hash = pack('H*', md5($salt . $password));
      do {
        $hash = pack('H*', md5($hash . $password));
      } while (--$count);
    }

    $output = substr($setting, 0, 12);
    $output .= $this->encode64($hash, 16);

    return $output;
  }

  /**
   * @param $input
   * @return string
   */
  function gensalt_extended($input)
  {
    $countLog2 = min($this->iterationCountLog2 + 8, 24);

    $count = (1 << $countLog2) - 1;

    $output = '_';
    $output .= $this->itoa64[$count & 0x3f];
    $output .= $this->itoa64[($count >> 6) & 0x3f];
    $output .= $this->itoa64[($count >> 12) & 0x3f];
    $output .= $this->itoa64[($count >> 18) & 0x3f];

    $output .= $this->encode64($input, 3);

    return $output;
  }

  /**
   * @param $input
   * @return string
   */
  function gensalt_blowfish($input)
  {
    $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    $output = '$2a$';
    $output .= chr(ord('0') + $this->iterationCountLog2 / 10);
    $output .= chr(ord('0') + $this->iterationCountLog2 % 10);
    $output .= '$';

    $i = 0;
    do {
      $c1 = ord($input[$i++]);
      $output .= $itoa64[$c1 >> 2];
      $c1 = ($c1 & 0x03) << 4;
      if ($i >= 16) {
        $output .= $itoa64[$c1];
        break;
      }

      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 4;
      $output .= $itoa64[$c1];
      $c1 = ($c2 & 0x0f) << 2;

      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 6;
      $output .= $itoa64[$c1];
      $output .= $itoa64[$c2 & 0x3f];
    } while (1);

    return $output;
  }

  /**
   * @param $password
   * @return string
   */
  function HashPassword($password)
  {
    $random = '';

    if (CRYPT_BLOWFISH == 1 && !$this->$portableHashes) {
      $random = $this->get_random_bytes(16);
      $hash =
        crypt($password, $this->gensalt_blowfish($random));
      if (strlen($hash) == 60)
        return $hash;
    }

    if (CRYPT_EXT_DES == 1 && !$this->$portableHashes) {
      if (strlen($random) < 3)
        $random = $this->get_random_bytes(3);
      $hash =
        crypt($password, $this->gensalt_extended($random));
      if (strlen($hash) == 20)
        return $hash;
    }

    if (strlen($random) < 6)
      $random = $this->get_random_bytes(6);
    $hash =
      $this->crypt_private($password,
        $this->gensalt_private($random));
    if (strlen($hash) == 34)
      return $hash;

    return '*';
  }

  /**
   * @param $password
   * @param $stored_hash
   * @return bool
   */
  function CheckPassword($password, $stored_hash)
  {
    $hash = $this->crypt_private($password, $stored_hash);
    if ($hash[0] == '*')
      $hash = crypt($password, $stored_hash);

    return $hash == $stored_hash;
  }
}
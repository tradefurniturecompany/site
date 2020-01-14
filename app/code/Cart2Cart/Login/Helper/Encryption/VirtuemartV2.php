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

namespace Cart2Cart\Login\Helper\Encryption;

class VirtuemartV2 extends VirtuemartV1
{
  /**
   * @inheritdoc
   */
  public function validatePassword($hash, $password, $salt)
  {
    if ($salt && parent::validatePassword($hash, $password, $salt)) {
      return true;
    }

    if (strpos($hash, '$P$') === 0) {
      $phpass = new Lib\PasswordHash(10, true);
      return $phpass->CheckPassword($password, $hash);
    } elseif ($hash[0] == '$') {
      return $this->passwordVerify($password, $hash);
    } elseif (substr($hash, 0, 8) == '{SHA256}') {
      $parts = explode(':', $hash);
      $salt = @$parts[1];

      $encrypted = ($salt) ? hash('sha256', $password . $salt) . ':' . $salt : hash('sha256', $password);
      $testcrypt = '{SHA256}' . $encrypted;

      return $this->_timingSafeCompare($hash, $testcrypt);
    }

    $parts = explode(':', $hash);
    $salt = @$parts[1];

    $testcrypt = md5($password . $salt) . ($salt ? ':' . $salt : (strpos($hash, ':') !== false ? ':' : ''));

    return $this->_timingSafeCompare($hash, $testcrypt);
  }

  /**
   * @param $password
   * @param $hash
   * @return bool
   */
  private function passwordVerify($password, $hash)
  {
    if (!function_exists('crypt')) {
      return false;
    }

    $ret = crypt($password, $hash);
    if (!is_string($ret) || $this->_strlen($ret) != $this->_strlen($hash) || $this->_strlen($ret) <= 13) {
      return false;
    }

    $status = 0;
    for ($i = 0; $i < $this->_strlen($ret); $i++) {
      $status |= (ord($ret[$i]) ^ ord($hash[$i]));
    }

    return $status === 0;
  }

  /**
   * @param $string
   * @return int
   */
  private function _strlen($string)
  {
    if (function_exists('mb_strlen')) {
      return mb_strlen($string, '8bit');
    }
    return strlen($string);
  }

  /**
   * @param $known
   * @param $unknown
   * @return bool
   */
  private function _timingSafeCompare($known, $unknown)
  {
    $known .= chr(0);
    $unknown .= chr(0);

    $knownLength = strlen($known);
    $unknownLength = strlen($unknown);

    $result = $knownLength - $unknownLength;

    for ($i = 0; $i < $unknownLength; $i++) {
      $result |= (ord($known[$i % $knownLength]) ^ ord($unknown[$i]));
    }

    return $result === 0;
  }
}
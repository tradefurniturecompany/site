<?php

namespace Cart2Cart\Login\Helper\Encryption;

class Zencart implements EncryptorInterface
{
  protected static $instance = null;

  public function __construct()
  {
    $phpVersion = PHP_VERSION;
    if (version_compare($phpVersion, '5.3.7', '<')) {
      define('PASSWORD_BCRYPT', 1);
      define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);

      if (! function_exists('password_hash')) {
        function password_hash($plain, $algo = null)
        {
          $password = '';
          for($i = 0; $i < 40; $i ++) {
            $password .= $this->zen_rand();
          }
          $salt = hash('sha256', $password);
          $password = hash('sha256', $salt . $plain) . ':' . $salt;

          return $password;
        }
      }

      if (! function_exists('password_verify')) {
        function password_verify($plain, $encrypted)
        {
          if ($this->zen_not_null($plain) && $this->zen_not_null($encrypted)) {
            $stack = explode(':', $encrypted);
            if (sizeof($stack) != 2)
              return false;
            if ($this->validatePasswordOldMd5($plain, $encrypted) === true) {
              return true;
            } elseif ($this->validatePasswordCompatSha256($plain, $encrypted) === true) {
              return true;
            }
          }
          return false;
        }
      }
      if (! function_exists('password_needs_rehash')) {
        function password_needs_rehash($hash, $algo = null)
        {
          $tmp = explode(':', $hash);
          if (count($tmp) == 2 && strlen($tmp [1]) == 2) {
            return true;
          } else {
            return false;
          }
        }
      }
    } elseif (version_compare($phpVersion, '5.5.0', '<')) {
      function password_hash($password, $algo, array $options = array()) {
        if (!function_exists('crypt')) {
          return false;
        }
        if (!is_string($password)) {
          return false;
        }
        if (!is_int($algo)) {
          return false;
        }
        switch ($algo) {
          case PASSWORD_BCRYPT:
            // Note that this is a C constant, but not exposed to PHP, so we don't define it here.
            $cost = 10;
            if (isset($options['cost'])) {
              $cost = $options['cost'];
              if ($cost < 4 || $cost > 31) {
                trigger_error(sprintf("password_hash(): Invalid bcrypt cost parameter specified: %d", $cost), E_USER_WARNING);
                return null;
              }
            }
            // The length of salt to generate
            $raw_salt_len = 16;
            // The length required in the final serialization
            $required_salt_len = 22;
            $hash_format = sprintf("$2y$%02d$", $cost);
          break;
          default:
            trigger_error(sprintf("password_hash(): Unknown password hashing algorithm: %s", $algo), E_USER_WARNING);
            return null;
        }
        if (isset($options['salt'])) {
          switch (gettype($options['salt'])) {
            case 'NULL':
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
              $salt = (string) $options['salt'];
            break;
            case 'object':
              if (method_exists($options['salt'], '__tostring')) {
                $salt = (string) $options['salt'];
                break;
              }
            case 'array':
            case 'resource':
            default:
              trigger_error('password_hash(): Non-string salt parameter supplied', E_USER_WARNING);
              return null;
          }
          if (strlen($salt) < $required_salt_len) {
            trigger_error(sprintf("password_hash(): Provided salt is too short: %d expecting %d", strlen($salt), $required_salt_len), E_USER_WARNING);
            return null;
          } elseif (0 == preg_match('#^[a-zA-Z0-9./]+$#D', $salt)) {
            $salt = str_replace('+', '.', base64_encode($salt));
          }
        } else {
          $buffer = '';
          $buffer_valid = false;
          if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
            $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
            if ($buffer) {
              $buffer_valid = true;
            }
          }
          if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
            $buffer = openssl_random_pseudo_bytes($raw_salt_len);
            if ($buffer) {
              $buffer_valid = true;
            }
          }
          if (!$buffer_valid && is_readable('/dev/urandom')) {
            $f = fopen('/dev/urandom', 'r');
            $read = strlen($buffer);
            while ($read < $raw_salt_len) {
              $buffer .= fread($f, $raw_salt_len - $read);
              $read = strlen($buffer);
            }
            fclose($f);
            if ($read >= $raw_salt_len) {
              $buffer_valid = true;
            }
          }
          if (!$buffer_valid || strlen($buffer) < $raw_salt_len) {
            $bl = strlen($buffer);
            for ($i = 0; $i < $raw_salt_len; $i++) {
              if ($i < $bl) {
                $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
              } else {
                $buffer .= chr(mt_rand(0, 255));
              }
            }
          }
          $salt = str_replace('+', '.', base64_encode($buffer));
        }
        $salt = substr($salt, 0, $required_salt_len);

        $hash = $hash_format . $salt;

        $ret = crypt($password, $hash);

        if (!is_string($ret) || strlen($ret) <= 13) {
          return false;
        }

        return $ret;
      }

      function password_get_info($hash) {
        $return = array(
          'algo' => 0,
          'algoName' => 'unknown',
          'options' => array(),
        );
        if (substr($hash, 0, 4) == '$2y$' && strlen($hash) == 60) {
          $return['algo'] = PASSWORD_BCRYPT;
          $return['algoName'] = 'bcrypt';
          list($cost) = sscanf($hash, "$2y$%d$");
          $return['options']['cost'] = $cost;
        }
        return $return;
      }

      function password_needs_rehash($hash, $algo, array $options = array()) {
        $info = password_get_info($hash);
        if ($info['algo'] != $algo) {
          return true;
        }
        switch ($algo) {
          case PASSWORD_BCRYPT:
            $cost = isset($options['cost']) ? $options['cost'] : 10;
            if ($cost != $info['options']['cost']) {
              return true;
            }
          break;
        }
        return false;
      }

      function password_verify($password, $hash) {
        if (!function_exists('crypt')) {
          trigger_error("Crypt must be loaded for password_verify to function", E_USER_WARNING);
          return false;
        }
        $ret = crypt($password, $hash);
        if (!is_string($ret) || strlen($ret) != strlen($hash) || strlen($ret) <= 13) {
          return false;
        }

        $status = 0;
        for ($i = 0; $i < strlen($ret); $i++) {
          $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }

        return $status === 0;
      }
    }
  }

  public function validatePassword($hash, $password, $salt)
  {
    $type = $this->detectPasswordType($hash . ':'. $salt);
    if ($type != 'unknown') {
      $method = 'validatePassword' . ucfirst($type);
      return $this->{$method}($password, $hash . ':'. $salt);
    }

    $result = password_verify($password, $hash);
    return $result;
  }

  function detectPasswordType($encryptedPassword)
  {
    $type = 'unknown';
    $tmp = explode(':', $encryptedPassword);
    if (count($tmp) == 2) {
      if (strlen($tmp [1]) > 2) {
        $type = 'compatSha256';
      } elseif (strlen($tmp [1]) == 2) {
        $type = 'oldMd5';
      }
    }
    return $type;
  }

  public function validatePasswordOldMd5($plain, $encrypted)
  {
    if ($this->zen_not_null($plain) && $this->zen_not_null($encrypted)) {
      $stack = explode(':', $encrypted);
      if (sizeof($stack) != 2)
        return false;
      if (md5($stack [1] . $plain) == $stack [0]) {
        return true;
      }
    }
    return false;
  }

  public function validatePasswordCompatSha256($plain, $encrypted)
  {
    if ($this->zen_not_null($plain) && $this->zen_not_null($encrypted)) {
      $stack = explode(':', $encrypted);
      if (sizeof($stack) != 2)
        return false;
      if (hash('sha256', $stack [1] . $plain) == $stack [0]) {
        return true;
      }
    }
    return false;
  }

  public function hashPassword($plain)
  {
    $hash = password_hash($plain, PASSWORD_DEFAULT);

    return $hash;
  }

  protected function zen_not_null($value)
  {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function zen_rand($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }
}
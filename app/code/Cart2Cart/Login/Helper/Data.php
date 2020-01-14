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

namespace Cart2Cart\Login\Helper;

use Magento\Framework\ObjectManager;

class Data
{
  /**
   * @var ObjectManager\ObjectManager
   */
  private $objectManager;

  private static $_validators;

  /**
   * @param ObjectManager\ObjectManager $objectManger
   */
  public function __construct(ObjectManager\ObjectManager $objectManger)
  {
    $this->objectManager = $objectManger;
  }

  public function getValidators()
  {
    if (self::$_validators === null) {
      $iterator = new \DirectoryIterator(__DIR__ . '/Encryption');
      foreach ($iterator as $fileInfo) {
        if ($fileInfo->isDot() || $fileInfo->getExtension() != 'php' || $fileInfo->getBasename('.php') == 'EncryptorInterface') {
          continue;
        }

        $className = 'Cart2Cart\\Login\\Helper\\Encryption\\' . $fileInfo->getBasename('.php');
        self::$_validators[$className] = $this->objectManager->create($className);
      }
    }

    return self::$_validators;
  }

  /**
   * @param $store
   * @param $version
   * @param $hash
   * @param $password
   * @param $salt
   * @return bool|mixed
   */
  public function passwordIsValid($hash, $password, $salt)
  {
    foreach (self::getValidators() as $validator) {
      if (!method_exists($validator, 'validatePassword')) {
        continue;
      }

      if (call_user_func([$validator, 'validatePassword'], $hash, $password, (string)$salt)) {
        return true;
      }
    }

    return false;
  }
}
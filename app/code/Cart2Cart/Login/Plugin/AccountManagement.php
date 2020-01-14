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

namespace Cart2Cart\Login\Plugin;

use Cart2Cart\Login;
use Cart2Cart\Login\Helper;
use Magento\Customer\Model;

class AccountManagement
{
  const FIELD_PASSWORD_SALT = 'password_salt';

  /**
   * @var Model\CustomerRegistry
   */
  private $customerRegistry;

  /**
   * @var Helper\Data
   */
  private $encryptionHelper;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * @var \Magento\Framework\App\ResourceConnection
   */
  private $resource;

  /**
   * @var \Magento\Framework\DB\Adapter\AdapterInterface
   */
  private $connection;

  /**
   * @param Model\CustomerRegistry $customerRegistry
   * @param Helper\Data $encryptionHelper
   * @param \Psr\Log\LoggerInterface $logger
   * @param \Magento\Framework\App\ResourceConnection $resource
   */
  public function __construct(Model\CustomerRegistry $customerRegistry, Helper\Data $encryptionHelper,
                              \Psr\Log\LoggerInterface $logger, \Magento\Framework\App\ResourceConnection $resource)
  {
    $this->customerRegistry = $customerRegistry;
    $this->encryptionHelper = $encryptionHelper;
    $this->logger = $logger;
    $this->resource = $resource;
  }

  /**
   * @param $subject
   * @param $username
   * @param $password
   */
  public function beforeAuthenticate($subject, $username, $password)
  {
    try {
      $customer = $this->customerRegistry->retrieveByEmail($username);

      if ($this->encryptionHelper->passwordIsValid($customer->getPasswordHash(), $password, $customer->getOrigData(AccountManagement::FIELD_PASSWORD_SALT))) {
        //avoiding customer required fields check
        $this->updateCustomerPassword($customer->getId(), $customer->hashPassword($password));
        $this->deleteTempAttributes($customer->getId());
        //force take password hash from database in \Magento\Customer\Model\AccountManagement
        $this->customerRegistry->remove($customer->getId());
      }
    } catch (\ReflectionException $e) {
      $this->logger->notice($e->getMessage());
    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
      //no such customer
    } catch (\Exception $e) {
      $this->logger->error($e);
    }
  }

  /**
   * @param $customerId
   * @param $password
   */
  private function updateCustomerPassword($customerId, $password)
  {
    $customerEntityTable = $this->resource->getTableName('customer_entity');
    $this->getConnection()->update($customerEntityTable, ['password_hash' => $password], "entity_id = $customerId");
  }

  /**
   * @param $customerId
   */
  private function deleteTempAttributes($customerId)
  {
    //TODO refactor direct query
    $customerEntityVarcharTable = $this->resource->getTableName('customer_entity_varchar');
    $eavAttributeTable = $this->resource->getTableName('eav_attribute');

    $this->getConnection()->query("
      DELETE cev
        FROM {$customerEntityVarcharTable} cev
      INNER JOIN {$eavAttributeTable} ea
        ON cev.attribute_id = ea.attribute_id
      WHERE
        cev.entity_id = {$customerId}
        AND ea.attribute_code = " . $this->getConnection()->quote(AccountManagement::FIELD_PASSWORD_SALT));
  }

  /**
   * @return \Magento\Framework\DB\Adapter\AdapterInterface
   */
  private function getConnection()
  {
    if (!$this->connection) {
      $this->connection = $this->resource->getConnection('core_write');
    }
    return $this->connection;
  }
}



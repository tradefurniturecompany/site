<?php 
/**
  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 */

/**
 * This is a default implementation of the Customweb_Payment_BackendOperation_IOperation interface.
 * 
 * @author Thomas Hunziker
 *
 */
class Customweb_Payment_BackendOperation_DefaultOperation implements Customweb_Payment_BackendOperation_IOperation {

	private $name;
	
	private $description;
	
	private $identifier;
	
	private $orderModifications = false;
	
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets the operation name.
	 * 
	 * @param string $name
	 * @return Customweb_Payment_BackendOperation_DefaultOperation
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Sets the operation description.
	 * 
	 * @param string $description
	 * @return Customweb_Payment_BackendOperation_DefaultOperation
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this->description;
	}
	
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * Sets the operation identifier.
	 * 
	 * @param string $identifier
	 * @return Customweb_Payment_BackendOperation_DefaultOperation
	 */
	public function setIndentifier($identifier) {
		$this->identifier = $identifier;
		return $this;
	}
	
	public function canAcceptOrderModifications() {
		return $this->orderModifications;
	}
	
	/**
	 * Sets whether this operation can accept order modifications or not.
	 * 
	 * @param boolean $accept
	 * @return Customweb_Payment_BackendOperation_DefaultOperation
	 */
	public function setAcceptOrderModifications($accept) {
		$this->orderModifications = $accept;
		return $this;
	}
}
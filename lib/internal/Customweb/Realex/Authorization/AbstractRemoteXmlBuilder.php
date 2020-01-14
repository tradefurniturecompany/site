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
 * This class provides common methods to build XML's for interacting with the 
 * remote authorization interface. 
 * 
 * @author Mathis Kappeler
 *
 */
abstract class Customweb_Realex_Authorization_AbstractRemoteXmlBuilder extends Customweb_Realex_Authorization_AbstractXmlBuilder {
	
	final protected function getMpiOrTssInfoElement() {
		$cavv = $this->getTransaction()->get3DSecureCAVV();
		if ($cavv == null) {
			return $this->getTSSInfoElement();
		}
		else {
			return "<mpi>
					<cavv>" . $this->getTransaction()->get3DSecureCAVV() . "</cavv>
					<xid>" . $this->getTransaction()->get3DSecureXid() . "</xid>
					<eci>" . $this->getTransaction()->getECI() . "</eci>
				</mpi>";
		}
	}
	
	final protected function getReccuringElement() {
		if ($this->getTransaction()->getTransactionContext()->createRecurringAlias()) {
			return "<recurring type='variable' sequence='first'></recurring>";
		}
		else {
			return '';
		}
	}
	
	protected function getCardDataElement() {
		return $this->getCvcElement($this->getTransaction()) . parent::getCardDataElement();
	}
	
	
}
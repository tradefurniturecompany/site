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
 * Classes annotated with this annotation are listed in the backend.
 * 
 * @author Thomas Hunziker
 *
 */
class Customweb_Payment_BackendOperation_Form_Annotation_BackendForm implements Customweb_IAnnotation{

	private $sortOrder = 0;
	
	
	public function setValue($sortOrder) {
		$this->sortOrder = $sortOrder;
	}
	
	public function getSortOrder() {
		return $this->sortOrder;
	}
	
}
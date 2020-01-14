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
 * This class is responsible to process a remote transaction.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Server_ApmAuthorizationProcessor extends Customweb_Realex_Authorization_AbstractXmlProcessor {
	
	public function __construct(Customweb_Realex_Configuration $configuration, Customweb_Realex_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container) {
		$builder = new Customweb_Realex_Authorization_Server_ApmAuthorizationBuilder($transaction, $configuration, $container);
		parent::__construct($configuration, $builder, $transaction, $container);
	}
	
	public function process() {
		return parent::process();
	}
}


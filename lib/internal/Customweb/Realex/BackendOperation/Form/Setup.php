<?php

/**
 *  * You are allowed to use this API in your web application.
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
 * @BackendForm
 */
class Customweb_Realex_BackendOperation_Form_Setup extends Customweb_Payment_BackendOperation_Form_Abstract {

	public function getTitle(){
		return Customweb_I18n_Translation::__("Setup");
	}

	public function getElementGroups(){
		return array(
			$this->getSetupGroup(),
			$this->getUrlGroup(),
			$this->getIpAddressGroup() 
		);
	}

	private function getSetupGroup(){
		$group = new Customweb_Form_ElementGroup();
		$group->setTitle(Customweb_I18n_Translation::__("Short Installation Instructions:"));
		
		$control = new Customweb_Form_Control_Html('description', 
				Customweb_I18n_Translation::__(
						'This is a brief instruction of the main and most important installation steps, which need to be performed when installing the Realex module. For detailed instructions regarding additional and optional settings, please refer to the enclosed instructions in the zip. '));
		$element = new Customweb_Form_WideElement($control);
		$group->addElement($element);
		
		$control = new Customweb_Form_Control_Html('steps', $this->createOrderedList($this->getSteps()));
		
		$element = new Customweb_Form_WideElement($control);
		$group->addElement($element);
		return $group;
	}

	private function getUrlGroup(){
		$group = new Customweb_Form_ElementGroup();
		$group->setTitle('URLs');
		$group->addElement($this->getNotificationUrlElement());
		return $group;
	}

	private function getNotificationUrlElement(){
		$control = new Customweb_Form_Control_Html('notificationURL', $this->getEndpointAdapter()->getUrl('process', 'index'));
		$element = new Customweb_Form_Element(Customweb_I18n_Translation::__("Notification URL"), $control);
		$element->setDescription(
				Customweb_I18n_Translation::__(
						"This URL has to be reported to Realex. The transaction notification must be sent to this URL."));
		return $element;
	}

	private function getIpAddressGroup(){
		$group = new Customweb_Form_ElementGroup();
		$group->setTitle('IP Address');
		$request = new Customweb_Core_Http_Request(new Customweb_Core_Url("http://www.customweb.com/my-ip.php"));
		$client = Customweb_Core_Http_Client_Factory::createClient();
		$response = $client->send($request);
		
		$ip = "";
		if ($response->getStatusCode() == 200) {
			$ip = $response->getBody();
		}
		else {
			$ip = Customweb_I18n_Translation::__("Couldn't obtain IP address.");
		}
		
		$control = new Customweb_Form_Control_Html("ip", 
				Customweb_I18n_Translation::__("Your server IP address is: '@IP'.", array(
					"@IP" => $ip 
				)));
		$group->addElement(new Customweb_Form_WideElement($control));
		return $group;
	}

	private function getTemplateUrlElement(){
		$control = new Customweb_Form_Control_Html('templateURL', $this->getEndpointAdapter()->getUrl('template', 'index'));
		$element = new Customweb_Form_Element(Customweb_I18n_Translation::__("Template URL"), $control);
		$element->setDescription(Customweb_I18n_Translation::__("This is the URL required by the Skin Creator"));
		return $element;
	}

	private function getSteps(){
		return array(
			Customweb_I18n_Translation::__(
					"For the Transaction Feedback please send the URL that is generated in the main module to Realex (<a href='mailto:support@realexpayments.com'>support@realexpayments.com</a>)"),
			Customweb_I18n_Translation::__(
					'In order to be able to open the Payment Page of Realex you must transmit the IP address of your server to Realex.'),
			Customweb_I18n_Translation::__('Activate the payment method and test.') 
		);
	}

	private function createOrderedList(array $steps){
		$list = '<ol>';
		foreach ($steps as $step) {
			$list .= "<li>$step</li>";
		}
		$list .= '</ol>';
		return $list;
	}
}
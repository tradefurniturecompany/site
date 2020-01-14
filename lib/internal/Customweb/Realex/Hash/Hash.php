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
 *
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Hash_Hash{
	private $stringToHash;
	private $hash;
	private $encriptionAlgorithm;
	
	/**
	 * @param string $stringToHash fex. timestamp.merchantid.orderid...payerref.chname.carnumber 
	 * @param string $secret
	 * @param string $encriptionAlgorithm
	 */
	public function __construct($stringToHash, $secret, $encriptionAlgorithm) {
		$this->stringToHash = (string) $stringToHash;
		$this->encriptionAlgorithm = $encriptionAlgorithm;
		$this->calculateHash($secret);
	}
	
	public function getHash(){
		return $this->hash;
	}
	
	public function getHashKeyLowercase(){
		return strtolower($this->encriptionAlgorithm) . "hash";
	}
	
	public function getHashKeyUppercase(){
		return strtoupper($this->getHashKeyLowercase());
	}
	
	public function isHashValid($compareHash){
		if(strtolower($compareHash) == strtolower($this->hash)) {
			return true;
		}
		return false;
	}
	
	public function getEncriptionAlgorithm(){
		return $this->encriptionAlgorithm;
	}
	
	private function calculateHash($secret){
		$tmp = $this->calculateHashIntern($this->stringToHash) . '.' . $secret;
		$this->hash = strtolower($this->calculateHashIntern($tmp));
	}
	
	private function calculateHashIntern($string) {
		if (strtolower($this->encriptionAlgorithm) == 'sha1') {
			return sha1($string);
		}	
		else {
			return md5($string);
		}
	}
	
}
<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Stdlib\Cookie;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\HTTP\Header as HttpHeader;
use Psr\Log\LoggerInterface;

/**
 * CookieManager helps manage the setting, retrieving and deleting of cookies.
 *
 * To aid in security, the cookie manager will make it possible for the application to indicate if the cookie contains
 * sensitive data so that extra protection can be added to the contents of the cookie as well as how the browser
 * stores the cookie.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class PhpCookieManager implements CookieManagerInterface
{
    /**#@+
     * Constants for Cookie manager.
     * RFC 2109 - Page 15
     * http://www.ietf.org/rfc/rfc6265.txt
     */
    const MAX_NUM_COOKIES = 50;
    const MAX_COOKIE_SIZE = 4096;
    const EXPIRE_NOW_TIME = 1;
    const EXPIRE_AT_END_OF_SESSION_TIME = 0;
    /**#@-*/

    /**#@+
     * Constant for metadata array key
     */
    const KEY_EXPIRE_TIME = 'expiry';
    /**#@-*/

    /**#@-*/
    private $scope;

    /**
     * @var CookieReaderInterface
     */
    private $reader;

    /**
     * Logger for warning details.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Object that provides access to HTTP headers.
     *
     * @var HttpHeader
     */
    private $httpHeader;

    /**#@+
     * Constant for SameSite Supported Php Version
     */
    private const SAMESITE_SUPPORTED_PHP_VERSION = '7.3';
    /**#@-*/

    /**#@+
     * Constant for Set-Cookie Header
     */
    private const COOKIE_HEADER = 'Set-Cookie:';
    /**#@-*/

    /**
     * @param CookieScopeInterface $scope
     * @param CookieReaderInterface $reader
     * @param LoggerInterface $logger
     * @param HttpHeader $httpHeader
     */
    public function __construct(
        CookieScopeInterface $scope,
        CookieReaderInterface $reader,
        LoggerInterface $logger = null,
        HttpHeader $httpHeader = null
    ) {
        $this->scope = $scope;
        $this->reader = $reader;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->httpHeader = $httpHeader ?: ObjectManager::getInstance()->get(HttpHeader::class);
    }

    /**
     * Set a value in a private cookie with the given $name $value pairing.
     *
     * Sensitive cookies cannot be accessed by JS. HttpOnly will always be set to true for these cookies.
     *
     * @param string $name
     * @param string $value
     * @param SensitiveCookieMetadata $metadata
     * @return void
     * @throws FailureToSendException Cookie couldn't be sent to the browser.  If this exception isn't thrown,
     * there is still no guarantee that the browser received and accepted the cookie.
     * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
     * @throws InputException If the cookie name is empty or contains invalid characters.
     */
    public function setSensitiveCookie($name, $value, SensitiveCookieMetadata $metadata = null)
    {
        $metadataArray = $this->scope->getSensitiveCookieMetadata($metadata)->__toArray();
        $this->setCookie((string)$name, (string)$value, $metadataArray);
    }

    /**
     * Set a value in a public cookie with the given $name $value pairing.
     *
     * Public cookies can be accessed by JS. HttpOnly will be set to false by default for these cookies,
     * but can be changed to true.
     *
     * @param string $name
     * @param string $value
     * @param PublicCookieMetadata $metadata
     * @return void
     * @throws FailureToSendException If cookie couldn't be sent to the browser.
     * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
     * @throws InputException If the cookie name is empty or contains invalid characters.
     */
    public function setPublicCookie($name, $value, PublicCookieMetadata $metadata = null)
    {
        $metadataArray = $this->scope->getPublicCookieMetadata($metadata)->__toArray();
        $this->setCookie((string)$name, (string)$value, $metadataArray);
    }

    /**
     * Set a value in a cookie with the given $name $value pairing.
     *
     * @param string $name
     * @param string $value
     * @param array $metadataArray
     * @return void
     * @throws FailureToSendException If cookie couldn't be sent to the browser.
     * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
     * @throws InputException If the cookie name is empty or contains invalid characters.
     */
    protected function setCookie($name, $value, array $metadataArray)
    {
        $expire = $this->computeExpirationTime($metadataArray);

        $this->checkAbilityToSendCookie($name, $value);

        if (version_compare(phpversion(), self::SAMESITE_SUPPORTED_PHP_VERSION, '>=')) {

            $phpSetcookieSuccess = setcookie(
                $name,
                $value,
                [
                    'expires' => $expire,
                    'path' => $this->extractValue(CookieMetadata::KEY_PATH, $metadataArray, ''),
                    'domain' => $this->extractValue(CookieMetadata::KEY_DOMAIN, $metadataArray, ''),
                    'secure' => $this->extractValue(CookieMetadata::KEY_SECURE, $metadataArray, false),
                    'httponly' => $this->extractValue(CookieMetadata::KEY_HTTP_ONLY, $metadataArray, false),
                    'samesite' => $this->extractValue(CookieMetadata::KEY_SAME_SITE, $metadataArray, 'Lax')
                ]
            );
            if (!$phpSetcookieSuccess) {
                $params['name'] = $name;
                if ($value == '') {
                    throw new FailureToSendException(
                        new Phrase('The cookie with "%name" cookieName couldn\'t be deleted.', $params)
                    );
                } else {
                    $exceptionMessage = 'The cookie with "%name" cookieName couldn\'t be sent. Please try again later.';
                    throw new FailureToSendException(
                        new Phrase($exceptionMessage, $params)
                    );
                }
            }
        } else {
            $this->setCookieSameSite($name, $value, $metadataArray);
        }
    }

    /**
     * Retrieve the size of a cookie.
     *
     * The size of a cookie is determined by the length of 'name=value' portion of the cookie.
     *
     * @param string $name
     * @param string $value
     * @return int
     */
    private function sizeOfCookie($name, $value)
    {
        // The constant '1' is the length of the equal sign in 'name=value'.
        return strlen($name) + 1 + strlen($value);
    }

    /**
     * Determines ability to send cookies, based on the number of existing cookies and cookie size
     *
     * @param string $name
     * @param string|null $value
     * @return void if it is possible to send the cookie
     * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
     * @throws InputException If the cookie name is empty or contains invalid characters.
     */
    private function checkAbilityToSendCookie($name, $value)
    {
        if ($name == '' || preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            throw new InputException(
                new Phrase(
                    'Cookie name cannot be empty and cannot contain these characters: =,; \\t\\r\\n\\013\\014'
                )
            );
        }

        $numCookies = count($_COOKIE);

        if (!isset($_COOKIE[$name])) {
            $numCookies++;
        }

        $sizeOfCookie = $this->sizeOfCookie($name, $value);

        if ($numCookies > static::MAX_NUM_COOKIES) {
            $this->logger->warning(
                new Phrase('Unable to send the cookie. Maximum number of cookies would be exceeded.'),
                array_merge($_COOKIE, ['user-agent' => $this->httpHeader->getHttpUserAgent()])
            );
        }

        if ($sizeOfCookie > static::MAX_COOKIE_SIZE) {
        	# 2020-10-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# 1) «Unable to send the cookie. Size of 'mage-messages' is <…> bytes.»
			# https://github.com/tradefurniturecompany/site/issues/186
			# 2) https://magento.stackexchange.com/a/292945
			# 3) https://magento.stackexchange.com/a/314402
			# 4) https://magento.stackexchange.com/a/307744
        	df_log([
				'issue' => 'https://github.com/tradefurniturecompany/site/issues/186'
				,'name' => $name
				,'size' => $sizeOfCookie
				,'value' => $value
			]);
            throw new CookieSizeLimitReachedException(
                new Phrase(
                    'Unable to send the cookie. Size of \'%name\' is %size bytes.',
                    [
                        'name' => $name,
                        'size' => $sizeOfCookie,
                    ]
                )
            );
        }
    }

    /**
     * Determines the expiration time of a cookie.
     *
     * @param array $metadataArray
     * @return int in seconds since the Unix epoch.
     */
    private function computeExpirationTime(array $metadataArray)
    {
        if (isset($metadataArray[PhpCookieManager::KEY_EXPIRE_TIME])
            && $metadataArray[PhpCookieManager::KEY_EXPIRE_TIME] < time()
        ) {
            $expireTime = $metadataArray[PhpCookieManager::KEY_EXPIRE_TIME];
        } else {
            if (isset($metadataArray[CookieMetadata::KEY_DURATION])) {
                $expireTime = $metadataArray[CookieMetadata::KEY_DURATION] + time();
            } else {
                $expireTime = PhpCookieManager::EXPIRE_AT_END_OF_SESSION_TIME;
            }
        }

        return $expireTime;
    }

    /**
     * Determines the value to be used as a $parameter.
     *
     * If $metadataArray[$parameter] is not set, returns the $defaultValue.
     *
     * @param string $parameter
     * @param array $metadataArray
     * @param string|boolean|int|null $defaultValue
     * @return string|boolean|int|null
     */
    private function extractValue($parameter, array $metadataArray, $defaultValue)
    {
        if (array_key_exists($parameter, $metadataArray)) {
            return $metadataArray[$parameter];
        } else {
            return $defaultValue;
        }
    }

    /**
     * Retrieve a value from a cookie.
     *
     * @param string $name
     * @param string|null $default The default value to return if no value could be found for the given $name.
     * @return string|null
     */
    public function getCookie($name, $default = null)
    {
        return $this->reader->getCookie($name, $default);
    }

    /**
     * Deletes a cookie with the given name.
     *
     * @param string $name
     * @param CookieMetadata $metadata
     * @return void
     * @throws FailureToSendException If cookie couldn't be sent to the browser.
     *     If this exception isn't thrown, there is still no guarantee that the browser
     *     received and accepted the request to delete this cookie.
     * @throws InputException If the cookie name is empty or contains invalid characters.
     */
    public function deleteCookie($name, CookieMetadata $metadata = null)
    {
        $metadataArray = $this->scope->getCookieMetadata($metadata)->__toArray();

        // explicitly set an expiration time in the metadataArray.
        $metadataArray[PhpCookieManager::KEY_EXPIRE_TIME] = PhpCookieManager::EXPIRE_NOW_TIME;

        $this->checkAbilityToSendCookie($name, '');

        // cookie value set to empty string to delete from the remote client
        $this->setCookie($name, '', $metadataArray);

        // Remove the cookie
        unset($_COOKIE[$name]);
    }

    /**
     * Polyfill for Set-Cookie with support for SameSite attribute
     *
     * Supports Php version 7.2 and lower
     *
     * @param string $name
     * @param string $value
     * @param array $metadataArray
     * @throws FailureToSendException
     * @return void
     */
    private function setCookieSameSite(string $name, string $value, array $metadataArray): void
    {

        $expires = $this->computeExpirationTime($metadataArray);
        $path = $this->extractValue(CookieMetadata::KEY_PATH, $metadataArray, '');
        $domain = $this->extractValue(CookieMetadata::KEY_DOMAIN, $metadataArray, '');
        $secure = $this->extractValue(CookieMetadata::KEY_SECURE, $metadataArray, false);
        $httpOnly = $this->extractValue(CookieMetadata::KEY_HTTP_ONLY, $metadataArray, false);
        $sameSite = $this->extractValue(CookieMetadata::KEY_SAME_SITE, $metadataArray, 'Lax');
        $params = [];
        $setCookieSuccess = false;

        if ('' === $value) {
            $params[] = $name . '=' . 'deleted';

        } else {
            $params[] = $name . '=' . rawurlencode($value);
        }

        if (0 !== $expires) {
            $formattedExpirationTime = gmdate('D, d-M-Y H:i:s T', $expires);
            $params[] = sprintf('expires=%s', $formattedExpirationTime);
        }

        if ($path) {
            $params[] = sprintf('path=%s', $path);
        }

        if ($domain) {
            $params[] = sprintf('domain=%s', $domain);
        }

        if ($httpOnly) {
            $params[] = 'HttpOnly';
        }

        if ($secure) {
            $params[] = 'secure';
        }

        $params[] = sprintf('SameSite=%s', $sameSite);
        $header = sprintf(self::COOKIE_HEADER . "%s", implode('; ', $params));
        header($header, false);

        $setCookieSuccess = array_filter(headers_list(), function ($value) use ($header) {
            return strpos($value, $header) !== false;
        });

        if (!$setCookieSuccess) {
            $args['name'] = $name;
            if ($value == '') {
                throw new FailureToSendException(
                    new Phrase('The cookie with "%name" cookieName couldn\'t be deleted.', $args)
                );
            } else {
                $exceptionMessage = 'The cookie with "%name" cookieName couldn\'t be sent. Please try again later.';
                throw new FailureToSendException(
                    new Phrase($exceptionMessage, $args)
                );
            }
        }
    }
}

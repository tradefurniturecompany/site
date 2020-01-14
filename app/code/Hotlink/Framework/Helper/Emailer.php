<?php
namespace Hotlink\Framework\Helper;

class Emailer
{

    protected $scopeConfig;

    public function __construct( \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function send( array $to, $subject, $content, array $cc = array(), $html = true )
    {
        $senderName = $this->scopeConfig->getValue( 'trans_email/ident_general/name' , \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        if ( ! $senderName )
            {
                $senderName = 'Magento';
            }
        $senderEmail = $this->scopeConfig->getValue( 'trans_email/ident_general/email' , \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        if ( ! $senderEmail )
            {
                $senderEmail = 'root@localhost.com';
            }

        $zendMail = new \Zend_Mail( 'utf-8' );
        $zendMail->setFrom( $senderEmail, $senderName );
        $zendMail->setSubject( $subject );

        foreach ( $to as $recipient )
            {
                $zendMail->addTo( $recipient );
            }

        foreach ( $cc as $recipient )
            {
                $zendMail->addCc( $recipient );
            }

        if ( $html )
            {
                $zendMail->setBodyHtml( $content );
            }
        else
            {
                $zendMail->setBodyText( $content );
            }
        $zendMail->send();
    }

}
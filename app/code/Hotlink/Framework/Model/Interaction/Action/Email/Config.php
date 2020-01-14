<?php
namespace Hotlink\Framework\Model\Interaction\Action\Email;

class Config extends \Hotlink\Framework\Model\Interaction\Action\Config\AbstractConfig
{
    const PATH_EMAIL_ALLOWED_LEVELS = 'hotlink_framework/installation/interaction_report_email_level_include';

    public function getSuccess()
    {
        return $this->interaction->getConfig()->getCsvField( 'action_email_on_success' );
    }

    public function getFail()
    {
        return $this->interaction->getConfig()->getCsvField( 'action_email_on_fail' );
    }

    public function getEmailLevelsInclude()
    {
        return $this->interaction->getConfig()->getCsvPathField( self::PATH_EMAIL_ALLOWED_LEVELS, null );
    }
}

<?php
namespace Hotlink\Brightpearl\Controller\Adminhtml\Authorisation;

class Returnaction extends \Hotlink\Brightpearl\Controller\Adminhtml\Authorisation\AbstractAuthorisation
{

    public function execute()
    {
        $report = $this->_initReport();
        $report
            ->setTrigger('Brightpearl redirect')
            ->setProcess('Authorisation Return')
            ->setContext('On Brightpearl return');

        $report->info('Starting Authorisation Return')->indent();

        $resultMessageMap = array(
            'SUCCESS'            => 'Authorisation completed successfully',
            'USER_REJECTED'      => 'Authorisation process has been cancelled',
            'MISSING_PARAMETER'  => 'During the authorisation process, one of the mandatory parameters was not provided',
            'INVALID_APP_REF'    => 'An invalid application reference has been supplied during the authorisation process',
            'INVALID_DEV_REF'    => 'An invalid developer reference has been supplied during the authorisation process',
            'INVALID_APP_TYPE'   => 'An invalid application type has been supplied during the authorisation process',
            'OAUTH_DISABLED'     => 'Unable to complete authorisation as OAuth style authentication has not been enabled for this app',
            'APP_UNAVAILABLE'    => 'Application not available because suspended, not released, or not available for the account\'s country',
            'ACCOUNT_RESTRICTED' => 'Trial accounts are not permitted to install applications in Brightpearl'
        );

        $result = strtoupper( trim( (string) $this->getRequest()->getParam( 'result' ) ) );

        $report->debug('result = '.$result);
        $report->addReference($result);

        if (in_array($result, array_keys($resultMessageMap))){
            if ($result == 'SUCCESS') {
                $this->messageManager->addSuccess( __( $resultMessageMap[$result] ) );
                $report->setStatus( \Hotlink\Framework\Model\Report::STATUS_SUCCESS );
                $report->info('Redirecting user to System Config');
                return $this->redirect(
                    $this->urlBuilder->getUrl('adminhtml1/system_config/edit', [ 'section' => 'hotlink_brightpearl' ]),
                    false );
            }
            else {
                $this->messageManager->addError( __( $resultMessageMap[$result] ) );
                $report->setStatus(\Hotlink\Framework\Model\Report::STATUS_ERRORS);
                $report->info('Redirecting user back to form');
                return $this->redirect( '*/*/form' );
            }
        } else {
            $this->messageManager->addError( __('An unknown error has occured during the authorisation process') );
            $report->setStatus(\Hotlink\Framework\Model\Report::STATUS_ERRORS);
            $report->info('Redirecting user back to form');
            return $this->redirect( '*/*/form' );
        }

        $report->close();

        return $this->redirect( '*/*/form' );
    }

}

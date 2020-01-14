<?php
namespace Hotlink\Brightpearl\Workaround\Magento230\Magento\Framework\App\Request\CsrfValidator;

class Plugin
{

    // ref: https://magento.stackexchange.com/questions/253414/magento-2-3-upgrade-breaks-http-post-requests-to-custom-module-endpoint
    /**
     * @param \Magento\Framework\App\Request\CsrfValidator $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ActionInterface $action
     */
    public function aroundValidate(
        $subject,
        \Closure $proceed,
        $request,
        $action
    )
    {
        if ( $request->getModuleName() == 'hotlink_brightpearl' )
            {
                return; // Skip CSRF check
            }
        $proceed( $request, $action );
    }

}

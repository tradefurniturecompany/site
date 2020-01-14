<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Controller\Adminhtml\Rule;

class MassDelete extends \Amasty\Payrestriction\Controller\Adminhtml\Rule\AbstractMassAction
{
    protected function massAction($collection)
    {
        foreach($collection as $model)
        {
            $model->delete();
        }
        $this->messageManager->addSuccess(__('Record(s) were successfully deleted'));
    }
}

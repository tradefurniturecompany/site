<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Test\Unit\Controller\Adminhtml\Link;

use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Controller\Adminhtml\Link\Save;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Amasty\MegaMenu\Test\Unit\Traits;

/**
 * Class SaveTest
 *
 * @see Save
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Save::isLinkValueNotSelect
     *
     * @dataProvider isLinkValueNotSelectDataProvider
     *
     * @throws \ReflectionException
     */
    public function testIsLinkValueNotSelect($data, $expectedResult)
    {
        $saveAction = $this->createPartialMock(Save::class, []);

        $actualResult = $saveAction->isLinkValueNotSelect($data);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Data provider for isLinkValueNotSelect test
     * @return array
     */
    public function isLinkValueNotSelectDataProvider()
    {
        return [
            [
                [
                    LinkInterface::TYPE => UrlKey::LINK,
                    LinkInterface::LINK => 'xxxx'
                ],
                false
            ],
            [
                [
                    LinkInterface::TYPE => UrlKey::LANDING_PAGE,
                    LinkInterface::PAGE_ID => 33,
                    LinkInterface::LINK => 'xxxx'
                ],
                false
            ],
            [
                [
                    LinkInterface::TYPE => UrlKey::LANDING_PAGE,
                    LinkInterface::PAGE_ID => 33
                ],
                false
            ],
            [
                [
                    LinkInterface::TYPE => UrlKey::LANDING_PAGE,
                    LinkInterface::PAGE_ID => null
                ],
                true
            ],
            [
                [
                    LinkInterface::TYPE => UrlKey::CMS_PAGE,
                    LinkInterface::PAGE_ID => null,
                    LinkInterface::LINK => 'test'
                ],
                true
            ]
        ];
    }
}

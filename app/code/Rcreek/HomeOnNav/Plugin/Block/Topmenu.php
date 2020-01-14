<?php

namespace Rcreek\HomeOnNav\Plugin\Block;

use Magento\Framework\Data\Tree\NodeFactory;

class Topmenu
{

    /**
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Data\Tree\NodeFactory $nodeFactory
     */
    public function __construct(
        NodeFactory $nodeFactory
    )
    {
        $this->nodeFactory = $nodeFactory;
    }

    /**
     *
     * Inject node into menu.
     **/
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    )
    {
        $node = $this->nodeFactory->create(
            [
                'data' => $this->getNodeAsArray(),
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );

        $children = $subject->getMenu()->getChildren();
        $originalChildren = clone($children);

        foreach ($children as $child) {
            $subject->getMenu()->removeChild($child);
        }

        $subject->getMenu()->addChild($node);

        foreach ($originalChildren as $child) {
            $subject->getMenu()->addChild($child);
        }
    }

    /**
     *
     * Build node
     **/
    protected function getNodeAsArray()
    {
        return [
            'name' => '',
            'id' => 'home',
            'class' => 'nav-home',
            'url' => '/',
            'has_active' => false,
            'is_active' => false
        ];
    }
}
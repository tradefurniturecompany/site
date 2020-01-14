<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AnimationsIn implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
                'label' => __('None'),
                'value' => ''
                ];
        $options[] = [
                'label' => __('Attention Seekers'),
                'value' => [
                        [
                            'label' => __('bounce'),
                            'value' => 'bounce',
                    ],
                    [
                            'label' => __('flash'),
                            'value' => 'flash',
                    ],
                    [
                            'label' => __('pulse'),
                            'value' => 'pulse',
                    ],
                    [
                            'label' => __('rubberBand'),
                            'value' => 'rubberBand',
                    ],
                    [
                            'label' => __('shake'),
                            'value' => 'shake',
                    ],
                    [
                            'label' => __('swing'),
                            'value' => 'swing',
                    ],
                    [
                            'label' => __('tada'),
                            'value' => 'tada',
                    ],
                    [
                            'label' => __('wobble'),
                            'value' => 'wobble',
                    ],
                    [
                            'label' => __('jello'),
                            'value' => 'jello',
                    ]
                ]
            ];
            $options[] = [
                'label' => __('Bouncing Entrances'),
                'value' => [
                        [
                            'label' => __('bounceIn'),
                            'value' => 'bounceIn',
                    ],
                    [
                            'label' => __('bounceInDown'),
                            'value' => 'bounceInDown',
                    ],
                    [
                            'label' => __('bounceInLeft'),
                            'value' => 'bounceInLeft',
                    ],
                    [
                            'label' => __('bounceInRight'),
                            'value' => 'bounceInRight',
                    ],
                    [
                            'label' => __('bounceInUp'),
                            'value' => 'bounceInUp',
                    ]
                ]
            ];
            $options[] = [
                'label' => __('Fading Entrances'),
                'value' => [
                        [
                            'label' => __('fadeIn'),
                            'value' => 'fadeIn',
                    ],
                    [
                            'label' => __('fadeInDown'),
                            'value' => 'fadeInDown',
                    ],
                    [
                            'label' => __('fadeInDownBig'),
                            'value' => 'fadeInDownBig',
                    ],
                    [
                            'label' => __('fadeInLeft'),
                            'value' => 'fadeInLeft',
                    ],
                    [
                            'label' => __('fadeInLeftBig'),
                            'value' => 'fadeInLeftBig',
                    ],
                    [
                            'label' => __('fadeInRight'),
                            'value' => 'fadeInRight',
                    ]
                    ,
                    [
                            'label' => __('fadeInRightBig'),
                            'value' => 'fadeInRightBig',
                    ]
                    ,
                    [
                            'label' => __('fadeInUp'),
                            'value' => 'fadeInUp',
                    ]
                    ,
                    [
                            'label' => __('fadeInUpBig'),
                            'value' => 'fadeInUpBig',
                    ]
                ]
            ];
            $options[] = [
                'label' => __('Flippers'),
                'value' => [
                        [
                            'label' => __('flip'),
                            'value' => 'flip',
                    ],
                    [
                            'label' => __('flipInX'),
                            'value' => 'flipInX',
                    ],
                    [
                            'label' => __('flipInY'),
                            'value' => 'flipInY',
                    ]
                ]
            ];
            $options[] = [
                'label' => __('Lightspeed'),
                'value' => [
                        [
                            'label' => __('lightSpeedIn'),
                            'value' => 'lightSpeedIn',
                    ]
                ]
            ];
            $options[] = [
                'label' => __('Rotating Entrances'),
                'value' => [
                        [
                            'label' => __('rotateIn'),
                            'value' => 'rotateIn',
                    ],
                    [
                            'label' => __('rotateInDownLeft'),
                            'value' => 'rotateInDownLeft',
                    ],
                    [
                            'label' => __('rotateInDownRight'),
                            'value' => 'rotateInDownRight',
                    ],
                    [
                            'label' => __('rotateInUpLeft'),
                            'value' => 'rotateInUpLeft',
                    ],
                    [
                            'label' => __('rotateInUpRight'),
                            'value' => 'rotateInUpRight',
                    ]
                ]
            ];
            $options[] = [
                'label' => __('Sliding Entrances'),
                'value' => [
                        [
                            'label' => __('slideInUp'),
                            'value' => 'slideInUp',
                    ],
                    [
                            'label' => __('slideInDown'),
                            'value' => 'slideInDown',
                    ],
                    [
                            'label' => __('slideInLeft'),
                            'value' => 'slideInLeft',
                    ],
                    [
                            'label' => __('slideInRight'),
                            'value' => 'slideInRight',
                    ]
                ]
            ];
            $options[] = [
                'label' => __('Zoom Entrances'),
                'value' => [
                        [
                            'label' => __('zoomIn'),
                            'value' => 'zoomIn',
                    ],
                    [
                            'label' => __('zoomInDown'),
                            'value' => 'zoomInDown',
                    ],
                    [
                            'label' => __('zoomInLeft'),
                            'value' => 'zoomInLeft',
                    ],
                    [
                            'label' => __('zoomInRight'),
                            'value' => 'zoomInRight',
                    ],
                    [
                            'label' => __('zoomInUp'),
                            'value' => 'zoomInUp',
                    ]
                ]
            ];
            $options[] = [
                'label' => __('Specials'),
                'value' => [
                    [
                            'label' => __('rollIn'),
                            'value' => 'rollIn',
                    ]
                ]
            ];
        return $options;
    }
}

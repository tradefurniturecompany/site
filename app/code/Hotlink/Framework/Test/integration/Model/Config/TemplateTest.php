<?php
namespace Hotlink\Framework\Test\integration\Model\Config;

/**
 *  @group  config
 */

class TemplateTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        $this->manager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    public function test_merge()
    {

        $templateFields = [ 'arr1' => [ 'id'             => 1,
                                        'name'           => 'onion',
                                        'order'          => 10,
                                        'implementation' => 'soup',
                                        'boolean'        => true,
                                        'path'           => 'template/arr1',
                                        'nest1'          => [ 'id'   => 99,
                                                              'name' =>  'shallott' ],
                                        'nest2'          => [ 'id'   => 'NaN',
                                                              'blur' => 'discovery' ]
        ],
                            'arr2' => [ 'id'             => 2,
                                        'name'           => 'carrot',
                                        'order'          => 20,
                                        'implementation' => 'crudite',
                                        'path'           => 'template/arr2',
                                        'boolean'        => false ],
                            'arr3' => [ 'id'             => 3,
                                        'name'           => 'tomatoe',
                                        'order'          => 30,
                                        'implementation' => 'salad',
                                        'path'           => 'template/arr3',
                                        'boolean'        => false ]
        ];

        $configFields = [ 'arr1' => [ 'id'             => 11,
                                      'name'           => 'onions',
                                      'implementation' => 'gravy',
                                      'path'           => 'config/arr1',
                                      'nest1'          => [ 'id'   => 901,
                                                            'name' => 'just say no' ]
        ],
                          'arr3' => [ 'boolean'        => true,
                                      'path'           => 'config/arr3' ],
                          'arr4' => [ 'id'             => 4,
                                      'name'           => 'zinger',
                                      'implementation' => 'burger',
                                      'path'           => 'config/arr4' ]
        ];

        $template = $this->getTestObject();
        $result = $template->merge( $templateFields, $configFields );

        $this->assertEquals( $result[ 'arr1' ][ 'id' ], 11 );
        $this->assertEquals( $result[ 'arr2' ][ 'id' ], 2 );
        $this->assertEquals( $result[ 'arr3' ][ 'id' ], 3 );
        $this->assertEquals( $result[ 'arr4' ][ 'id' ], 4 );

        $this->assertEquals( $result[ 'arr1' ][ 'name' ], 'onions' );
        $this->assertEquals( $result[ 'arr2' ][ 'name' ], 'carrot' );
        $this->assertEquals( $result[ 'arr3' ][ 'name' ], 'tomatoe' );
        $this->assertEquals( $result[ 'arr4' ][ 'name' ], 'zinger' );

        $this->assertEquals( $result[ 'arr1' ][ 'order' ], 10 );
        $this->assertEquals( $result[ 'arr2' ][ 'order' ], 20 );
        $this->assertEquals( $result[ 'arr3' ][ 'order' ], 30 );
        $this->assertFalse( array_key_exists( 'order', $result[ 'arr4' ] ) );

        $this->assertEquals( $result[ 'arr1' ][ 'implementation' ], 'gravy' );
        $this->assertEquals( $result[ 'arr2' ][ 'implementation' ], 'crudite' );
        $this->assertEquals( $result[ 'arr3' ][ 'implementation' ], 'salad' );
        $this->assertEquals( $result[ 'arr4' ][ 'implementation' ], 'burger' );

        $this->assertEquals( $result[ 'arr1' ][ 'boolean' ], true );
        $this->assertEquals( $result[ 'arr2' ][ 'boolean' ], false );
        $this->assertEquals( $result[ 'arr3' ][ 'boolean' ], true );
        $this->assertFalse( array_key_exists( 'boolean', $result[ 'arr4' ] ) );

        $this->assertEquals( $result[ 'arr1' ][ 'nest1' ][ 'id' ], 901 );
        $this->assertEquals( $result[ 'arr1' ][ 'nest2' ][ 'id' ], 'NaN' );
        $this->assertEquals( $result[ 'arr1' ][ 'nest1' ][ 'name' ], 'just say no' );
        $this->assertEquals( $result[ 'arr1' ][ 'nest2' ][ 'blur' ], 'discovery' );

        $this->assertEquals( $result[ 'arr1' ][ 'path' ], 'config/arr1' );
        $this->assertEquals( $result[ 'arr2' ][ 'path' ], 'template/arr2' );
        $this->assertEquals( $result[ 'arr3' ][ 'path' ], 'config/arr3' );
        $this->assertEquals( $result[ 'arr4' ][ 'path' ], 'config/arr4' );

    }

    public function getTestObject()
    {
        $template = $this->manager->create( '\Hotlink\Framework\Model\Config\Template' );
        return $template;
    }

}
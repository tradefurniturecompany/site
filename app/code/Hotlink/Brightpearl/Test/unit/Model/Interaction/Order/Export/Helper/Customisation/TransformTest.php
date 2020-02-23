<?php
namespace Hotlink\Brightpearl\Test\unit\Model\Interaction\Order\Export\Helper\Customisation;

/**
 *
 *  @group  order
 *  @group  interaction
 *  @group  customisation
 *
 */

class TransformTest extends \PHPUnit\Framework\TestCase
{

    function test_clean()
    {
        $transform = $this->getTransformer();
        $this->assertEquals( 'abc', $transform->clean( ' abc  ' ), "' abc  '" );
        $this->assertEquals( '1 23', $transform->clean( '   1 23    ' ), "'   1 23    '" );
    }

    function test_is_date_format()
    {
        $transform = $this->getTransformer();
        $this->assertTrue( $transform->isDateFormat( '[abc]' ),       "'[abc]'" );
        $this->assertTrue( $transform->isDateFormat( '[123]' ),       "'[123]'" );
        $this->assertTrue( $transform->isDateFormat( '[ab[cx]yx]' ),  "'[ab[cx]yx]'" );
        $this->assertTrue( $transform->isDateFormat( '   [ abc ] ' ), "'   [ abc ] '" );
        $this->assertTrue( $transform->isDateFormat( '[]' ),          "'[]'" );
        $this->assertFalse( $transform->isDateFormat( 'abc' ),        "'abc'" );
        $this->assertFalse( $transform->isDateFormat( '[abc' ),       "'[abc'" );
        $this->assertFalse( $transform->isDateFormat( 'abc]' ),       "'abc]'" );
        $this->assertFalse( $transform->isDateFormat( 'a[b]c' ),      "'a[b]c'" );
        $this->assertFalse( $transform->isDateFormat( '{abc}' ),      "'{abc}'" );
        $this->assertFalse( $transform->isDateFormat( '(abc)' ),      "'(abc)'" );
        $this->assertFalse( $transform->isDateFormat( '   ' ),        "'   '" );
        $this->assertFalse( $transform->isDateFormat( '' ),           "''" );
    }

    function test_get_date_format()
    {
        $transform = $this->getTransformer();
        $this->assertEquals( 'abc', $transform->getDateFormat( '[abc]' ) );
        $this->assertEquals( '123', $transform->getDateFormat( '[123]' ) );
        $this->assertEquals( 'ab[cx]yx', $transform->getDateFormat( '[ab[cx]yx]' ) );
        $this->assertEquals( ' abc ', $transform->getDateFormat( '   [ abc ] ' ) );
        $this->assertEquals( '', $transform->getDateFormat( '[]' ) );
        $this->assertEquals( false, $transform->getDateFormat( 'abc' ) );
        $this->assertEquals( false, $transform->getDateFormat( '{abc}' ) );
        $this->assertEquals( false, $transform->getDateFormat( '(abc)' ) );
        $this->assertEquals( false, $transform->getDateFormat( '' ) );
    }

    function test_is_lookup()
    {
        $transform = $this->getTransformer();
        $this->assertTrue( $transform->isLookup( '{a=1,b=2,c=3}' ), "'{a=1,b=2,c=3}'" );
        $this->assertTrue( $transform->isLookup( '{123}' ),         "'{123}'" );
        $this->assertTrue( $transform->isLookup( '{ab{cx}yx}' ),    "'{ab{cx}yx}'" );
        $this->assertTrue( $transform->isLookup( '   { abc } ' ),   "'   { abc } '" );
        $this->assertTrue( $transform->isLookup( '{}' ),            "'{}'" );
        $this->assertFalse( $transform->isLookup( 'abc' ),          "'abc'" );
        $this->assertFalse( $transform->isLookup( '{abc' ),         "'{abc'" );
        $this->assertFalse( $transform->isLookup( 'abc}' ),         "'abc}'" );
        $this->assertFalse( $transform->isLookup( 'a{b}c' ),        "'a{b}c'" );
        $this->assertFalse( $transform->isLookup( '[abc]' ),        "'[abc]'" );
        $this->assertFalse( $transform->isLookup( '(abc)' ),        "'(abc)'" );
        $this->assertFalse( $transform->isLookup( '   ' ),          "'   '" );
        $this->assertFalse( $transform->isLookup( '' ),             "''" );
    }

    function test_get_lookup_success()
    {
        $transform = $this->getTransformer();
        $expression = '{a=1,b=2,3=c, 99  = donkey Kong   , !42  != what a mess,z=, "17, please = nice, to meet you", b=100 }';
        $result = $transform->getLookup( $expression );
        $this->assertEquals( 7, count( $result ), "count = " . count( $result ) );

        // basic assignment : a=1
        reset( $result );
        $this->assertEquals( 'a', key( $result ), "key = a" );
        $this->assertEquals( 1, $result[ 'a' ],   "val[a] = 1" );

        // reassignment overwrites previous value : b=2 ... b=100
        next( $result );
        $this->assertEquals( 'b', key( $result ), "key = b" );
        $this->assertEquals( 100, $result[ 'b' ], "val[b] = 100" );

        // numeric indices supported : 3=c
        next( $result );
        $this->assertEquals(  3 , key( $result ), "key = 3" );
        $this->assertEquals( 'c', $result[ '3' ],  "arr[3] = c" );

        // whitespace removed : ' 99  = donkey Kong   '
        next( $result );
        $this->assertEquals( '99'         , key( $result ) , "key = '99'" );
        $this->assertEquals( 'donkey Kong', $result[ '99' ], "arr['99'] = 'donkey Kong'" );

        // unusual characters ok : ' !42  != what a mess'
        next( $result );
        $this->assertEquals( '!42  !'     , key( $result )      , "key = '!42  !'" );
        $this->assertEquals( 'what a mess', $result[ '!42  !' ], "arr['!42  !'] = 'what a mess'" );

        // empty strings are default : z=
        next( $result );
        $this->assertEquals( 'z', key( $result ), "key = 'z'" );
        $this->assertEquals( '',  $result[ 'z' ], "arr['z'] = ''" );

        // commas handled within quotes : "17, please = nice, to meet you"
        next( $result );
        $this->assertEquals( '17, please'       , key( $result )         , "key = '17, please'" );
        $this->assertEquals( 'nice, to meet you', $result[ '17, please' ], "arr['17, please'] = 'nice, to meet you'" );

    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     *  @expectedExceptionMessage Unable to create list item from string
     */
    function test_get_lookup_invalid_empty()
    {
        $transform = $this->getTransformer();
        $transform->getLookup( '{}' );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     *  @expectedExceptionMessage Unable to create list item from string
     */
    function test_get_lookup_invalid_no_key()
    {
        $transform = $this->getTransformer();
        $transform->getLookup( '{a=1,}' );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     *  @expectedExceptionMessage Unable to create list item from string
     */
    function test_get_lookup_invalid_no_val()
    {
        $transform = $this->getTransformer();
        $transform->getLookup( '{a=1,b}' );
    }

    function test_apply_lookup_success()
    {
        $transform = $this->getTransformer();
        $expression = '{hot=dog,mouse=mat,"king=kong"}';
        $this->assertEquals( 'dog', $transform->apply( $expression, 'hot' ), 'hot=dog' );
        $this->assertEquals( 'mat', $transform->apply( $expression, 'mouse' ), 'mouse=mat' );
        $this->assertEquals( 'kong', $transform->apply( $expression, 'king' ), 'king=kong' );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     *  @expectedExceptionMessage No lookup key defined for
     */
    function test_apply_lookup_invalid_key()
    {
        $transform = $this->getTransformer();
        $transform->apply( '{hot=dog,mouse=mat,"king=kong"}', 'billboard' );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     *  @expectedExceptionMessage Cannot perform lookup when value is an object
     */
    function test_apply_lookup_invalid_val()
    {
        $transform = $this->getTransformer();
        $object = new \stdClass();
        $transform->apply( '{hot=dog,mouse=mat,"king=kong"}', $object );
    }

    function test_apply_sprintf_success()
    {
        $transform = $this->getTransformer();
        $this->assertEquals( 'err : 99', $transform->apply( 'err : %s', 99 ) );
        $this->assertEquals( '99', $transform->apply( '%d', 99 ) );
        $this->assertEquals( '0005', $transform->apply( '%04d', 5 ) );
        $this->assertEquals( '(    monkey)', $transform->apply( '(%10s)', 'monkey' ) );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     */
    function test_apply_sprintf_fail()
    {
        $transform = $this->getTransformer();
        $transform->apply( '%2$05d', 99 );   // Parameters (2$) are not supported
    }

    function test_apply_date_success()
    {
        $transform = $this->getTransformer();

        // support string dates
        $this->assertEquals( '2017-06-20', $transform->apply( '[Y-m-d]', '20 June 2017 18:31:17' ) );

        // support integer dates
        $time = strtotime( '20 June 2017 18:31:17' );
        $this->assertEquals( '2017-06-20', $transform->apply( '[Y-m-d]', $time ) );
    }

    protected function getTransformer()
    {
        return new \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation\Transform();
    }

}

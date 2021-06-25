<?php
namespace Hotlink\Framework\Test;

class BashPrinter extends \PHPUnit\Util\Printer implements \PHPUnit\Framework\TestListener
{

    const INDENT = 3;
    const START_LEVEL = 1;
    const DOTS = '...';

    const COL_TEST = 70;
    const COL_MESSAGE = 100;
    const COL_EXCEPTION = 180;

    const COLUMN_FILL = ' ';
    // const COLUMN_FILL = '.';    // use for debugging

    protected $suiteNesting = 0;
    protected $level = self::START_LEVEL;
    protected $styles = array( 'red'         => "[0;31m",   'light_red'     => "[1;31m",
                               'green'       => "[0;32m",   'light_green'   => "[1;32m",
                               'blue'        => "[0;34m",   'light_blue'    => "[1;34m",
                               'grey'        => "[1;30m",   'light_grey'    => "[0;37m",
                               'cyan'        => "[0;36m",   'light_cyan'    => "[1;36m",
                               'magenta'     => "[0;35m",   'light_magenta' => "[1;35m",

                               'yellow'      => "[1;33m",
                               'brown'       => "[0;33m",

                               'white'       => "[1;37m",
                               'black'       => "[0;30m",

                               'normal'      => "[0m",      'bold'        => "[1m",
                               'underscore'  => "[4m",      'reverse'     => "[7m" );

    protected $icons = array( 'short' => array( 'none'       => '     ',
                                                'running'    => '[ ? ]',
                                                'pass'       => '[ + ]',
                                                'error'      => '[ E ]',
                                                'warning'    => '[ W ]',
                                                'fail'       => '[ F ]',
                                                'incomplete' => '[ i ]',
                                                'risky'      => '[ r ]',
                                                'skipped'    => '[ - ]' ),
                              'long'  => array( 'none'       => '      ',
                                                'running'    => ' ---> ',
                                                'pass'       => ' pass ',
                                                'warning'    => ' wrn! ',
                                                'error'      => ' err! ',
                                                'fail'       => ' fail ',
                                                'incomplete' => ' !fin ',
                                                'risky'      => ' !rsk ',
                                                'skipped'    => ' skip ' ),
                              'style' => array( 'none'       => '',
                                                'running'    => '',
                                                'pass'       => 'light_green,reverse',
                                                'warning'    => 'brown,reverse',
                                                'error'      => 'light_red,reverse',
                                                'fail'       => 'light_magenta,reverse',
                                                'incomplete' => 'light_blue',
                                                'risky'      => 'brown',
                                                'skipped'    => 'grey' ) );

    protected $iconLookup = 'long';
    protected $testPassed = true;
    protected $columnWidthMax = null;
    protected $columnWidthTest = null;
    protected $columnWidthMessage = null;
    protected $columnException = null;

    protected function getColumnWidthMax()
    {
        if ( is_null( $this->columnWidthMax ) )
            {
                $this->columnWidthMax = exec( 'tput cols' ) - 3;
                if ( $this->columnWidthMax <= 0 )
                    {
                        $this->columnWidthMax = 120;
                    }
            }
        return $this->columnWidthMax;
    }

    protected function style( $text, $style="normal" )
    {
        $out = $this->styles[ "$style" ];
        $ech = chr(27) . "$out" . "$text" . chr(27) . "[0m";
        return $ech;
    }

    protected function applyStyling( $text, $styling )
    {
        $styles = explode( ',', $styling );
        foreach ( $styles as $style )
            {
                $text = $this->style( $text, $style );
            }
        return $text;
    }

    protected function indent()
    {
        $this->level++;
        return $this;
    }

    protected function unindent()
    {
        $this->level--;
        return $this;
    }

    protected function getLevel()
    {
        return $this->level;
    }

    protected function fixedWidth( $text, $width, $suffix )
    {
        $len = strlen( $text );
        $end = strlen( $suffix );
        if ( $len > $width - $end )
            {
                $text = substr( $text, 0, $width - $end ) . $suffix;
            }
        else
            {
                $text = str_pad( $text, $width, self::COLUMN_FILL );
            }
        return $text;
    }

    protected function getIndent()
    {
        $indentPadding = str_pad( '', $this->level * self::INDENT, ' ', STR_PAD_LEFT );
        return $indentPadding;
    }

    protected function writeException( $exception )
    {
        $this->indent();
        $indent = $this->getIndent();
        $widthIndent = strlen( $indent );
        $columnIndent = $this->newColumn( $widthIndent, $indent, '' );

        $widthMessage = $this->getAvailableWidth( [ $columnIndent ] );

        $messages = explode( "\n", ( string ) $exception );
        $this->write( "\n" );
        foreach ( $messages as $message )
            {
                $lines = str_split( $message, $widthMessage );
                foreach ( $lines as $line )
                    {
                        $columns = [ $columnIndent, $this->newColumn( $widthMessage, $line, '' ) ];
                        $text = $this->getRowString( $columns );
                        $this->write( "\r" . $text  . "\n" );
                    }
            }
        $this->write( "\n" );
        $this->unindent();
    }

    protected function writeTestSuite( $name )
    {
        $indent = $this->getIndent();
        $columns = array();
        $columns[] = $this->newColumn( strlen( $indent ), $indent, '' );
        $columns[] = $this->newColumn( $this->getAvailableWidth( $columns ), $name, '...' );
        $text = $this->getRowString( $columns );
        $this->write( "\r" . $text . "\n" );
        $this->write( "\r\n" );
    }

    protected function writeTest( $status, $name, $message = '', $newline = true )
    {
        $styling = $this->getStyling( $status );
        $icon = $this->getIcon( $status );
        $indent = $this->getIndent();
        $columns = array();
        $columns[] = $this->newColumn( strlen( $indent ), $indent, '' );
        $columns[] = $this->newColumn( strlen( $icon ) + 2, $icon, '  ' );
        $widthAvailable = $this->getAvailableWidth( $columns );
        $widthName = ( int ) (  0.5 * $widthAvailable );
        $widthMessage = $widthAvailable - $widthName;

        $columns[] = $this->newColumn( $widthName, $name );
        $columns[] = $this->newColumn( $widthMessage, $message );

        $text = $this->getRowString( $columns );

        if ( $icon && $styling )
            {
                // Apply styling after padding so that columns width are maintained
                $styled = $this->applyStyling( $icon, $styling );
                $text = str_replace( $icon, $styled, $text );
            }
        if ( $newline )
            {
                $this->write( "\r" . $text . "\n" );
            }
        else
            {
                $this->write( "\r" . $text );
            }
    }

    protected function summarise( $test )
    {
        if ( $test instanceof \PHPUnit\Framework\TestSuite )
            {
                foreach ( $test->tests() as $child )
                    {
                        $this->summarise( $child );
                    }
            }
        $result = $test->getResult();  // always null
    }

    //
    //  Interface \PHPUnit\Framework\TestListener
    //
    function addWarning( \PHPUnit\Framework\Test $test, \PHPUnit\Framework\Warning $e, $time )
    {
        $this->testPassed = false;
        $this->writeTest( 'warning', $test->getName() );
        $this->writeException( $e );
    }

    function addError(\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
        $this->testPassed = false;
        $this->writeTest( 'error', $test->getName() );
        $this->writeException( $e );
    }

    function addFailure(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\AssertionFailedError $e, $time)
    {
        $this->testPassed = false;
        $this->writeTest( 'fail', $test->getName(), $e->getMessage() );
    }

    function addIncompleteTest(\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
        $this->testPassed = false;
        $this->writeTest( 'incomplete', $test->getName(), $e->getMessage() );
    }

    function addRiskyTest(\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
        $this->testPassed = false;
        $this->writeTest( 'risky', $test->getName(), $e->getMessage() );
    }

    function addSkippedTest(\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
        $this->testPassed = false;
        $this->writeTest( 'skipped', $test->getName(), $e->getMessage() );
    }

    function startTestSuite(\PHPUnit\Framework\TestSuite $suite)
    {
        $this->suiteNesting++;
        $name = $suite->getName();
        if ( $this->suiteNesting > 1 )
            {
                $parts = explode( 'tests\\', $name );
                if ( count( $parts ) > 1 )
                    {
                        $name = $parts[ 1 ];
                    }
            }
        $this->writeTestSuite( $name );
        $this->indent();
    }

    function endTestSuite(\PHPUnit\Framework\TestSuite $suite)
    {
        $this->unindent();
        if ( $this->getLevel() == self::START_LEVEL )
            {
                //$this->summarise( $suite );
            }
        $this->suiteNesting--;
        $this->write( "\r\n" );
    }

    function startTest(\PHPUnit\Framework\Test $test)
    {
        $this->testPassed = true;
        $this->writeTest( 'running', $test->getName(), '.... executing ....', false );
    }

    function endTest(\PHPUnit\Framework\Test $test, $time)
    {
        if ( $this->testPassed )
            {
                $this->writeTest( 'pass', $test->getName(), '', true );
            }
    }

    protected function newColumn( $width, $message, $suffix = '...' )
    {
        return array( 'width'   => $width,
                      'message' => $message,
                      'suffix'  => $suffix );
    }

    protected function getWidth( $columns )
    {
        $width = 0;
        foreach ( $columns as $column )
            {
                $width += $column[ 'width' ];
            }
        return $width;
    }

    protected function getAvailableWidth( $columns )
    {
        return $this->getColumnWidthMax() - $this->getWidth( $columns );
    }

   protected function getRowString( $columns )
    {
        $row = '';
        foreach ( $columns as $column )
            {
                $width   = $column[ 'width' ];
                $message = $column[ 'message' ];
                $suffix  = $column[ 'suffix' ];
                $text = $this->fixedWidth( $message, $width, $suffix );
                $row .= $text;
            }
        return $row;
    }

    protected function getStyling( $status )
    {
        return isset( $this->icons[ 'style' ][ $status ] ) ? $this->icons[ 'style' ][ $status ] : false;
    }

    protected function getIcon( $status )
    {
        return isset( $this->icons[ $this->iconLookup ][ $status ] ) ? $this->icons[ $this->iconLookup ][ $status ] : false;
    }

}

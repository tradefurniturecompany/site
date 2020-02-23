<?php
namespace Hotlink\Framework\Helper\Convention;

class Interaction extends \Hotlink\Framework\Helper\Convention\AbstractConvention
{

    //
    //  The convention is to infer a block class name from an interaction class name as follows:
    //    Given the interaction  :    vendor\module\name\some\path\      interaction\          does\something\export
    //    The block should be    :    vendor\module\name\block\adminhtml\interaction\admin\tab\does\something\export
    //
    //  You can overload to provide explict block names if you wish to break from convention.
    //  Returning false indicates the interaction does not support an admin tab.
    //
    //  \Hotlink\Framework\Interaction\Log\Cleaning
    //  \Hotlink\Framework\Block\Adminhtml\Interaction\Admin\Tab\Log\Cleaning
    //
    function getTabBlock( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    {
        $parts = array_reverse( explode( "\\", get_class( $interaction ) ) );
        $index = 0;
        $found = false;
        while ( !$found && ( $index < count( $parts ) ) )
            {
                if ( strtolower( $parts[ $index ] ) == 'interaction' )
                    {
                        $found = $index;
                        break;
                    }
                $index++;
            }
        $before = array_splice( 0, $index );
        $after = array_splice( $parts, $index );

        $module = $parts[ 0 ];
        $model = $parts[ 1 ];

        $paths = explode( '_', $model );
        $found = false;
        while ( !$found && ( count( $paths ) > 0 ) )
            {
                $found = ( $paths[ 0 ] == 'interaction' );
                array_shift( $paths );
            }
        if ( $found )
            {
                $path = implode( '_', $paths );
                $path = 'adminhtml_interaction_index_tab_' . $path;
                $name = $module . '/' . $path;
                if ( !$this->interactionConventionCheckHelper->blockExists( $name ) )
                    {
                        $name = 'hotlink_framework/adminhtml_interaction_index_tab_default';
                    }
                return $name;
            }
        return FALSE;
    }

    function getConfigClass( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    {
        return $this->_getValidClass( $interaction, [ 'Config' ], '\Hotlink\Framework\Model\Interaction\Config\DefaultConfig' );
    }

    function getEnvironmentClass( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction )
    {
        return $this->_getValidClass( $interaction, [ 'Environment' ], '\Hotlink\Framework\Model\Interaction\Environment\DefaultEnvironment' );
    }

    function getImplementationClass( $interaction )
    {
        return $this->_getValidClass( $interaction, [ 'Implementation' ], false );
    }

    // function getModel( \Hotlink\Framework\Model\Interaction\Environment\AbstractEnvironment $environment )
    // { throw new Execption ( "obsolete" );
    //     $name = $this->interactionReflectionHelper->getModelName( $environment );
    //     $parts = explode( "_", $name );
    //     array_pop( $parts );
    //     $name = implode( "_", $parts );
    //     return $name;
    // }

    // function getTabBlock( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interction )
    // {
    //     $parts = explode( '/', $interction->getModel() );
    //     $module = $parts[ 0 ];
    //     $model = $parts[ 1 ];

    //     $paths = explode( '_', $model );
    //     $found = false;
    //     while ( !$found && ( count( $paths ) > 0 ) )
    //         {
    //             $found = ( $paths[ 0 ] == 'interaction' );
    //             array_shift( $paths );
    //         }
    //     if ( $found )
    //         {
    //             $path = implode( '_', $paths );
    //             $path = 'adminhtml_interaction_index_tab_' . $path;
    //             $name = $module . '/' . $path;
    //             if ( !$this->interactionConventionCheckHelper->blockExists( $name ) )
    //                 {
    //                     $name = 'hotlink_framework/adminhtml_interaction_index_tab_default';
    //                 }
    //             return $name;
    //         }
    //     return FALSE;
    // }

}

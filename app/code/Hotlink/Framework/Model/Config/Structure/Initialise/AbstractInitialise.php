<?php
namespace Hotlink\Framework\Model\Config\Structure\Initialise;

class AbstractInitialise
{

    function extract( $data, $path, $missing = false )
    {
        $parts = explode( '/', $path );
        foreach ( $parts as $part )
            {
                if ( isset( $data[ $part ] ) )
                    {
                        $data = $data[ $part ];
                    }
                else
                    {
                        return $missing;
                    }
            }
        return $data;
    }

    function setReferences( $fields, $sectionName, $groupName )
    {
        foreach ( $fields as $name => $field )
            {
                $fields[ $name ][ 'path' ] = $sectionName . '/' . $groupName;
                if ( isset( $field[ 'depends' ][ 'fields' ] ) )
                    {
                        foreach ( $field[ 'depends' ][ 'fields' ] as $ref => $properties )
                            {
                                $fields[ $name ][ 'depends' ][ 'fields' ][ $ref ][ 'id' ] = $sectionName . '/' . $groupName . '/' . $ref;
                                $fields[ $name ][ 'depends' ][ 'fields' ][ $ref ][ 'dependPath' ][ 0 ] = $sectionName;
                                $fields[ $name ][ 'depends' ][ 'fields' ][ $ref ][ 'dependPath' ][ 1 ] = $groupName;
                            }
                    }
            }
        return $fields;
    }

    function merge( $submissive, $dominant )
    {
        $result = array_replace_recursive( $submissive, $dominant );
        return $result;
    }

    protected function error( $message )
    {
        throw new \Magento\Framework\Exception\LocalizedException( $message );
    }

}

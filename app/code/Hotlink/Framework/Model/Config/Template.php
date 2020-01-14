<?php
namespace Hotlink\Framework\Model\Config;

class Template
{

    const DEFAULT_SORT_ORDER_CONTEXT = 2010;

    protected $factory;
    protected $reflection;
    protected $html;
    protected $keyGroupHelper;
    protected $map;
    protected $reader;
    protected $scopeDefiner;

    protected $_template = null;

    public function __construct(
        \Hotlink\Framework\Helper\Factory $factory,
        \Hotlink\Framework\Helper\Reflection $reflection,
        \Hotlink\Framework\Helper\Html $html,
        \Hotlink\Framework\Helper\Config\Interaction\Group\Key $keyGroupHelper,
        \Hotlink\Framework\Model\Config\Map $map,
        \Hotlink\Framework\Model\Config\Template\Reader $reader,
        \Magento\Config\Model\Config\ScopeDefiner $scopeDefiner
    )
    {
        $this->factory = $factory;
        $this->reflection = $reflection;
        $this->html = $html;
        $this->keyGroupHelper = $keyGroupHelper;
        $this->map = $map;
        $this->reader = $reader;
        $this->scopeDefiner = $scopeDefiner;
    }

    public function getFactory()
    {
        return $this->factory;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function apply( $config )
    {
        if ( $sections = $this->extract( $config, 'config/system/sections' ) )
            {
                foreach ( $sections as $sectionName => $sectionData )
                    {
                        if ( $groups = $this->extract( $sectionData, 'children' ) )
                            {
                                foreach ( $groups as $groupName => $groupData )
                                    {
                                        if ( $this->extract( $groupData, 'hotlink/interaction' ) )
                                            {
                                                $class = $this->keyGroupHelper->decode( $groupName );

                                                // Interactions are treated as singletons during config rendering to avoid multiple
                                                // instantiations, however this is only a minor optimisation that runs contrary to normal
                                                // interaction instance behaviour.

                                                $interaction = $this->getFactory()->singleton( $class );

                                                $configFields = $this->extract( $groupData, 'children', [] );

                                                $templateFields = [];
                                                $templateFields = $templateFields + $this->getTemplateFieldsHeadings( $interaction );
                                                $templateFields = $templateFields + $this->getTemplateFieldsImplementations( $interaction );
                                                $templateFields = $templateFields + $this->getTemplateFieldsActions( $interaction );
                                                $templateFields = $templateFields + $this->getTemplateFieldsTriggers( $interaction );
                                                $templateFields = $templateFields + $this->getTemplateFieldsMonitors( $interaction );

                                                $result = $this->merge( $templateFields, $configFields );
                                                $result = $this->setReferences( $result, $sectionName, $groupName );

                                                uasort( $result, [ $this, 'compare' ] );

                                                $config[ 'config' ][ 'system' ]
                                                    [ 'sections' ][ $sectionName ][ 'children' ]
                                                    [ $groupName ][ 'children' ] = $result;
                                            }
                                    }
                            }
                    }
            }
        return $config;
    }

    public function compare( $a, $b )
    {
        $orderA = ( int ) $this->extract( $a, 'sortOrder', 99999 );
        $orderB = ( int ) $this->extract( $b, 'sortOrder', 99999 );
        return $orderA - $orderB;
    }

    public function extract( $data, $path, $missing = false )
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

    public function setReferences( $fields, $sectionName, $groupName )
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

    public function merge( $submissive, $dominant )
    {
        $result = array_replace_recursive( $submissive, $dominant );
        return $result;
    }

    public function getTemplate()
    {
        if ( is_null( $this->_template ) )
            {
                $this->_template = $this->reader->read();
            }
        return $this->_template;
    }

    public function getTemplateFields( $path )
    {
        return $this->extract( $this->getTemplate(), $path, [] );
    }

    public function getTemplateFieldsHeadings( $interaction )
    {
        return $this->getTemplateFields( 'config/system/sections/template/children/interaction/children' );
    }

    public function getTemplateFieldsImplementations( $interaction )
    {
        $result = [];
        $implementations = $this->getMap()->getImplementations( $interaction );
        if ( count ( $implementations ) > 1 )
            {
                $result = $this->getTemplateFields( 'config/system/sections/template/children/implementations/children' );
                if ( isset( $result[ 'implementation' ] ) && ( !isset( $result[ 'implementation' ][ 'source_interaction' ] ) ) )
                     {
                         $result[ 'implementation' ][ 'source_interaction' ] = $this->getReflection()->getClass( $interaction );
                     }
            }
        return $result;
    }

    public function getTemplateFieldsActions( $interaction )
    {
        $result = [];
        $actions = $this->getMap()->getActions( $interaction );
        foreach ( $actions as $action )
            {
                $configKey = $this->keyGroupHelper->encode( $action );
                $result = $result + $this->getTemplateFields( "config/system/sections/template/children/$configKey/children" );
            }
        return $result;
    }

    public function getTemplateFieldsMonitors( $interaction )
    {
        $result = [];
        $monitors = $this->getMap()->getMonitors( $interaction );
        foreach ( $monitors as $monitor )
            {
                $configKey = $this->keyGroupHelper->encode( $monitor );
                $result = $result + $this->getTemplateFields( "config/system/sections/template/children/$configKey/children" );
            }
        return $result;
    }

    public function getTemplateFieldsTriggers( $interaction )
    {
        $result = [];
        $triggers = $this->getMap()->getTriggers( $interaction );
        if ( count( $triggers ) > 0 )
            {
                $fields = $this->getTemplateFields( 'config/system/sections/template/children/triggers/children' );
                foreach ( $fields as $field )
                    {
                        $id = $field[ 'id' ];
                        switch ( $id )
                            {
                                case 'enabled':
                                    $sort = isset( $field[ 'sortOrder' ] ) ? ( int ) $field[ 'sortOrder' ] : self::DEFAULT_SORT_ORDER_CONTEXT;
                                    $counter = 0;
                                    foreach ( $triggers as $class )
                                        {
                                            $trigger = $this->getFactory()->singleton( $class );
                                            foreach ( $trigger->getContexts() as $context => $description )
                                                {
                                                    $copy = $this->merge( [], $field );
                                                    $copy[ 'sortOrder' ] = $sort + $counter;
                                                    //$id = $this->_triggerContextKey( $trigger, $context, $copy[ 'id' ] );
                                                    $id = $this->getContextKey( $context, $copy[ 'id' ] );
                                                    $copy[ 'id' ] = $id;
                                                    $copy[ 'label' ] = $description;
                                                    $result[ $id ] = $copy;
                                                    $counter++;
                                                }
                                        }
                                    break;
                                default:
                                    $result[ $id ] = $field;
                            }
                    }
            }
        return $result;
    }

    // A one-way hash - this does not need to be reversible, only repeatable
    // Requires that context keys are unique across triggers
    public function getContextKey( $context, $key )
    {
        return $context;
        $parts = [ 'context', $context, $key ];
        $result = implode( '_', $parts );
        return $result;
    }

    protected function error( $message )
    {
        throw new \Magento\Framework\Exception\LocalizedException($message);
    }

}

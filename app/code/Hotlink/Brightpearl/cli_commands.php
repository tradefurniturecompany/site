<?php

if ( PHP_SAPI == 'cli' )
    {
        \Hotlink\Framework\Console\PlatformLocator::register( 'Hotlink\Brightpearl\Model\Platform' );
    }

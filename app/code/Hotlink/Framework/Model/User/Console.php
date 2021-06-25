<?php
namespace Hotlink\Framework\Model\User;

class Console extends \Hotlink\Framework\Model\User\AbstractUser
{

    function __construct()
    {
        list( $uid, $gid ) = $this->getLinuxUserGroupIds();
        $user  = $this->getLinuxUser( $uid );
        $group = $this->getLinuxGroup( $gid );
        $host  = gethostname();
        $os    = php_uname( 's' ) . ' ' .php_uname( 'r' ) . ' (' .php_uname( 'm' ) . ')';

        $this->_username    = "$user";
        $this->_fullname    = "$user:$group";
        $this->_description = "$user @ $host";
        $this->_name        = "$user:$group";
    }

    function getType()
    {
        return "cli";
    }

    function getIP()
    {
        return gethostname();
    }

    protected function getLinuxUser( $uid )
    {
        $user = "uid" . $uid;
        try
            {
                $file = fopen( '/etc/passwd', 'r' );
                while ( $line = fgets( $file ) )
                    {
                        $parts = explode( ':', $line );
                        if ( isset( $parts[ 2 ] ) && ( $parts[ 2 ] == $uid ) )
                            {
                                $user = $parts[ 0 ];
                                break;
                            }
                    }
                fclose( $file );
            }
        catch ( \Exception $e )
            {
            }
        return $user;
    }

    protected function getLinuxGroup( $gid )
    {
        $group = "gid" . $gid;
        try
            {
                $file = fopen( '/etc/group', 'r' );
                while ( $line = fgets( $file ) )
                    {
                        $parts = explode( ':', $line );
                        if ( isset( $parts[ 2 ] ) && ( $parts[ 2 ] == $gid ) )
                            {
                                $group = $parts[ 0 ];
                                break;
                            }
                    }
                fclose( $file );
            }
        catch ( \Exception $e )
            {
            }
        return $group;
    }

    protected function getLinuxUserGroupIds()
    {
        $tempdir = sys_get_temp_dir();
        $temp = tempnam( $tempdir, "foo" );
        file_put_contents( $temp, "oink" );
        $userid = fileowner( $temp );
        $groupid = filegroup( $temp );
        unlink( $temp );
        return [ $userid, $groupid ];
    }

}

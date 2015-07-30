<?php
namespace Hubble\Library\Storage;

/**
 * @describe:
 * @author: Jerry Yang(hy0kle@gmail.com)
 * */
class DBSlave extends \Hubble\Library\Storage\DBBase
{
    private function __clone() {
    }

    public function connect()
    {
        $db = self::_createConnect('slaves');
        return $db;
    }
}
/* vi:set ts=4 sw=4 et fdm=marker: */


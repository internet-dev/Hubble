<?php
namespace Hubble\Common;

/**
 * @describe:
 * @author: Jerry Yang(hy0kle@gmail.com)
 * */
class HubbleException extends \Exception
{
    protected $code = -1;
    protected $message = '';

    public function __construct($code)
    {
        $this->code = $code;
        $this->message = \Hubble\Common\ErrorCode::getMessage($code);
    }
}
/* vi:set ts=4 sw=4 et fdm=marker: */

<?php
namespace Hotlink\Brightpearl\Model\Api\Message\Request;

interface IRequest
{
    public function getAction();
    public function getBody();
    public function getContentEncoding();
    public function getMethod();
    public function validate();
}
<?php
namespace Hotlink\Brightpearl\Model\Api\Message\Request;

interface IRequest
{
    function getAction();
    function getBody();
    function getContentEncoding();
    function getMethod();
    function validate();
}
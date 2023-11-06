<?php

namespace AwesomeCoder\Exception;

use Exception;
use AwesomeCoder\Interface\ContainerExceptionInterface;

class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}

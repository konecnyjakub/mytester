<?php
declare(strict_types=1);

namespace MyTester\Attributes;

/**
 * @author Jakub Konečný
 * @property mixed $value
 */
abstract class BaseAttribute
{
    use \Nette\SmartObject;
}

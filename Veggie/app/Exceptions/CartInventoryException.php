<?php

namespace App\Exceptions;

use RuntimeException;

class CartInventoryException extends RuntimeException
{
    public function __construct(public readonly array $issues)
    {
        parent::__construct(implode(' ', array_column($issues, 'message')));
    }
}

<?php
declare(strict_types=1);

namespace App\Domain\News;

use App\Domain\DomainException\DomainException;

class NewsDomainException extends DomainException
{
    public function __construct(string $message)
    {
        $this->message = $message;
    }

}

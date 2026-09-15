<?php

namespace App\Support;

use Heritage\Contracts\Support\Arrayable;

class CheckoutSummary implements Arrayable
{
    public function __construct(
        public readonly int $total,
        public readonly string $currency,
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'currency' => $this->currency,
        ];
    }
}

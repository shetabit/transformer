<?php

namespace Shetabit\Transformer\Tests\Fixtures;

use Shetabit\Transformer\Contracts\TransformerInterface;

class CredentialsTransformer implements TransformerInterface
{
    public function transform(array $data) : array
    {
        return [
            'username' => $data['u'] ?? null,
            'password' => $data['p'] ?? null,
        ];
    }
}

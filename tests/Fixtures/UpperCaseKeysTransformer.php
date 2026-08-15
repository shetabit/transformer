<?php

namespace Shetabit\Transformer\Tests\Fixtures;

use Shetabit\Transformer\Contracts\TransformerInterface;

class UpperCaseKeysTransformer implements TransformerInterface
{
    public function transform(array $data) : array
    {
        $transformed = [];

        foreach ($data as $key => $value) {
            $transformed[is_string($key) ? strtoupper($key) : $key] = is_array($value)
                ? $this->transform($value)
                : $value;
        }

        return $transformed;
    }
}

<?php

namespace Shetabit\Transformer\Contracts;

interface TransformerInterface
{
    /**
     * @param array<array-key, mixed> $data
     *
     * @return array<array-key, mixed>
     */
    public function transform(array $data) : array;
}

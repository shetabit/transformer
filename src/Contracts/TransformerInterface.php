<?php

namespace Shetabit\Transformer\Contracts;

interface TransformerInterface
{
    /**
     * Transform data
     * 
     * @param array $data
     * @return array
     */
    public function transform(array $data) : array;
}

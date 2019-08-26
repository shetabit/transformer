<?php

namespace Shetabit\Transformer\Contracts;

interface TransformerInterface
{
    /**
     * Transform data
     * 
     * @return array
     */
    public function transform(array $data) : array;
}
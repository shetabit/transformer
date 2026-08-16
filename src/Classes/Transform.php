<?php

namespace Shetabit\Transformer\Classes;

use Shetabit\Transformer\Contracts\TransformerInterface;
use Shetabit\Transformer\Exceptions\TransformerNotValidException;

class Transform
{
    /** @var array<array-key, mixed> */
    protected array $originalData = [];

    protected TransformerInterface|null $transformer = null;

    /** @var array<array-key, mixed> */
    protected array $transformedData = [];

    /**
     * @param array<array-key, mixed> $originalData
     */
    public function __construct(array $originalData = [])
    {
        $this->setOriginalData($originalData);
    }

    /**
     * @param array<array-key, mixed> $originalData
     */
    public function setOriginalData(array $originalData) : static
    {
        $this->originalData = $originalData;

        return $this;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getOriginalData() : array
    {
        return $this->originalData;
    }

    public function use(TransformerInterface $transformer) : static
    {
        return $this->setTransformer($transformer);
    }

    public function setTransformer(TransformerInterface $transformer) : static
    {
        $this->transformer = $transformer;

        return $this;
    }

    public function getTransformer() : TransformerInterface|null
    {
        return $this->transformer;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getTransformedData() : array
    {
        return $this->transformedData;
    }

    /**
     * @return array<array-key, mixed>
     *
     * @throws TransformerNotValidException
     */
    public function get(TransformerInterface|null $transformer = null) : array
    {
        if ($transformer instanceof TransformerInterface) {
            $this->setTransformer($transformer);
        }

        if (! $this->transformer instanceof TransformerInterface) {
            throw new TransformerNotValidException('Transformer not found');
        }

        $this->transformedData = $this->transformer->transform($this->getOriginalData());

        return $this->getTransformedData();
    }
}

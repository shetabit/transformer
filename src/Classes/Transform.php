<?php

namespace Shetabit\Transformer\Classes;

use Shetabit\Transformer\Contracts\TransformerInterface;
use Shetabit\Transformer\Exceptions\TransformerNotValidException;

class Transform
{
    /**
     * Main data
     *
     * @var array
     */
    protected $originalData = [];

    /**
     * Transformer
     * 
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * Transformed data
     *
     * @var array
     */
    protected $transformedData = [];

    /**
     * Transform constructor.
     *
     * @param array $originalData
     */
    public function __construct(array $originalData = [])
    {
        $this->setOriginalData($originalData);
    }

    /**
     * Set original data
     *
     * @param array $originalData
     * @return $this
     */
    public function setOriginalData(array $originalData)
    {
        $this->originalData = $originalData;

        return $this;
    }

    /**
     * Retrieve original data
     *
     * @return array
     */
    public function getOriginalData() : array
    {
        return $this->originalData;
    }

    /**
     * Set data transformer
     * 
     * @param TransformerInterface $transformer
     * @return $this
     */
    public function use(TransformerInterface $transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Retrieve transformed data
     *
     * @return array
     */
    public function getTransformedData() : array
    {
        return $this->transformedData;
    }

    /**
     * Run transformer
     *
     * @param TransformerInterface|null $transformer
     * @return array
     * @throws TransformerNotValidException
     */
    public function get(TransformerInterface $transformer = null) : array
    {
        if (!empty($transformer)) {
            $this->setTransformer($transformer);
        }

        if (empty($this->transformer)) {
            throw new TransformerNotValidException('Transformer not found');
        }

        $this->transformedData = $this->transformer->transform($this->getOriginalData());

        return $this->getTransformedData();
    }
}

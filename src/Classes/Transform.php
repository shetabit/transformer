<?php

namespace Shetabit\Transformer\Classes;

use Shetabit\Transformer\Classes\Transformer;
use Shetabit\Transformer\Contracts\TransformerInterface;

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
     * Class constructor
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
     *
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
     * @return $this
     */
    public function setTransformer(TransformerInterface $transformer)
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
     * @param TransformerInterface $transformer
     *
     * @return array
     */
    public function get(TransformerInterface $transformer = null) : array
    {
        if (!empty($transformer)) {
            $this->setTransformer($transformer);
        }

        $this->transformedData = $this->transformer->transform($this->getOriginalData());

        return $this->getTransformedData();
    }
}

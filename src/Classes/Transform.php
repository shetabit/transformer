<?php

namespace Shetabit\Transformer\Classes;

class Transform
{
    /**
     * Main data
     *
     * @var array
     */
    protected $originalData = [];

    /**
     * Transformed data
     *
     * @var array
     */
    protected $transformedData = [];

    /**
     * Main's format
     *
     * @var array
     */
    protected $sourceFormat = [];

    /**
     * Destination's format
     *
     * @var array
     */
    protected $destinationFormat = [];

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
     * Retrieve transformed data
     *
     * @return array
     */
    public function getTransformedData() : array
    {
        return $this->transformedData;
    }

    /**
     * Set current data's format
     *
     * @param array $format
     *
     * @return $this
     */
    public function from(array $format)
    {
        $this->sourceFormat = $format;

        return $this;
    }

    /**
     * Set destination data's format
     *
     * @param array $format
     *
     * @return $this
     */
    public function to(array $format)
    {
        $this->destinationFormat = $format;

        return $this;
    }

    /**
     * Run transformer
     *
     * @param array $format
     *
     * @return array
     */
    public function get(array $format = []) : array
    {
        if (!empty($format)) {
            $this->from(array_keys($format))->to(array_values($format));
        }

        $this->transformedData = $this->getOriginalData();

        $refactorFormat = array_combine($this->sourceFormat, $this->destinationFormat);

        foreach ($refactorFormat as $from => $to) {
            $this->transformedData = $this->replaceKey($this->getTransformedData(), $from, $to);
        }

        return $this->getTransformedData();
    }

    /**
     * Replace a key
     *
     * @param array $originalData
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function replaceKey(array $originalData, string $from, string $to) : array
    {
        $index = array_search($from, array_keys($originalData));

        if ($index !== false) {
            $replacement = array($to => $originalData[$from]);

            array_splice_assoc($originalData, $index, 1, $replacement);
        }

        return $originalData;
    }
}

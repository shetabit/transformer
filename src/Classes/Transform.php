<?php

namespace Shetabit\Transformer\Classes;

class Transform 
{
    protected $originalData;

    protected $transformedData;

    protected $sourceFormat;

    protected $destinationFormat;

    public function __construct(array $originalData = [])
    {
        $this->setOriginalData($originalData);
    }

    public function setOriginalData(array $originalData)
    {
        $this->originalData = $originalData;

        return $this;
    }

    public function getOriginalData()
    {
        return $this->originalData;
    }

    public function from(array $format)
    {
        $this->sourceFormat = $format;

        return $this;
    }

    public function to(array $format)
    {
        $this->destinationFormat = $format;

        return $this;
    }

    public function get(array $format = []) : array
    {
        $reformedData = $this->getOriginalData();

        $refactorFormat = empty($format) ? array_combine($this->sourceFormat, $this->originalFormat) : $format;

        $currentKeys = array_keys($this->getOriginalData());

        foreach ($refactorFormat as $from => $to) {
            $reformedData = $this->replaceKey($reformedData, $from, $to);
        }
   
        return $reformedData;
    }

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

<?php

namespace Shetabit\Transformer\Classes;

use Shetabit\Transformer\Contracts\TransformerInterface;

class Transformer implements TransformerInterface
{
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
     * Transformer constructor
     *
     * @param array $format
     */
    public function __construct(array $format = [])
    {
        $this->from(array_keys($format))->to(array_values($format));
    }

    /**
     * Transform data
     * 
     * @return array
     */
    public function transform(array $data) : array
    {
        foreach ($this->getFormat() as $from => $to) {
            $data = $this->replaceKey($data, $from, $to);
        }

        return $data;
    }

    /**
     * Set current data's format
     *
     * @param array $format
     *
     * @return $this
     */
    public function from($format)
    {
        $format = is_array($format) ? $format : [$format];

        $this->sourceFormat = array_merge($this->sourceFormat, $format);

        return $this;
    }

    /**
     * Set destination data's format
     *
     * @param $format
     * @return $this
     */
    public function to($format)
    {

        $format = is_array($format) ? $format : [$format];

        $this->destinationFormat = array_merge($this->destinationFormat, $format);

        return $this;
    }

    /**
     * produce transform format
     * 
     * @return array
     */
    protected function getFormat() : array
    {
        return array_combine($this->sourceFormat, $this->destinationFormat);
    }

    /**
     * Replace a key
     *
     * @param array $originalData
     * @param string $from
     * @param string $to
     * @return array
     */
    protected function replaceKey(array $originalData, string $from, string $to) : array
    {
        $index = array_search($from, array_keys($originalData));

        if ($index !== false) {
            $replacement = array($to => $originalData[$from]);

            array_splice_assoc($originalData, $index, 1, $replacement);
        }

        return $originalData;
    }
}

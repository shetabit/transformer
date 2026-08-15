<?php

namespace Shetabit\Transformer\Classes;

use Shetabit\Transformer\Contracts\TransformerInterface;
use Shetabit\Transformer\Exceptions\InvalidFormatException;

class Transformer implements TransformerInterface
{
    /** @var list<array-key> */
    protected array $sourceFormat = [];

    /** @var list<array-key> */
    protected array $destinationFormat = [];

    protected bool $recursive = false;

    /**
     * @param array<array-key, array-key> $format
     */
    public function __construct(array $format = [], bool $recursive = false)
    {
        $this->from(array_keys($format))->to(array_values($format))->recursive($recursive);
    }

    /**
     * @param array<array-key, mixed> $data
     *
     * @return array<array-key, mixed>
     *
     * @throws InvalidFormatException
     */
    public function transform(array $data) : array
    {
        return $this->rename($data, $this->getFormat());
    }

    /**
     * @param array-key|array<array-key, array-key> $format
     */
    public function from(int|string|array $format) : static
    {
        $this->sourceFormat = [...$this->sourceFormat, ...array_values((array) $format)];

        return $this;
    }

    /**
     * @param array-key|array<array-key, array-key> $format
     */
    public function to(int|string|array $format) : static
    {
        $this->destinationFormat = [...$this->destinationFormat, ...array_values((array) $format)];

        return $this;
    }

    /**
     * Whether the key map is applied to the arrays nested in the data as well.
     */
    public function recursive(bool $recursive = true) : static
    {
        $this->recursive = $recursive;

        return $this;
    }

    public function isRecursive() : bool
    {
        return $this->recursive;
    }

    /**
     * @return array<array-key, array-key>
     *
     * @throws InvalidFormatException
     */
    public function getFormat() : array
    {
        if (count($this->sourceFormat) !== count($this->destinationFormat)) {
            throw new InvalidFormatException(sprintf(
                'The format is incomplete: %d source key(s) are mapped onto %d destination key(s).',
                count($this->sourceFormat),
                count($this->destinationFormat),
            ));
        }

        return array_combine($this->sourceFormat, $this->destinationFormat);
    }

    /**
     * Every key is renamed at once, so a key can take the name another key is giving up
     * in the same run. Where two keys end up with the same name the renamed one wins,
     * and of two renamed ones the one that comes first in the data.
     *
     * @param array<array-key, mixed>     $data
     * @param array<array-key, array-key> $format
     *
     * @return array<array-key, mixed>
     */
    protected function rename(array $data, array $format) : array
    {
        $renamed = [];
        $takenByRename = [];

        foreach ($data as $key => $value) {
            if ($this->recursive && is_array($value)) {
                $value = $this->rename($value, $format);
            }

            $isMapped = array_key_exists($key, $format);
            $destination = $isMapped ? $format[$key] : $key;

            if (array_key_exists($destination, $renamed) && (! $isMapped || isset($takenByRename[$destination]))) {
                continue;
            }

            $renamed[$destination] = $value;

            if ($isMapped) {
                $takenByRename[$destination] = true;
            }
        }

        return $renamed;
    }
}

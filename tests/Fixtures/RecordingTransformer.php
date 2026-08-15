<?php

namespace Shetabit\Transformer\Tests\Fixtures;

use Shetabit\Transformer\Contracts\TransformerInterface;

class RecordingTransformer implements TransformerInterface
{
    /** @var list<array<array-key, mixed>> */
    public array $calls = [];

    public function transform(array $data) : array
    {
        $this->calls[] = $data;

        return ['called' => count($this->calls)];
    }
}

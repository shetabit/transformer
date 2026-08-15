<?php

namespace Shetabit\Transformer\Tests\Feature;

use Shetabit\Transformer\Classes\Transform;
use Shetabit\Transformer\Classes\Transformer;
use Shetabit\Transformer\Tests\Fixtures\CredentialsTransformer;
use Shetabit\Transformer\Tests\TestCase;

class DocumentedUsageTest extends TestCase
{
    public function testAFormatHandedToTheTransformerRenamesTheKeys() : void
    {
        $originalData = [
            'f_name' => 'mahdi',
            'l_name' => 'khanzadi',
        ];

        $transformer = new Transformer([
            'f_name' => 'first_name',
            'l_name' => 'last_name',
        ]);

        $this->assertSame(
            ['first_name' => 'mahdi', 'last_name' => 'khanzadi'],
            new Transform($originalData)->get($transformer),
        );
    }

    public function testFromAndToRenameTheKeys() : void
    {
        $originalData = [
            'f_name' => 'mahdi',
            'l_name' => 'khanzadi',
        ];

        $transformer = new Transformer();
        $transformer->from('f_name')->to('first_name');
        $transformer->from('l_name')->to('last_name');

        $this->assertSame(
            ['first_name' => 'mahdi', 'last_name' => 'khanzadi'],
            new Transform($originalData)->use($transformer)->get(),
        );
    }

    public function testACustomTransformerBuildsWhateverItLikes() : void
    {
        $originalData = [
            'u' => 'mahdikhanzadi',
            'p' => '246810',
        ];

        $this->assertSame(
            ['username' => 'mahdikhanzadi', 'password' => '246810'],
            new Transform($originalData)->get(new CredentialsTransformer()),
        );
    }

    public function testOneTransformRunsOverEveryRowOfAResultSet() : void
    {
        $rows = [
            ['f_name' => 'mahdi', 'l_name' => 'khanzadi'],
            ['f_name' => 'ali', 'l_name' => 'ahmadi'],
        ];

        $transform = new Transform()->use(new Transformer([
            'f_name' => 'first_name',
            'l_name' => 'last_name',
        ]));

        $transformed = array_map(
            static fn (array $row): array => $transform->setOriginalData($row)->get(),
            $rows,
        );

        $this->assertSame([
            ['first_name' => 'mahdi', 'last_name' => 'khanzadi'],
            ['first_name' => 'ali', 'last_name' => 'ahmadi'],
        ], $transformed);
    }
}

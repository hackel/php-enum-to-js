<?php

declare(strict_types=1);

use Hackel\EnumToJs\EnumFinder;
use Hackel\EnumToJs\Tests\Enums\Color;
use Hackel\EnumToJs\Tests\Enums\Nested\Path\DayOfWeek;
use Hackel\EnumToJs\Tests\Enums\Number;
use Hackel\EnumToJs\Tests\Enums\Suit;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

describe('EnumFinder', function () {
    it('can retrieve all the enums in the given path', function () {
        $enumFinder = app(EnumFinder::class);

        $enums = $enumFinder->findEnums(__DIR__ . '/Enums');

        expect($enums)->toBeInstanceOf(Collection::class)
            ->and($enums->count())->toBe(4)
            ->and($enums->toArray())->toBe([
                Suit::class,
                Color::class,
                Number::class,
                DayOfWeek::class,
            ]);
    });

    it('skips a file if it does not have a namespace', function () {
        $finder = $this->mock(Symfony\Component\Finder\Finder::class)
            ->shouldReceive('files')
            ->andReturnSelf()
            ->shouldReceive('in')
            ->andReturnSelf()
            ->shouldReceive('name')
            ->andReturnSelf()
            ->shouldReceive('getIterator')
            ->andReturn(new ArrayIterator([
                new SplFileInfo(
                    __DIR__ . '/Enums/NoNamespaceEnum.php',
                    __DIR__ . '/Enums',
                    __DIR__ . '/Enums/NoNamespaceEnum.php',
                ),
            ]))
            ->getMock();

        $enumFinder = new EnumFinder($finder);
        $enums = $enumFinder->findEnums(__DIR__ . '/Enums');

        expect($enums)->toBeEmpty();
    });
});

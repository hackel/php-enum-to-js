<?php

declare(strict_types=1);

namespace Hackel\EnumToJs\Tests\Feature;

use Hackel\EnumToJs\EnumToJson;
use Hackel\EnumToJs\Tests\Enums\Color;
use Hackel\EnumToJs\Tests\Enums\Number;
use Hackel\EnumToJs\Tests\Enums\Suit;

describe('EnumToJson', function () {
    it('converts a string-backed PHP Enum into a JSON object', function () {
        $enum = Color::class;

        $action = app(EnumToJson::class);

        $jsObject = $action->convert($enum);

        expect($jsObject)->toBe(
            <<<'JSON'
            {
                "RED": "red",
                "GREEN": "green",
                "BLUE": "blue"
            }
            JSON
        );
    });

    it('converts an int-backed PHP Enum into a JSON object', function () {
        $enum = Number::class;

        $action = app(EnumToJson::class);

        $jsObject = $action->convert($enum);

        expect($jsObject)->toBe(
            <<<'JSON'
            {
                "ONE": 1,
                "TWO": 2,
                "THREE": 3
            }
            JSON
        );
    });

    it('converts a pure PHP Enum into a JSON array', function () {
        $enum = Suit::class;

        $action = app(EnumToJson::class);

        $jsObject = $action->convert($enum);

        expect($jsObject)->toBe(
            <<<'JSON'
            [
                "Hearts",
                "Diamonds",
                "Clubs",
                "Spades"
            ]
            JSON
        );
    });
});

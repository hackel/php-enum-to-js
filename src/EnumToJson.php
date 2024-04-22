<?php

declare(strict_types=1);

namespace Hackel\EnumToJs;

use Hackel\EnumToJs\Contracts\EnumToJson as EnumToJsonContract;
use Illuminate\Support\Collection;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use ReflectionEnumUnitCase;
use UnitEnum;

final class EnumToJson implements EnumToJsonContract
{
    /**
     * @param class-string<UnitEnum> $enum
     * @throws \ReflectionException
     */
    public function convert(string $enum): string
    {
        $cases = $this->getCases($enum);

        return $this->getJsEnumContent($cases);
    }

    /**
     * @param class-string<UnitEnum> $enum
     * @return Collection<int, string>|Collection<string, int|string>
     * @throws \ReflectionException
     */
    private function getCases(string $enum): Collection
    {
        $reflector = new ReflectionEnum($enum);
        $cases = $reflector->getCases();

        if ($reflector->isBacked()) {
            return collect($cases)->flatMap(
                /** @phpstan-ignore-next-line */
                fn(ReflectionEnumBackedCase $case): array => [
                    $case->getName() => $case->getBackingValue(),
                ],
            );
        }

        return collect($cases)->flatMap(
            fn(ReflectionEnumUnitCase $case): array => [
                $case->getName(),
            ],
        );
    }

    /**
     * @param Collection<int, string>|Collection<string, int|string> $cases
     */
    private function getJsEnumContent(Collection $cases): string
    {
        return $cases->toJson(JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }
}

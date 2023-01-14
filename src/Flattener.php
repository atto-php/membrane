<?php

declare(strict_types=1);

namespace Atto\Membrane;

use Membrane\Attribute\Placement;
use Membrane\Attribute\SetFilterOrValidator;
use Membrane\Filter\CreateObject\FromArray;

#[SetFilterOrValidator(new FromArray(Flattener::class), Placement::BEFORE)]
final class Flattener
{
    public static function fromArray(array $data): array
    {
        $flattened = [];
        foreach (['path', 'query', 'body'] as $key) {
            if (is_array($data[$key])) {
                $flattened = array_merge($flattened, $data[$key]);
            }
        }

        return $flattened;
    }
}
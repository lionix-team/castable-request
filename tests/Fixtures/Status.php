<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Tests\Fixtures;

enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
}

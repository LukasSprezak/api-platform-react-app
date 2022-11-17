<?php
declare(strict_types=1);
namespace App\Enum;

enum StatusProductEnum: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case FINISHED = 'finished';
}

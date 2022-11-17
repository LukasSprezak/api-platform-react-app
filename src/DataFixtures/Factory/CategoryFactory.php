<?php
declare(strict_types=1);
namespace App\DataFixtures\Factory;

use App\Entity\Category;
use Zenstruck\Foundry\ModelFactory;
use function Zenstruck\Foundry\faker;

class CategoryFactory extends ModelFactory
{
    protected static function getClass(): string
    {
        return Category::class;
    }

    protected function getDefaults(): array
    {
        return  [
            'createdAt' => faker()->dateTimeBetween('-2 year', '-1 year'),
            'updatedAt' => faker()->dateTimeThisYear
        ];
    }
}
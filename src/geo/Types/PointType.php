<?php

namespace App\geo\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

use App\geo\Objects\Point;

class PointType extends Type
{
    const POINT = 'point';

    public function getName():string
    {
        return self::POINT;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform):string
    {
        return 'POINT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform):Point
    {
        list($latitude, $longitude) = sscanf($value, '(%f, %f)');

        return new Point($latitude, $longitude);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform):mixed
    {
        if ($value instanceof Point) {
            $value = sprintf('(%F, %F)', $value->getLatitude(), $value->getLongitude());
        }

        return $value;
    }

    public function canRequireSQLConversion():bool
    {
        return true;
    }
}
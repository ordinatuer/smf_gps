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

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        list($longitude, $latitude) = sscanf($value, 'point(%f, %f)');

        return new Point($latitude, $longitude);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Point) {
            $value = sprintf('(%F, %F)', $value->getLongitude(), $value->getLatitude());
        }

        return $value;
    }

    public function canRequireSQLConversion()
    {
        return true;
    }

    // public function convertToPHPValueSQL($sqlExpr, AbstractPlatform $platform)
    // {
    //     return sprintf('AsText(%s)', $sqlExpr);
    // }

    // public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    // {
    //     return sprintf('PointFromText(%s)', $sqlExpr);
    // }
}
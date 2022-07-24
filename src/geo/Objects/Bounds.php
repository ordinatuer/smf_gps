<?php
namespace App\geo\Objects;

class Bounds
{
    public function __construct(
        private float $southWestLat,
        private float $southWestLng,
        private float $northEastLat,
        private float $northEastLng,
    ){}
    /**
     * @return float
     */ 
    public function getSouthWestLat()
    {
        return $this->southWestLat;
    }

    /**
     * @return float
     */ 
    public function getSouthWestLng()
    {
        return $this->southWestLng;
    }

    /**
     * @return float
     */ 
    public function getNorthEastLat()
    {
        return $this->northEastLat;
    }

    /**
     * @return float
     */ 
    public function getNorthEastLng()
    {
        return $this->northEastLng;
    }
}
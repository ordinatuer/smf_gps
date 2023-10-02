<?php
namespace App\Service\Encoders;

use Symfony\Component\Serializer\Encoder\XmlEncoder;

class WaypointsService
{
    const TEMPLATE = [
        // '@creator' => 'Health',
        '@xsi:schemaLocation' => 'http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd',
        '@version' => '1.0',
        '@xmlns' => 'http://www.topografix.com/GPX/1/0',
        '@xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
    ];
    private $encoder;

    public function __construct()
    {
        $this->encoder = new XmlEncoder();
    }

    public function encode(array $addressList)
    {
        $wayPoints = [];

        foreach ($addressList as $address) {
            $wayPoints[] = [
                '@lat' => $address['lat'],
                '@lon' => $address['lon'],
                '#' => [
                    'name' => $this->getNameTeil($address['address']),
                    'desc' => $address['address'],
                    'extensions' => [
                        'osmand:color' => '#b4d00d0d',
                        'osmand:icon' => 'place_town',
                        'osmand:background' => 'circle',
                    ],
                ],
            ];
        }

        $data = [
            'metadata' => [
                'name' => 'Metadata name',
                'desc' => 'Metadata description',
                'author' => [
                    'name' => 'Author name',
                ],
            ],
            'wpt' => $wayPoints,
        ];

        return $data;
    }

    private function getNameTeil(string $address):string
    {
        $_address = explode(', ', $address);

        return $_address[2] . ', ' . $_address[3];
    }
}
<?php
namespace App\Service\Geocode;

use ErrorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class YandexGeocode
{
    public function __construct(
        private HttpClientInterface $client,
        private string $api_url,
        private string $api_key,
        private LoggerInterface $logger,
    )
    {}
    
    /**
     * Отправка запроса с использовнием yii/httpclient
     * https://geocode-maps.yandex.ru/1.x/?apikey=API_KEY&geocode=City+Street+house+build&format=json&kind=house
     */
    public function geocode(Array $data)
    {
        $geocodeString = $this->getGeocodeString($data);

        $response  = $this->client->request('GET', $this->api_url, [
            'query' => [
                'apikey' => $this->api_key,
                'geocode' => $geocodeString,
                'format' => 'json',
                'kind' => 'house',
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new ErrorException('Yandex Geocode API Error. StatusCode is ' . $response->getStatusCode());
        }

        $response_data = $response->toArray()['response'];

        $this->logger->info(implode('|', [
            $geocodeString,
            json_encode($data, JSON_UNESCAPED_UNICODE),
            json_encode($response_data, JSON_UNESCAPED_UNICODE),
        ]));

        if (!isset($response_data['GeoObjectCollection'])) {
            throw new ErrorException('Yandex Geocode API Error. Bad Response. Element "GeoObjectCollection" not found');
        }

        $address_data = [];
        // $address_text = $response_data['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['formatted'];
        // $name = $response_data['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'];
        $address_data['address'] = $response_data['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['formatted'];
        list($address_data['lon'], $address_data['lat']) = explode(' ', $response_data['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);
        $address_data['api_response'] = json_encode($response_data, JSON_UNESCAPED_UNICODE);

        return $address_data;
    }

    private function getGeocodeString(Array $data):String
    {
        $string = implode('+', $data);

        return $string;
    }
}
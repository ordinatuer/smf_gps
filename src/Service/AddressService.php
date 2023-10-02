<?php
namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use App\Entity\Yafile;
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Service\Geocode\YandexGeocode;
use App\Service\Exceptions\AddressServiceException;
use ErrorException;

class AddressService
{
    const OPERATORS = [
        'OPERATOR_1' => 1,
        'OPERATOR_2' => 2,
        // ...
    ];

    public $message = '';

    public function __construct(
        private YafileUploader $yafileUploader,
        private AddressRepository $addressRepository,
        private YandexGeocode $geocode,
    )
    {}

    /**
     * @throws AddressServiceException
     */
    public function work(Yafile $address)
    {
        try {
            return $this->getFile($address);
        } catch(AddressServiceException $e) {
            $this->message = $e->getMessage();
        }
    }

    /**
     * @param Yafile $address
     * @throws AddressServiceException
     * @return Array
     */
    public function getFile(Yafile $addressFile):Array
    {
        $file = $this->yafileUploader->getDirectory(YafileUploader::ADDRESS_DIR) . DIRECTORY_SEPARATOR . $addressFile->getName();
        $f = fopen($file, 'r');

        if (!$f) {
            throw new AddressServiceException('File open error');
        }

        $stat = [
            'add' => 0,
            'total' => 0,
        ];

        while(($data = fgetcsv($f, separator: ',')) !== false ) {

            try {
                $data_geocode = $this->geocode->geocode($data);
            } catch(ErrorException $e) {
                $this->message = $e->getMessage();
                break;
            }

            $stat['total']++;

            if ($this->addressRepository->findOneBy([
                'city' => Address::DEFAULT_CITY,
                'street' => $data[0],
                'house' => $data[1],
                'build' => $data[2],
                'file' => $addressFile->getId(),
            ]) ) {
                continue;
            }

            $address = new Address();

            $address->setDefaultCity();

            $address->setStreet($data[0]);
            $address->setHouse($data[1]);
            $address->setBuild($data[2]);

            $address->setAddress($data_geocode['address']);
            $address->setLat($data_geocode['lat']);
            $address->setLon($data_geocode['lon']);

            //ST_SetSRID(ST_MakePoint(long, lat), 4326)
            // https://github.com/jsor/doctrine-postgis
            $point = sprintf('SRID=4326;POINT(%f %f)', $data_geocode['lon'], $data_geocode['lat']);
            $address->setLocation($point);

            $address->setOperator(self::OPERATORS['OPERATOR_1']);
            $address->setApiResponse($data_geocode['api_response']);
            $address->setFile($addressFile);

            $this->addressRepository->add($address);
            $stat['add']++;
        }

        return $stat;
    }
}
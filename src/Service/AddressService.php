<?php
namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use App\Entity\Yafile;
use App\Entity\Address;
use App\Repository\AddressRepository;
// use App\Repository\YafileRepository;
use App\Service\Geocode\YandexGeocode;
use App\Service\Exceptions\AddressServiceException;
use App\geo\Objects\Point;
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
        private TokenStorageInterface $user,
        // private YafileRepository $yafileRepository,
        private AddressRepository $addresRepository,
        private YandexGeocode $geocode,
    )
    {}

    /**
     * @throws AddressServiceException
     */
    public function work(Yafile $address)
    {
        try {
            $this->getFile($address);
        } catch(AddressServiceException $e) {
            $this->message = $e->getMessage();
        }
    }

    /**
     * @param Yafile $address
     * @throws AddressServiceException
     * @return resource
     */
    public function getFile(Yafile $addressFile)
    {
        $file = $this->yafileUploader->getDirectory(YafileUploader::ADDRESS_DIR) . DIRECTORY_SEPARATOR . $addressFile->getName();
        $f = fopen($file, 'r');

        if (!$f) {
            throw new AddressServiceException('File open error');
        }

        while(($data = fgetcsv($f, separator: ',')) !== false ) {

            try {
                $data_geocode = $this->geocode->geocode($data);
            } catch(ErrorException $e) {
                $this->message = $e->getMessage();
                return false;
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
            $point = sprintf('SRID=4326;POINT(%f %f)', $data_geocode['lon'], $data_geocode['lat']);
            $address->setLocation($point);

            $address->setOperator(self::OPERATORS['OPERATOR_1']);
            $address->setApiResponse($data_geocode['api_response']);
            $address->setFile($addressFile);

            $this->addresRepository->add($address);
        }
    }
}
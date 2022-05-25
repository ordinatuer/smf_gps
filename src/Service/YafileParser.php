<?php
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Corruption;
use App\Entity\Yafile;
use App\geo\Objects\Point;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

//use Symfony\Component\Validator\Constraints\Length;

class YafileParser
{
    const EXT_CSV = '.csv';
    const SEPARATOR_CSV = ',';

    public function __construct(
        private string $directory,
        private ManagerRegistry $doctrine,

        private int $writeRows = 100,
        private int $insertedRows = 0,
        private int $totalTime = 0,
    ) {}

    public function getFilesList(): array
    {
        $files = scandir($this->directory);
        $list = [];

        foreach ($files as $file) {
            if (stristr($file, self::EXT_CSV)) {
                $list[] = $file;
            }
        }

        return $list;
    }

    /**
     * CSV не валидный, поэтому немного костылей 
     */
    public function readFile(Yafile $file)
    {
        $manager = $this->doctrine->getManager();

        try {
            $_status = 0;

            $filename = $this->directory . '/' . $file->getName();
            if (!file_exists($filename)) {
                $_status = Yafile::LOAD_LOST;
                throw new \Exception('File not found');
            }

            $fp = fopen($filename,'r');
            if (!$fp) {
                $_status = Yafile::FILE_OPEN_ERROR;
                throw new \Exception('File open error');
            }
        } catch (\Exception $fe) {
            $file->setStatus($_status);
            $manager->flush();

            return $filename . ' ' . $fe->getMessage() . " --- FILE OPEN ERROR  --- \n";
        }

        if ($fp) {
            // .CSV headers
            $row = fgetcsv($fp, separator: self::SEPARATOR_CSV);

            $file->setStatus(Yafile::LOAD_PARSE_IN_PROGRESS);
            $manager->flush();

            // отключаем логирование для снижения потребления ресурсов (критично)
            $manager->getConnection()->getConfiguration()->setSQLLogger(null);

            $i = 0;

            // ######################################################
            if (0 === $this->totalTime) {
                $this->totalTime = time();
            }
            // ######################################################

            // построчное чтение
            while( ($row = fgetcsv($fp, separator: self::SEPARATOR_CSV) ) !== false) {
                $i++;

                $manager->persist( $this->setCorruption($row) );

                if ($this->writeRows < $i) {
                    $i = 0;
                    $manager->flush();
                    $manager->clear();

                    $this->insertedRows += $this->writeRows;
                }
            }

            $this->insertedRows += $i;

            // необходимо после очистки ($manager->clear())
            $_file = $manager->getRepository(Yafile::class)->find($file->getId());
            $_file->setStatus(Yafile::LOAD_PARSED);
            $manager->flush();

            $time = time() - $this->totalTime;
            $time_str = $time . ' sec';

            if (60 < $time) {
                $sec = $time % 60;
                $min = ($time - $sec) / 60;

                $time_str = $min . ' min ' . $sec . ' sec';
            }

            print(
                $this->insertedRows . ' | ' .
                //$file->getId() . ' | ' .
                $time_str .
                " ---  END FILE  ---------------------------- \n");

            fclose($fp);
        } else {
            // ошибка при открытии файла
            $file->setStatus(Yafile::LOAD_LOST);
            $manager->flush();
        }

        $manager->clear();

        return $this->insertedRows;
    }

    public function yafiles()
    {
        $files = $this->getFilesList();

        $repository = $this->doctrine->getRepository(Yafile::class);
        $ins = [];

        foreach($files as $file) {
            $_file = null;
            $dbFile = $repository->findOneBy(['name' => $file]);

            if (empty($dbFile)) {
                $newFile = $this->makeYafile($file);
                $repository->add($newFile);

                $_file = $newFile;
            }

            if ($dbFile && $dbFile->getStatus() === Yafile::LOAD_NOT_PARSED) {
                $_file = $dbFile;
            }

            if (!is_null($_file)) {
                $ins[] = $this->readFile($_file);
            }
        }

        return $ins;
    }

    private function makeYafile(string $file, int $status = Yafile::LOAD_NOT_PARSED):Yafile
    {
        $entity = new Yafile();

        $entity->setName($file);
        $entity->setAdded(new \DateTime(filectime($this->directory .'/' . $file)));
        $entity->setStatus($status);

        return $entity;
    }

    /**
     * заполнение модели для БД
     */
    private function setCorruption(array $data):Corruption
    {
        $cor = new Corruption();

        $cor->setYaId($data[0]);
        $cor->setFirstName($data[1]);
        $cor->setFullName($data[2]);

        $cor->setEmail($data[3]);
        $cor->setPhoneNumber($data[4]);

        $cor->setAddressCity($data[5]);
        $cor->setAddressStreet($data[6]);
        $cor->setAddressHouse($data[7]);
        $cor->setAddressEntrance($data[8]);
        $cor->setAddressFloor($data[9]);
        $cor->setAddressOffice($data[10]);

        $cor->setAddressComment($data[11]);

        $cor->setAddressDoorcode($data[18]);

        $location = new Point($data[12], $data[13]);
        $cor->setLocation($location);
        $cor->setLocationLatitude($data[12]);
        $cor->setLocationLongitude($data[13]);

        $cor->setAmountCharged($data[14]);
        $cor->setUserId($data[15]);
        $cor->setUserAgent($data[16]);

        $time = new \DateTimeImmutable($data[17]);
        $cor->setCreatedAt($time);
        
        return $cor;
    }
}

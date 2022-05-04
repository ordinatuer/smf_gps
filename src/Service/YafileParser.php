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

//    private string $directory;
//    private ManagerRegistry $doctrine;

//    private int $writeRows;
//    private int $insertedRows;
//    private int $totalTime;

    public function __construct(
        private string $directory,
        private ManagerRegistry $doctrine,

        private int $writeRows = 100,
        private int $insertedRows = 0,
        private int $totalTime = 0
    )
    {
//        $this->directory = $directory;
//        $this->doctrine = $doctrine;

//        $this->writeRows = 100;
//        $this->insertedRows = 0;
//        $this->totalTime = 0;
    }

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
        try {
            $filename = $this->directory .'/test/test_' . $file->getName();
            if (!file_exists($filename)) {
                throw new \Exception('File not found');
            }

            $fp = fopen($filename,'r');

            if (!$fp) {
                throw new \Exception('File open error');
            }
        } catch (\Exception $fe) {
            return $filename . ' ' . $fe->getMessage() . " --- FILE OPEN ERROR  --- \n";
        }


        $manager = $this->doctrine->getManager();

        if ($fp) {
            // .CSV headers
            $row = fgets($fp);

            $row_buffer = '';

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
            while( ($row = fgets($fp) ) !== false) {
                // переносы строки в данных, заключенных в кавычки " ... \n ... "
                // ..., ...," ...\n <---
                // ..., ..., ...,\n <---
                // ... ", ..., ...
                if ( ((substr_count($row, '"') % 2) === 1) OR ('' !== $row_buffer) ) {
                    $row_buffer .= $row;
                }

                // ..., ...," ...\n
                // ..., ..., ...,\n
                // ... ", ..., ... <---
                // OR
                // ..., ..., ..., ...
                // OR
                // ..., ..., "...", ..., "..."
                if (('' != $row_buffer) AND ((substr_count($row_buffer, '"') % 2)) === 0 ) {
                    $row = $row_buffer;
                    $row_buffer = '';
                }

                if ('' == $row_buffer) {
                    $i++;

                    $cols = $this->explodeString($row);

                    $manager->persist( $this->setCorruption($cols) );
                    
                    if ($this->writeRows < $i) {
                        $i = 0;
                        $manager->flush();
                        $manager->clear();

                        // ###################################
                        $this->insertedRows += $this->writeRows;

                        $time = time() - $this->totalTime;
                        $time_str = $time . ' sec';

                        if (60 < $time) {
                            $sec = $time % 60;
                            $min = ($time - $sec) / 60;

                            $time_str = $min . ' min ' . $sec . ' sec';
                        }
                        // ######################################

                        print($this->insertedRows . ' | ' . $time_str . " ---------------------------- \n");
                    }
                }
            }

            $this->insertedRows += $i;

            // необходимо после очистки ($manager->clear())
            $_file = $manager->getRepository(Yafile::class)->find($file->getId());
            $_file->setStatus(Yafile::LOAD_PARSED);
            $manager->flush();

            print($this->insertedRows . '| '. $file->getId() . ' | ' . " ---  END FILE  ---------------------------- \n");

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
     * нарезка строки по столбцам с учётом данных в двойных кавычках
     */
    private function explodeString(string $string):array
    {
        $replaced = '||REPLACED||';
        $string = str_replace('""', "'", $string);

        preg_match_all('/"([^"]+)"/', $string, $matches);

        // замена вместе с кавычками
        if (0 < count($matches[0]) ) {
            foreach($matches[0] as $match) {
                $string = str_replace($match, $replaced, $string);
            }
        }

        $data = explode(',', $string);

        // запись данных без кавычек
        if (0 < count($matches[1]) ) {
            foreach($data as $key => $value) {
                if ($value == $replaced) {
                    $data[$key] = array_shift($matches[1]);
                }
            }
        }

        return $data;
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

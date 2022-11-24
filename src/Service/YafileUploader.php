<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class YafileUploader
{
    const ADDRESS_DIR = 'address';
    
    public $errorMessage;

    public function __construct(
        private string $directory,
        private SluggerInterface $slugger,
    ){}

    public function upload(UploadedFile $file, String $subDirectory = '')
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        // $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        $newFilename = $safeFilename . '.' . $file->guessExtension();
    
        try {
            $file->move($this->getDirectory($subDirectory), $newFilename);
        } catch(FileException $fe) {
            $this->errorMessage = $fe->getMessage();

            return false;
        }

        return $newFilename;
    }

    public function getDirectory(String $subDirectory = ''): String
    {
        if ('' !== $subDirectory) {
            return $this->directory . DIRECTORY_SEPARATOR . $subDirectory;
        }

        return $this->directory;
    }
}
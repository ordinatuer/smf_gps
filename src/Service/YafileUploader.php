<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Esception\FileEsception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class YafileUploader
{
    private $directory;
    private $slugger;
    private $errorMessage;

    public function __construct(string $directory, SluggerInterface $slugger)
    {
        $this->directory = $directory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
    
        try {
            $file->move($this->getDirectory(), $newFilename);
        } catch(FileException $fe) {
            $errorMessage = $fe->getMessage();
        }

        return $newFilename;
    }

    public function getDirectory()
    {
        return $this->directory;
    }
}
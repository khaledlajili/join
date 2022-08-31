<?php

namespace App\service;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const TECHNICAL_TESTS = 'technical_tests';
    const TECHNICAL_TESTS_RESULTS = 'technical_tests_results';
    const PRE_REGISTRATION_FORM_RESULTS = 'pre_registration_form_results';
    const PROFILE_IMGS = 'profile_imgs';

    private Filesystem $publicUploadFileSystem;
    private Filesystem $privateUploadFileSystem;


    public function __construct(Filesystem $publicUploadFileSystem, Filesystem $privateUploadFileSystem)
    {
        $this->publicUploadFileSystem = $publicUploadFileSystem;
        $this->privateUploadFileSystem = $privateUploadFileSystem;
    }

    /**
     * @throws FilesystemException
     */
    public function uploadTechnicalTest(File $file, ?string $existingFilename = null): string
    {

        $newFilename = $this->uploadFile($file, self::TECHNICAL_TESTS, false);
        $filesystem=$this->privateUploadFileSystem;
        if ($existingFilename) {
            $filesystem->delete(self::TECHNICAL_TESTS . '/' . $existingFilename);
        }
        return $newFilename;
    }

    /**
     * @throws FilesystemException
     */
    public function uploadTechnicalTestResult(File $file): string
    {
        $newFilename = $this->uploadFile($file, self::TECHNICAL_TESTS_RESULTS, false);
        return $newFilename;
    }

    /**
     * @throws FilesystemException
     */
    public function uploadPreRegistrationFormResponse(File $file): string
    {
        $newFilename = $this->uploadFile($file, self::PRE_REGISTRATION_FORM_RESULTS, false);
        return $newFilename;
    }

    /**
     * @throws FilesystemException
     */
    public function uploadCandidateImg(File $file, ?string $existingFilename = null): string
    {

        $newFilename = $this->uploadFile($file, self::PROFILE_IMGS, true);
        $filesystem=$this->publicUploadFileSystem;
        if ($existingFilename) {
            $filesystem->delete(self::PROFILE_IMGS . '/' . $existingFilename);
        }
        return $newFilename;
    }

    /**
     * @throws FilesystemException
     */
    public function readStream(string $directory, bool $isPublic){
        $fileSystem = $isPublic ? $this->publicUploadFileSystem : $this->privateUploadFileSystem;
        $ressource=$fileSystem->readStream($directory);
        return $ressource;
    }

    /**
     * @throws FilesystemException
     */
    private function uploadFile(File $file, string $directory, bool $isPublic): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }
        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->guessExtension();
        $fileSystem = $isPublic ? $this->publicUploadFileSystem : $this->privateUploadFileSystem;

        $stream = fopen($file->getPathname(), 'r');
        $fileSystem->writeStream(
            $directory . '/' . $newFilename,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }
        return $newFilename;
    }
}
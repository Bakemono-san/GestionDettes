<?php

namespace App\Contracts;

interface UploadImageServiceInt{
    public function uploadImage($file);
    public function getImage($path);
}
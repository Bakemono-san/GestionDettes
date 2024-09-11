<?php

namespace App\Contracts;

interface QrCodeServiceInt{
    public function generateQrCode($data);
}
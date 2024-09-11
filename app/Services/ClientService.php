<?php

namespace App\Services;

use App\Contracts\ClientServiceInt;
use App\Events\asyncTransfertProcess;
use App\Events\ImageUploadEvent;
use App\Exceptions\ApiResponseException;
use App\Facades\ClientRepositoryFacade;
use App\Facades\PdfServiceFacade;
use App\Facades\QrCodeServiceFacade;
use App\Facades\UploadFileFacade;
use App\Facades\UserRepositoryFacade;
use App\Http\Requests\StoreClientRequest;
use App\Jobs\TestJob;
use App\Mail\SendMail;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use function Termwind\render;

class ClientService implements ClientServiceInt
{

    public function all($filters = [])
    {
        return ClientRepositoryFacade::all($filters);
    }
    public function find($id)
    {
        return ClientRepositoryFacade::find($id);
    }
    public function create(StoreClientRequest $request)
    {
        try {

            $clientData = $request->only('surname', 'adresse', 'telephone');

            DB::beginTransaction();

            $client = ClientRepositoryFacade::create($clientData);
            DB::commit();
            return $client;
        } catch (ApiResponseException $e) {
            DB::rollBack();
            return $e->getMessage();
        }



        // $photo = UploadFileFacade::withCloudinary($photo);
        // $pdf = PdfServiceFacade::generatePdf2($qrCode, $photoBase64, $userData);
        // Mail::to('ochatobake@gmail.com')->send(new SendMail($userData, $pdf));









        // try {
        //     DB::beginTransaction();

        //     $photoContents = file_get_contents($photo->getRealPath());
        //     $photoBase64 = 'data:image/png;base64,' . base64_encode($photoContents);
        //     $userData['photo'] = $photoBase64;


        //     // $image = '<img src="' . $photoBase64 . '" alt="Photo" style="width:200px;height:200px;size:cover" />';
        //     // $qrCode = QrCodeServiceFacade::generateQrCode($clientData);

        //     // $clientData['qrcode'] = $qrCode;
        //     // $photo = UploadFileFacade::withCloudinary($photo);
        //     // $userData['photo'] = $photo;

        //     // $pdf = PdfServiceFacade::generatePdf2($qrCode, $photoBase64, $userData);
        //     // echo $pdf;
        //     // Mail::to('ochatobake@gmail.com')->send(new SendMail($userData, $pdf));

        //     $client = ClientRepositoryFacade::create($clientData, $userData);
        //     DB::commit();
        //     return $client;
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return $e->getMessage();
        // }
    }

    public function update($id, array $data)
    {
        return ClientRepositoryFacade::update($id, $data);
    }
    public function delete($id)
    {
        return ClientRepositoryFacade::delete($id);
    }

    public function get($id)
    {
        return ClientRepositoryFacade::get($id);
    }

    public function dettes($id){
        return ClientRepositoryFacade::dettes($id);
    }

    public function getClientWithDebtswithArticle(){
        return ClientRepositoryFacade::getClientWithDebtswithArticle();
    }
}

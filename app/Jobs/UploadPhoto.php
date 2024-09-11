<?php

namespace App\Jobs;

use App\Facades\UploadFileFacade;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadPhoto implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    protected $user;
    protected $photo;
    public function __construct(User $user, $photo)
    {
        $this->user = $user;
        $this->photo = $photo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // $photo = Storage::get('public/'.$this->photo);

        $fullPath = storage_path('app/public/' . $this->photo);


        $photo = UploadFileFacade::withCloudinary($fullPath);
        $this->user->photo = $photo;
        $this->user->update(['photo' => $photo]);
        $this->user->save();
    }
}

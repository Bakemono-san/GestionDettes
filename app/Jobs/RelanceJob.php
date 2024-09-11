<?php

namespace App\Jobs;

use App\Facades\UploadFileFacade;
use App\Facades\UserRepositoryFacade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RelanceJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Dispatchable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $files = Storage::disk('public')->allFiles('relance');

        foreach ($files as $filePath) {
            try {
                // Get the filename (assuming it matches the user's login)
                $filename = pathinfo($filePath, PATHINFO_FILENAME);

                Log::debug('Processing file:', ['file' => $filePath]);

                // Find the user based on the filename
                $user = UserRepositoryFacade::findByLogin($filename);
                
                if ($user) {
                    // Get the absolute path to the file
                    $absoluteFilePath = Storage::disk('public')->path($filePath);

                    // Upload the file to Cloudinary
                    $cloudinaryUrl = UploadFileFacade::withCloudinary($absoluteFilePath);

                    if ($cloudinaryUrl) {
                        // Update the user's profile picture with the new Cloudinary URL
                        $user->update(['photo' => $cloudinaryUrl]);

                        // Delete the file from local storage after successful upload
                        Storage::disk('public')->delete($filePath);

                        Log::info('Updated user with file:', ['user_id' => $user->id, 'file' => $filePath]);
                    } else {
                        Log::warning('Cloudinary upload failed for file:', ['file' => $filePath]);
                    }
                } else {
                    Log::warning('No user found for file:', ['file' => $filePath]);
                }
            } catch (\Exception $e) {
                Log::error('Error processing file:', ['file' => $filePath, 'error' => $e->getMessage()]);
            }
        }
    }
}

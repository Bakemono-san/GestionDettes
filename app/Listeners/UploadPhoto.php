<?php

namespace App\Listeners;

    use App\Events\asyncTransfertProcess;
    use App\Jobs\UploadPhoto as JobsUploadPhoto;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Queue\InteractsWithQueue;

    class UploadPhoto
    {
        /**
         * Create the event listener.
         */
        public function __construct()
        {
            //
        }

        /**
         * Handle the event.
         */
        public function handle(asyncTransfertProcess $event): void
        {
            JobsUploadPhoto::dispatch($event->user, $event->photo);
        }
    }

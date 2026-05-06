<?php

namespace App\Jobs;

    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Support\Facades\Storage;
    use App\Models\Cv;

    class CleanupDeletedUserAccount implements ShouldQueue
    {
        use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        public $userId;
        public $rawAvatar;

        /**
         * Create a new job instance.
         */
        public function __construct(string $userId, ?string $rawAvatar)
        {
            $this->userId = $userId;
            $this->rawAvatar = $rawAvatar;
        }

        /**
         * Execute the job.
         */
        public function handle(): void
        {
            if ($this->rawAvatar) {
                Storage::disk('public')->delete($this->rawAvatar);
            }

            // Soft-delete all associated resumes
            Cv::where('user_id', $this->userId)->delete();
        }
    }

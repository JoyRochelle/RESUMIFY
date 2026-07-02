<?php

namespace App\Actions\Resumes;

use App\Exceptions\ResumeQuotaExceededException;
use App\Models\Cv;
use Illuminate\Support\Facades\DB;

class DuplicateResumeAction
{
    public function execute(Cv $resume): Cv
    {
        if (!$resume->user->canCreateResume()) {
            throw new ResumeQuotaExceededException();
        }

        $resume->loadMissing('sections');

        return DB::transaction(function () use ($resume) {
            $copy = $resume->replicate();
            $copy->user_id = $resume->user_id;
            $copy->title = $resume->title . ' (Copy)';
            $copy->save();

            foreach ($resume->sections as $section) {
                $newSection = $section->replicate();
                $newSection->cv_id = $copy->id;
                $newSection->save();
            }

            return $copy;
        });
    }
}

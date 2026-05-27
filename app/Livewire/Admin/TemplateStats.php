<?php

namespace App\Livewire\Admin;

use App\Models\CvTemplate;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class TemplateStats extends Component
{
    #[On('template-toggled')]
    #[On('template-deleted')]
    public function refresh(): void
    {
        // Triggers re-render with fresh DB counts
    }

    public function render(): View
    {
        return view('livewire.admin.template-stats', [
            'total'         => CvTemplate::count(),
            'activeCount'   => CvTemplate::where('is_active', true)->count(),
            'inactiveCount' => CvTemplate::where('is_active', false)->count(),
            'premiumCount'  => CvTemplate::where('is_premium', true)->count(),
        ]);
    }
}

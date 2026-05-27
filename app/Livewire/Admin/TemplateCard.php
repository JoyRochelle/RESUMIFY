<?php

namespace App\Livewire\Admin;

use App\Models\CvTemplate;
use Illuminate\View\View;
use Livewire\Component;

class TemplateCard extends Component
{
    public CvTemplate $template;

    public function toggle(): void
    {
        $this->template->update(['is_active' => !$this->template->is_active]);
        $this->dispatch('template-toggled');
    }

    public function delete(): void
    {
        if ($this->template->thumbnail_url) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->template->thumbnail_url);
        }

        $this->template->delete();

        $this->dispatch('template-deleted');
    }

    public function render(): View
    {
        return view('livewire.admin.template-card');
    }
}

<?php

namespace App\View\Components\Auth;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public string $name;
    public string $label;
    public string $type;
    public string $placeholder;
    public bool $required;
    public $value;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $label = '',
        string $type = 'text',
        string $placeholder = '',
        bool $required = false,
        $value = null
    ) {
        $this->name = $name;
        $this->label = $label ?: ucfirst($name);
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.auth.input');
    }
}

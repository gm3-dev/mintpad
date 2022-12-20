<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $position;
    public $addon;
    public $classes;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($position = 'right', $addon = false)
    {
        $this->position = $position;
        $this->addon = $addon;
        $this->classes = '';

        if ($this->addon && $this->position == 'right') {
            $this->classes = 'addon-right ';
        }
        if ($this->addon && $this->position == 'left') {
            $this->classes = 'addon-left ';
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.input');
    }
}

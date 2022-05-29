<?php

namespace FintechSystems\Payfast\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PayfastJetstreamReceipts extends Component
{
    public $user;

    public $receipts;

    public function mount()
    {
        $this->user = Auth::user();

        $this->receipts = $this->user->receipts;
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('vendor.payfast.components.payfast-jetstream-receipts');
    }
}

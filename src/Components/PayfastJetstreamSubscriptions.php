<?php

namespace FintechSystems\Payfast\Components;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use FintechSystems\Payfast\Facades\Payfast;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class PayfastJetstreamSubscriptions extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $confirmingCancelSubscription = false;

    public $displayingCreateSubscription = false;

    public $plan = 3;

    public $identifier;

    protected $listeners = [
        'billingUpdated' => '$refresh',
        'message' => '$refresh',
    ];
    
    public function confirmCancelSubscription()
    {
        $this->resetErrorBag();

        $this->password = '';

        $this->dispatchBrowserEvent('confirming-cancel-subscription');

        $this->confirmingCancelSubscription = true;
    }

    public function cancelSubscription() {
        ray(Auth::user()->subscriptions()->active()->first()->token);
        
        Payfast::cancelSubscription(Auth::user()->subscriptions()->active()->first()->token);
        
        $this->emit('billingUpdated');
    }

    /**
     * When the selected plan changes, refresh the PayFast identifier's signature
     */
    public function updatedPlan($planId)
    {        
        $this->plan = $planId;        
    }
    
    public function displayCreateSubscription()
    {        
        $this->identifier = Payfast::createOnsitePayment(
            (int) $this->plan,
            Carbon::now()->addDay()->format('Y-m-d'), // When to start recurring payments   
        );
     
        $this->displayingCreateSubscription = true;        
    }
    
    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        ray('Subscription component render is called');
        
        return view('vendor.payfast.components.payfast-jetstream-subscriptions');
    }
}

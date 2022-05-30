<?php

namespace FintechSystems\Payfast\Components;

use FintechSystems\Payfast\Facades\Payfast;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JetstreamSubscriptions extends Component
{
    public $user;

    public $confirmingCancelSubscription = false;

    public $displayingCreateSubscription = false;

    public $plan = 3;

    public $identifier;

    public $updateCardLink;

    protected $listeners = [
        'billingUpdated' => '$refresh',        
    ];

    public function confirmCancelSubscription()
    {
        $this->resetErrorBag();

        $this->password = '';

        $this->dispatchBrowserEvent('confirming-cancel-subscription');

        $this->confirmingCancelSubscription = true;
    }

    public function cancelSubscription()
    {
        ray($this->user->subscriptions()->active()->first()->token);

        Payfast::cancelSubscription(Auth::user()->subscriptions()->active()->first()->token);

        $this->emit('billingUpdated');

        $this->confirmingCancelSubscription = false;
    }

    /**
     * Update card
     */
    public function updateCard()
    {
        $token = $this->user->activeSubscription()->token;

        $url = "https://www.payfast.co.za/eng/recurring/update/$token?return=" . config('app.url') . "/user/profile?card_updated=true";

        ray($url);

        return redirect()->to($url);        
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
        if ($this->user->onGenericTrial()) {
            // TODO check if monthly or yearly before moving date forward
            $subscriptionStartsAt =  $this->user->trialEndsAt()->addMonth()->format('Y-m-d');
        }

        if ((null !== $this->user->activeSubscription()) && $this->user->activeSubscription()->onGracePeriod()) {
            $subscriptionStartsAt = $this->user->activeSubscription()->ends_at->addDay()->format('Y-m-d');
        }

        if (!isset($subscriptionStartsAt)) {
            $subscriptionStartsAt = \Carbon\Carbon::now()->format('Y-m-d');
        }

        $this->identifier = Payfast::createOnsitePayment(
            (int) $this->plan,
            $subscriptionStartsAt
        );

        $this->displayingCreateSubscription = true;
    }

    public function mount()
    {
        $this->user = Auth::user();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('vendor.payfast.components.jetstream-subscriptions');
    }
}

<?php

namespace FintechSystems\Payfast\Components;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use FintechSystems\Payfast\Facades\Payfast;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class PayfastJetstreamSubscriptions extends Component
{
    public $user;

    public $confirmingCancelSubscription = false;

    public $displayingCreateSubscription = false;

    public $plan = 3;

    public $identifier;

    protected $listeners = [
        'billingUpdated' => '$refresh',
        // 'message' => '$refresh',
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
     * When the selected plan changes, refresh the PayFast identifier's signature
     */
    public function updatedPlan($planId)
    {
        $this->plan = $planId;
    }

    public function displayCreateSubscription()
    {
        $subscriptionStartsAt =
            $this->user->onGenericTrial()
            ? $this->user->trialEndsAt()->addDay()->format('Y-m-d')
            : now()->format('Y-m-d');

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
        return view('vendor.payfast.components.payfast-jetstream-subscriptions');
    }
}

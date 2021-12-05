<?php

namespace FintechSystems\Payfast\Components;

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
    public $state = [];

    public $updateCard;

    public $createSubscription;
    
    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount()
    {
        $this->state = Auth::user()->withoutRelations()->toArray();
        
        $this->updateCard = Payfast::updateCardLink($this->state['credit_card_token']);

        $this->createSubscription = Payfast::createToken(5);
    }

    /**
     * Update the user's subscription information.
     *
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return void
     */
    public function updateSubscriptionInformation(UpdatesUserProfileInformation $updater)
    {
        $this->resetErrorBag();

        $updater->update(
            Auth::user(),
            $this->state
        );
        
        $this->emit('saved');

        $this->emit('refresh-navigation-menu');
    }
    
    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
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

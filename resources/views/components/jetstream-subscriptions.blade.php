<x-jet-action-section>
    <x-slot name="title">
        {{ __('Subscription Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('View/Update subscription information.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            
            @if ($user->onGenericTrial() && $user->subscriptions()->active()->count() == 0)
                <h3 class="text-lg font-medium text-gray-900">
                    You are current on trial till the {{ $user->trialEndsAt()->format('jS F Y') }}
                </h3>
                <div class="mt-3 max-w-xl text-sm text-gray-600">
                    <p>
                        If you subscribe now the first payment is due on the {{ $user->trialEndsAt()->addDay()->format('jS F Y'); }}.
                    </p>
                </div>
            @endif

            {{-- @if ($user->subscriptions()->onGracePeriod()->count() == 1) --}}
            @subscriptionGracePeriod
                <h3 class="text-lg font-medium text-gray-900">
                    Your subscription was cancelled on the {{ $user->subscriptions()->active()->first()->cancelled_at->format('Y-m-d') }} and is in the grace period.                    
                </h3>
                <div class="mt-3 max-w-xl text-sm text-gray-600">
                    <p>
                        The last day of your subscription is
                        {{ $user->subscriptions()->active()->first()->ends_at->format('Y-m-d') }}.
                    </p>
                </div>
            @endsubscriptionGracePeriod

            @if ($user->subscriptions()->active()->count() == 1 and $user->subscriptions()->onGracePeriod()->count() == 0)
                <h3 class="text-lg font-medium text-gray-900">
                    You are subscribed to the
                    {{ $user->subscriptions()->active()->first()->name }} plan.
                </h3>
                <div class="mt-3 max-w-xl text-sm text-gray-600">
                    <p>
                        The next payment is due
                        {{ $user->subscriptions()->active()->first()->next_bill_at->format('Y-m-d') }}.
                    </p>
                </div>
            @endif
            
            @if (!$user->onGenericTrial() && $user->subscriptions()->active()->count() == 0)
                <h3 class="text-lg font-medium text-gray-900">
                    You are not currently subscribed to any plan.
                </h3>
                <div class="mt-3 max-w-xl text-sm text-gray-600">
                    <p>
                        {{ __('Please select from our list of plans below') }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Subscription Action Buttons -->
        <div class="mt-5">            
            @if ($user->subscriptions()->active()->count() > 0 && $user->subscriptions()->onGracePeriod()->count() != 1)
                <x-jet-secondary-button
                    style="color: red;"
                    wire:click="confirmCancelSubscription"
                    wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>

                <x-jet-secondary-button
                    style="color: blue;"
                    wire:click="updateCard"
                    >
                    {{ __('Update Card') }}
                </x-jet-secondary-button>                                                
            @else
                <div class="flex">
                    <select
                        wire:model="plan"
                        name="plan"
                        class="mt-1 block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"                        
                        >
                        @foreach (config('payfast.plans') as $key => $value)
                            <option value="{{ $key }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>

                    <x-jet-secondary-button
                        class="ml-2 align-middle h-9 mt-2"
                        style="color: green;"
                        wire:click="displayCreateSubscription"
                        >
                        @subscriptionGracePeriod
                        {{ __('Resubscribe') }}
                        @else
                        {{ __('Subscribe') }}
                        @endsubscriptionGracePeriod
                    </x-jet-secondary-button>

                    <div wire:loading
                        class="ml-2 align-middle mt-3"
                    >
                        Please wait...
                    </div>

                </div>
            @endif

            
        </div>
        <!-- End Subscription Action Buttons -->
        
        <!-- Create Subscription Modal -->
        @if($displayingCreateSubscription)        
            <script>
                console.log('Launching Payfast onsite payment modal')

                window.payfast_do_onsite_payment({"uuid":"{{ $identifier }}"})
                
                console.log("Adding an event listener to 'message' for when it closes")
                
                window.addEventListener("message", refreshComponent);

            </script>
        @endif  

        @push('payfast-event-listener')
            <script>        
            const refreshComponent = () => {
                        console.log('Refreshing subscription status by emitting a billingUpdated event')
                        
                        Livewire.emit('billingUpdated')
                    }
            </script>
        @endpush

        <!-- Start Cancel Subscription Confirmation Modal -->
        <x-jet-dialog-modal wire:model="confirmingCancelSubscription">

            <x-slot name="title">
                {{ __('Cancel Subscription') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you want to cancel your Best Agent subscription? Once your subscription is cancelled, your Best Agent profile, rankings, and property listings will be hidden.') }}
                <br><br>
                {{ __('The last date until which your rankings and sold properties will show is the ') }}                
            </x-slot>

            <x-slot name="footer">
                <div wire:loading
                        class="mr-2 align-middle mt-3"
                    >
                        Please wait...
                </div>

                <x-jet-secondary-button wire:click="$toggle('confirmingCancelSubscription')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>

                <x-jet-danger-button class="ml-2" wire:click="cancelSubscription" wire:loading.attr="disabled">
                    {{ __('Cancel Subscription') }}
                </x-jet-danger-button>
            </x-slot>

        </x-jet-dialog-modal>
        <!-- End Cancel Subscription Confirmation Modal -->
                
    </x-slot>
</x-jet-action-section>

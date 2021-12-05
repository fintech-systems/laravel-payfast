<x-jet-form-section submit="updateSubscriptionInformation">
    <x-slot name="title">
        {{ __('Subscription Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your subscription and credit card information.') }}
    </x-slot>

    <x-slot name="form">        
        <!-- Credit Card Token -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="credit_card_token" value="{{ __('Credit Card Token') }}" />
            <x-jet-input id="credit_card_token" type="text" class="mt-1 block w-full" wire:model.defer="state.credit_card_token" autocomplete="credit_card_token" />
            <x-jet-input-error for="credit_card_token" class="mt-2" />

            {!! $updateCard !!}

            <div wire:ignore>
            {!! $createSubscription !!}
            </div>
            
        </div>        
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled" wire:target="credit_card_token">
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>

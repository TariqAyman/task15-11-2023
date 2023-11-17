<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Payment Information') }}
                            </h2>
                        </header>

                        <form method="post" action="{{ $edit ? route('payments.update',$payment->id) : route('payments.store') }}" class="mt-6 space-y-6">
                            @csrf
                            @method($edit ? 'patch' : 'post')

                            <div>
                                <x-input-label for="transaction_id" :value="__('Transaction ID')"/>
                                <x-text-input id="transaction_id" name="transaction_id" type="text" step="1" class="mt-1 block w-full"
                                              :disabled="request()->has('transaction_id')"
                                              :value="$edit ? $payment->transaction_id : old('transaction_id',request()->get('transaction_id'))" required autofocus/>
                                <x-input-error class="mt-2" :messages="$errors->get('transaction_id')"/>
                            </div>

                            <div>
                                <x-input-label for="amount" :value="__('Amount')"/>
                                <x-text-input id="amount" name="amount" type="number" step="0.001" class="mt-1 block w-full" :value="$edit ? $payment->amount : old('amount',0.0)" required autofocus/>
                                <x-input-error class="mt-2" :messages="$errors->get('amount')"/>
                            </div>

                            <div>
                                <x-input-label for="paid_on" :value="__('Paid On')"/>
                                <x-text-input id="paid_on" name="paid_on" type="date" class="mt-1 block w-full" :value="$edit ? $payment->paid_on->format('Y-m-d') : old('paid_on', \Carbon\Carbon::now()->format('Y-m-d') )" required autofocus/>
                                <x-input-error class="mt-2" :messages="$errors->get('due_on')"/>
                            </div>


                            <div>
                                <x-input-label for="Details" :value="__('Details')"/>
                                <x-text-input id="details" name="details" type="text" class="mt-1 block w-full" :value="$edit ? $payment->details : old('details')" required autofocus/>
                                <x-input-error class="mt-2" :messages="$errors->get('details')"/>
                            </div>


                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                        <br>
                        <div class="flex items-center ">
                            @if($edit)
                                <div>
                                    <x-button-delete :action="route('payments.destroy',$payment->id)"/>
                                </div>
                            @endif
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

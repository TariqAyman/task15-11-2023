<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Transactions Information') }}
                            </h2>
                        </header>

                        <form method="post" action="{{ $edit ? route('transactions.update',$transaction->id) : route('transactions.store') }}" class="mt-6 space-y-6">
                            @csrf
                            @method($edit ? 'patch' : 'post')

                            @if($edit)
                                <div>
                                    <x-input-label for="id" :value="__('ID')"/>
                                    <x-text-input disabled id="id" name="id" type="text" class="mt-1 block w-full" :value="$transaction->id"/>
                                </div>
                            @endif

                            <div>
                                <x-input-label for="user_type" :value="__('Select a User')"/>
                                <label>
                                    <select required name="payer_id" class="mt-1 block w-full">
                                        <option value=""> Select a User</option>
                                        @foreach($users as $id => $name)
                                            <option value="{{ $id }}" {{ $edit && $transaction->payer_id == $id ? 'selected' : ''  }}> {{ $name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            <div>
                                <x-input-label for="amount" :value="__('Amount')"/>
                                <x-text-input id="amount" name="amount" type="number" step="0.001" class="mt-1 block w-full" :value="$edit ? $transaction->amount : old('amount',0.0)" required autofocus/>
                                <x-input-error class="mt-2" :messages="$errors->get('amount')"/>
                            </div>

                            <div>
                                <x-input-label for="vat" :value="__('vat')"/>
                                <x-text-input id="vat" name="vat" type="number" step="0.001" class="mt-1 block w-full" :value="$edit ? $transaction->vat : old('vat',0.0)" required autofocus/>
                                <x-input-error class="mt-2" :messages="$errors->get('vat')"/>
                            </div>

                            <div>
                                <x-input-label for="due_on" :value="__('Due On')"/>
                                <x-text-input id="due_on" name="due_on" type="date" class="mt-1 block w-full" :value="$edit ? $transaction->due_on->format('Y-m-d') : old('due_on',0.0)" required autofocus/>
                                <x-input-error class="mt-2" :messages="$errors->get('due_on')"/>
                            </div>

                            <div>
                                <x-input-label for="is_vat_inclusive" :value="__('Is VAT Inclusive?')"/>
                                <label>
                                    <select required name="is_vat_inclusive" class="mt-1 block w-full">
                                        <option value="1" {{ $edit && $transaction->is_vat_inclusive == 1 ? 'selected' : '' }}>True</option>
                                        <option value="0" {{ $edit && $transaction->is_vat_inclusive == 0 ? 'selected' : '' }}>No</option>

                                    </select>
                                </label>
                            </div>

                            <div>
                                <x-input-label for="total_paid_amount" :value="__('Total Paid Amount')"/>
                                <x-text-input disabled id="total_paid_amount" name="total_paid_amount" type="number" class="mt-1 block w-full" :value="$edit ? $transaction->total_paid_amount : old('total_paid_amount',0.0)" required autofocus/>
                                <x-input-error class="mt-2" :messages="$errors->get('total_paid_amount')"/>
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('status')"/>
                                <x-text-input disabled id="status" name="status" type="text" class="mt-1 block w-full" :value="$edit ? $transaction->status : null"/>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                        <br>
                        <div class="flex items-center ">
                            @if($edit)
                                <div>
                                    <x-button-delete :action="route('transactions.destroy',$transaction->id)"/>
                                </div>
                            @endif
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>

    @if($edit)
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('List Payments') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <x-primary-button onclick="location='{{ route('payments.create',['transaction_id' => $transaction->id]) }}'" target="_parent">
                    {{ __('Add new payment') }}
                </x-primary-button>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <x-table-simple
                            :columns='[
                            ["name" => "ID", "field" => "id"],
                            ["name" => "Created By", "field" => "created_by"],
                            ["name" => "Amount","field" => "amount"],
                            ["name" => "Paid On","field" => "paid_on"],
                            ["name" => "Details","field" => "details"],
		                ]'
                            :rows="collect($transaction->payments)->all() ?? []">

                        <x-slot name="tableActions">
                            <div class="flex flex-wrap space-x-4">
                                <a :href="`payments/${row.id}/edit`" class="font-semibold text-xl leading-tight">Edit</a>
                            </div>
                        </x-slot>

                    </x-table-simple>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>

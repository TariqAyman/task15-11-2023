<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List Payments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-primary-button onclick="location='{{ route('payments.create') }}'" target="_parent">
                {{ __('Add new') }}
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
                    :rows="collect($payments)->all()['data'] ?? []">

                    <x-slot name="tableActions">
                        <div class="flex flex-wrap space-x-4">
                            <a :href="`transactions/${row.transaction_id}/edit`" class="font-semibold text-xl leading-tight">View Translation</a>
                            |
                            <a :href="`payments/${row.id}/edit`" class="font-semibold text-xl leading-tight">Edit</a>
                        </div>
                    </x-slot>

                </x-table-simple>
            </div>
            {{ $payments->links() }}
        </div>
    </div>

</x-app-layout>

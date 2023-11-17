<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List Transactions') }}
        </h2>

    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="max-w-10xl mx-auto sm:px-6 lg:px-8 space-y-6">
                @if(auth()->user()->user_type == 'admin')
                    <x-primary-button onclick="location='{{ route('transactions.create') }}'" target="_parent">
                        {{ __('Add new') }}
                    </x-primary-button>
                @endif
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <x-table-simple
                        :columns='[
                            ["name" => "ID", "field" => "id"],
                            ["name" => "Payer",	"field" => "payer"],
                            ["name" => "Created by","field" => "created_by"],
                            ["name" => "Amount","field" => "amount"],
                            ["name" => "Due on","field" => "due_on"],
                            ["name" => "VAT %","field" => "vat"],
                            ["name" => "Is VAT inclusive","field" => "is_vat_inclusive"],
                            ["name" => "Status","field" => "status"],
                            ["name" => "Total Paid Amount","field" => "total_paid_amount"],
		                ]'
                        :rows="collect($transactions)->all()['data'] ?? []">
                        <x-slot name="tableActions">
                            <div class="flex flex-wrap space-x-4">
                                <a :href="`transactions/${row.id}/edit`" class="font-semibold text-xl leading-tight">Edit</a>
                            </div>
                        </x-slot>

                    </x-table-simple>
                </div>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

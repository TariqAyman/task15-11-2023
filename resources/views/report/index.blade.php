<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Report') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form action="{{ route('reports.index') }}" method='GET' class="mt-6 space-y-6">
                <div>
                    <x-input-label for="start_date" :value="__('Start Date')"/>
                    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block" :value="old('start_date', request()->get('start_date'))" autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('start_date')"/>
                </div>
                <div>
                    <x-input-label for="end_date" :value="__('End Date')"/>
                    <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block" :value="old('end_date', request()->get('end_date'))" required autofocus/>
                    <x-input-error class="mt-2" :messages="$errors->get('end_date')"/>
                </div>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </form>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <x-table-simple
                        :columns='[
                            ["name" => "year", "field" => "year"],
                            ["name" => "month",	"field" => "month"],
                            ["name" => "unpaid","field" => "unpaid"],
                            ["name" => "paid","field" => "paid"],
                            ["name" => "outstanding","field" => "outstanding"],
                            ["name" => "overdue","field" => "overdue"],
	                ]'
                        :rows="collect($report)->all() ?? []">
                </x-table-simple>
            </div>
        </div>
    </div>
</x-app-layout>

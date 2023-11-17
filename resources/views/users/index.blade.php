<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-primary-button onclick="location='{{ route('users.create') }}'" target="_parent">
                {{ __('Add new') }}
            </x-primary-button>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <x-table-simple
                    :columns='[
                            ["name" => "ID", "field" => "id"],
                            ["name" => "name",	"field" => "name"],
                            ["name" => "Email","field" => "email"],
                            ["name" => "User Type","field" => "user_type"]
	                ]'
                    :rows="collect($users)->all()['data'] ?? []">
                    <x-slot name="tableActions">
                        <div class="flex flex-wrap space-x-4">
                            <a :href="`users/${row.id}/edit`" class="font-semibold text-xl leading-tight">Edit</a>
                        </div>
                    </x-slot>
                </x-table-simple>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>

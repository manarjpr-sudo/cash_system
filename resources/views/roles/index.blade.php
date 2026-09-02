<x-slot name="header">

    <div class="flex justify-between items-center">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Roles
        </h2>

        <a
            href="{{ route('roles.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

            + New Role

        </a>

    </div>

</x-slot>


<div class="py-8">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded-lg p-6">


            {{-- Success Message --}}

            @if(session('success'))

                <div class="mb-5 bg-green-100 text-green-700 px-4 py-3 rounded">

                    {{ session('success') }}

                </div>

            @endif


            <div class="flex justify-between items-center mb-5">

                <h3 class="text-lg font-bold">
                    Roles List
                </h3>

            </div>


            {{-- Roles Table --}}

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="border-b bg-gray-100">

                        <tr>

                            <th class="py-3 px-3 text-left">
                                #
                            </th>

                            <th class="py-3 px-3 text-left">
                                Name
                            </th>

                            <th class="py-3 px-3 text-left">
                                Description
                            </th>

                            <th class="py-3 px-3 text-left">
                                Users
                            </th>

                            <th class="py-3 px-3 text-left">
                                Permissions
                            </th>

                            <th class="py-3 px-3 text-left">
                                Created
                            </th>

                            <th class="py-3 px-3 text-center">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($roles as $role)

                        <tr class="border-b hover:bg-gray-50">

                            {{-- ID --}}

                            <td class="px-3 py-3">
                                {{ $role->id }}
                            </td>


                            {{-- Name --}}

                            <td class="px-3 py-3 font-medium">

                                {{ ucfirst($role->name) }}

                            </td>


                            {{-- Description --}}

                            <td class="px-3 py-3 text-gray-600">

                                {{ $role->description ?? '-' }}

                            </td>


                            {{-- Users Count --}}

                            <td class="px-3 py-3">

                                <span class="px-2 py-1 rounded bg-blue-100 text-blue-700">

                                    {{ $role->users_count }}

                                </span>

                            </td>


                            {{-- Permissions --}}

                            <td class="px-3 py-3">

                                @forelse($role->permissions as $permission)

                                    <span
                                        class="inline-block px-2 py-1 mr-1 mb-1 rounded bg-gray-100 text-gray-700 text-sm">

                                        {{ $permission->name }}

                                    </span>

                                @empty

                                    <span class="text-gray-500">
                                        No Permissions
                                    </span>

                                @endforelse

                            </td>


                            {{-- Created --}}

                            <td class="px-3 py-3">

                                {{ $role->created_at?->format('Y-m-d H:i') }}

                            </td>


                            {{-- Actions --}}

                            <td class="px-3 py-3 text-center">

                                <a
                                    href="{{ route('roles.edit', $role) }}"
                                    class="text-blue-600 hover:underline">

                                    Edit

                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-8 text-gray-500">

                                No roles found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}

            <div class="mt-6">

                {{ $roles->links() }}

            </div>


        </div>

    </div>

</div>

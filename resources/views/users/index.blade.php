<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

```
<div class="bg-white shadow rounded-lg p-6">

    <div class="flex justify-between items-center mb-5">

        <h3 class="text-lg font-bold">
            Users List
        </h3>

        <a
            href="{{ route('users.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

            + New User

        </a>

    </div>

    @if(session('success'))

        <div class="mb-5 bg-green-100 text-green-700 px-4 py-3 rounded">

            {{ session('success') }}

        </div>

    @endif


    {{-- Search & Filters --}}

    <form method="GET"
          action="{{ route('users.index') }}"
          class="mb-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Search --}}

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name or email..."
                class="border rounded px-3 py-2">


            {{-- Status --}}

            <select
                name="status"
                class="border rounded px-3 py-2">

                <option value="">
                    All Statuses
                </option>

                <option value="active"
                    {{ request('status') === 'active' ? 'selected' : '' }}>
                    Active
                </option>

                <option value="inactive"
                    {{ request('status') === 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>

            </select>


            {{-- Role --}}

            <select name="role_id"
                    class="border rounded px-3 py-2">

                <option value="">
                    All Roles
                </option>

                @foreach($roles as $role)

                    <option value="{{ $role->id }}"
                        {{ request('role_id') == $role->id ? 'selected' : '' }}>

                        {{ ucfirst($role->name) }}

                    </option>

                @endforeach

            </select>


            {{-- Search Button --}}

            <button
                type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded">

                Search / Filter

            </button>

        </div>


        <div class="mt-4">

            <a
                href="{{ route('users.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded">

                Reset

            </a>

        </div>

    </form>


    {{-- Users Table --}}

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
                        Email
                    </th>

                    <th class="py-3 px-3 text-left">
                        Role
                    </th>

                    <th class="py-3 px-3 text-left">
                        Status
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

            @forelse($users as $user)

                <tr class="border-b hover:bg-gray-50">

                    <td class="px-3 py-3">
                        {{ $user->id }}
                    </td>


                    <td class="px-3 py-3 font-medium">
                        {{ $user->name }}
                    </td>


                    <td class="px-3 py-3">
                        {{ $user->email }}
                    </td>


                    <td class="px-3 py-3">

                        @if($user->role)

                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-700">

                                {{ ucfirst($user->role->name) }}

                            </span>

                        @else

                            <span class="text-gray-500">
                                No Role
                            </span>

                        @endif

                    </td>


                    <td class="px-3 py-3">

                        @if($user->status === 'active')

                            <span class="px-2 py-1 rounded bg-green-100 text-green-700">
                                Active
                            </span>

                        @else

                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700">
                                Inactive
                            </span>

                        @endif

                    </td>


                    <td class="px-3 py-3">
                        {{ $user->created_at?->format('Y-m-d H:i') }}
                    </td>


                    <td class="px-3 py-3 text-center">

                        <a
                            href="{{ route('users.edit', $user) }}"
                            class="text-blue-600 hover:underline mr-3">

                            Edit

                        </a>


                        @if($user->status !== 'active')

                            <form
                                method="POST"
                                action="{{ route('users.approve', $user) }}"
                                class="inline">

                                @csrf

                                <button
                                    type="submit"
                                    class="text-green-600 hover:underline">

                                    Activate

                                </button>

                            </form>

                        @else

                            <form
                                method="POST"
                                action="{{ route('users.deactivate', $user) }}"
                                class="inline">

                                @csrf

                                <button
                                    type="submit"
                                    class="text-red-600 hover:underline">

                                    Deactivate

                                </button>

                            </form>

                        @endif

                    </td>


                </tr>


            @empty

                <tr>

                    <td
                        colspan="7"
                        class="text-center py-8 text-gray-500">

                        No users found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}

    <div class="mt-6">

        {{ $users->links() }}

    </div>

</div>
```

</div>

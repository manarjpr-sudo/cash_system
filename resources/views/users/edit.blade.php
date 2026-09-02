<x-slot name="header">

    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit User
    </h2>

</x-slot>


<div class="py-8">

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded-lg p-6">

            {{-- Validation Errors --}}

            @if($errors->any())

                <div class="mb-6 bg-red-100 text-red-700 px-4 py-3 rounded">

                    <ul class="list-disc list-inside">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form method="POST"
                  action="{{ route('users.update', $user) }}">

                @csrf
                @method('PUT')


                {{-- Name --}}

                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full border rounded px-3 py-2">

                </div>


                {{-- Email --}}

                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full border rounded px-3 py-2">

                </div>


                {{-- Password --}}

                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        New Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="w-full border rounded px-3 py-2">

                    <p class="text-sm text-gray-500 mt-1">
                        Leave blank to keep the current password.
                    </p>

                </div>


                {{-- Confirm Password --}}

                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full border rounded px-3 py-2">

                </div>


                {{-- Role --}}

                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Role
                    </label>

                    <select
                        name="role_id"
                        required
                        class="w-full border rounded px-3 py-2">

                        <option value="">
                            Select Role
                        </option>

                        @foreach($roles as $role)

                            <option
                                value="{{ $role->id }}"
                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>

                                {{ ucfirst($role->name) }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Status --}}

                <div class="mb-6">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>

                    <select
                        name="status"
                        required
                        class="w-full border rounded px-3 py-2">

                        <option
                            value="active"
                            {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>

                            Active

                        </option>

                        <option
                            value="inactive"
                            {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>

                            Inactive

                        </option>

                    </select>

                </div>


                {{-- Actions --}}

                <div class="flex gap-3">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                        Update User

                    </button>


                    <a
                        href="{{ route('users.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded">

                        Cancel

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

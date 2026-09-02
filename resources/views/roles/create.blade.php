<x-slot name="header">

    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Create Role
    </h2>

</x-slot>


<div class="py-8">

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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


            <form method="POST" action="{{ route('roles.store') }}">

                @csrf


                {{-- Role Name --}}

                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Role Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g. Manager"
                        required
                        class="w-full border rounded px-3 py-2">

                </div>


                {{-- Description --}}

                <div class="mb-6">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="3"
                        placeholder="Describe this role..."
                        class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>

                </div>


                {{-- Permissions --}}

                <div class="mb-6">

                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Permissions
                    </label>


                    <div class="border rounded-lg p-4">

                        @forelse($permissions as $permission)

                            <label class="flex items-center gap-3 mb-3">

                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300">

                                <span>

                                    <span class="font-medium">
                                        {{ $permission->name }}
                                    </span>

                                    @if($permission->description)

                                        <span class="block text-sm text-gray-500">
                                            {{ $permission->description }}
                                        </span>

                                    @endif

                                </span>

                            </label>

                        @empty

                            <p class="text-gray-500">
                                No permissions available.
                            </p>

                        @endforelse

                    </div>

                </div>


                {{-- Actions --}}

                <div class="flex gap-3">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                        Create Role

                    </button>


                    <a
                        href="{{ route('roles.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded">

                        Cancel

                    </a>

                </div>


            </form>

        </div>

    </div>

</div>


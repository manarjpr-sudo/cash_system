<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Customer
        </h2>
    </x-slot>


    <div class="py-12">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('customers.store') }}">

                    @csrf

                    <div class="mb-4">
                        <label class="block mb-2">
                            Name
                        </label>

                        <input 
                            type="text"
                            name="name"
                            class="border rounded w-full p-2"
                            required>
                    </div>


                    <div class="mb-4">
                        <label class="block mb-2">
                            Phone
                        </label>

                        <input 
                            type="text"
                            name="phone"
                            class="border rounded w-full p-2">
                    </div>


                    <div class="mb-4">
                        <label class="block mb-2">
                            Identity Number
                        </label>

                        <input 
                            type="text"
                            name="identity_number"
                            class="border rounded w-full p-2">
                    </div>


                    <div class="mb-4">
                        <label class="block mb-2">
                            Room Number
                        </label>

                        <input 
                            type="text"
                            name="room_number"
                            class="border rounded w-full p-2">
                    </div>


                    <div class="mb-4">
                        <label class="block mb-2">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            class="border rounded w-full p-2"></textarea>
                    </div>


                    <button 
                        type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                        Save Customer
                    </button>


                </form>

            </div>

        </div>

    </div>

</x-app-layout>
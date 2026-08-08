<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Customer
        </h2>
    </x-slot>


    <div class="py-12">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">


                <form method="POST" action="{{ route('customers.update', $customer) }}">

                    @csrf
                    @method('PUT')


                    <div class="mb-4">

                        <label class="block mb-2">
                            Name
                        </label>

                        <input 
                            type="text"
                            name="name"
                            value="{{ $customer->name }}"
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
                            value="{{ $customer->phone }}"
                            class="border rounded w-full p-2">

                    </div>


                    <div class="mb-4">

                        <label class="block mb-2">
                            Identity Number
                        </label>

                        <input 
                            type="text"
                            name="identity_number"
                            value="{{ $customer->identity_number }}"
                            class="border rounded w-full p-2">

                    </div>


                    <div class="mb-4">

                        <label class="block mb-2">
                            Room Number
                        </label>

                        <input 
                            type="text"
                            name="room_number"
                            value="{{ $customer->room_number }}"
                            class="border rounded w-full p-2">

                    </div>


                    <div class="mb-4">

                        <label class="block mb-2">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            class="border rounded w-full p-2">{{ $customer->notes }}</textarea>

                    </div>


                    <button 
                        type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded">

                        Update Customer

                    </button>


                </form>


            </div>

        </div>

    </div>

</x-app-layout>
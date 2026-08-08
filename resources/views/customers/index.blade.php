<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customers
        </h2>
    </x-slot>


    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">


                <div class="flex justify-between items-center mb-4">

                    <h3 class="text-lg font-bold">
                        Customers List
                    </h3>


                    <a href="{{ route('customers.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded">
                        Add Customer
                    </a>

                </div>


                <table class="w-full">

                    <thead>

                        <tr class="border-b">

                            <th class="py-2">ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Room</th>
                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>


                    @foreach($customers as $customer)

                        <tr class="border-b">

                            <td class="py-2">
                                {{ $customer->id }}
                            </td>


                            <td>
                                {{ $customer->name }}
                            </td>


                            <td>
                                {{ $customer->phone }}
                            </td>


                            <td>
                                {{ $customer->room_number }}
                            </td>


                            <td>

                                <a href="{{ route('customers.edit',$customer) }}"
                                   class="text-blue-600">
                                    Edit
                                </a>


                                <form method="POST"
                                      action="{{ route('customers.destroy',$customer) }}"
                                      class="inline">

                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-600 ml-3">
                                        Delete
                                    </button>

                                </form>

                            </td>


                        </tr>


                    @endforeach


                    </tbody>

                </table>


                <div class="mt-4">
                    {{ $customers->links() }}
                </div>


            </div>

        </div>

    </div>

</x-app-layout>
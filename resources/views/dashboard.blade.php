<x-app-layout>

```
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Cash System Dashboard
    </h2>
</x-slot>


<div class="py-12">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


        {{-- Quick Actions --}}

        <div class="flex flex-wrap gap-3 mb-8">

            <a href="{{ route('operations.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                + New Operation

            </a>


            <a href="{{ route('operations.index') }}"
               class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded">

                View Operations

            </a>


            <a href="{{ route('customers.index') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded">

                View Customers

            </a>


            <a href="{{ route('customers.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded">

                + New Customer

            </a>

        </div>


        {{-- Statistics Cards --}}

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">


            <a href="{{ route('operations.index') }}"
               class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">

                <h3 class="text-gray-500">
                    Users
                </h3>

                <p class="text-3xl font-bold text-gray-800">
                    {{ $totalUsers }}
                </p>

            </a>


            <a href="{{ route('customers.index') }}"
               class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">

                <h3 class="text-gray-500">
                    Customers
                </h3>

                <p class="text-3xl font-bold text-gray-800">
                    {{ $totalCustomers }}
                </p>

            </a>


            <a href="{{ route('operations.index') }}"
               class="bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">

                <h3 class="text-gray-500">
                    Operations
                </h3>

                <p class="text-3xl font-bold text-gray-800">
                    {{ $totalOperations }}
                </p>

            </a>


            <div class="bg-white shadow-sm rounded-lg p-6">

                <h3 class="text-gray-500">
                    Transactions
                </h3>

                <p class="text-3xl font-bold text-blue-600">
                    {{ $totalTransactions }}
                </p>

            </div>


            <a href="{{ route('operations.index', ['status' => 'pending']) }}"
               class="bg-yellow-50 shadow-sm rounded-lg p-6 hover:shadow-md transition">

                <h3 class="text-yellow-700">
                    Pending
                </h3>

                <p class="text-3xl font-bold text-yellow-800">
                    {{ $pendingOperations }}
                </p>

            </a>


            <a href="{{ route('operations.index', ['status' => 'approved']) }}"
               class="bg-green-50 shadow-sm rounded-lg p-6 hover:shadow-md transition">

                <h3 class="text-green-700">
                    Approved
                </h3>

                <p class="text-3xl font-bold text-green-800">
                    {{ $approvedOperations }}
                </p>

            </a>


            <a href="{{ route('operations.index', ['status' => 'rejected']) }}"
               class="bg-red-50 shadow-sm rounded-lg p-6 hover:shadow-md transition">

                <h3 class="text-red-700">
                    Rejected
                </h3>

                <p class="text-3xl font-bold text-red-800">
                    {{ $rejectedOperations }}
                </p>

            </a>


            <a href="{{ route('operations.index') }}"
               class="bg-blue-50 shadow-sm rounded-lg p-6 hover:shadow-md transition">

                <h3 class="text-blue-700">
                    Total Amount
                </h3>

                <p class="text-3xl font-bold text-blue-800">
                    {{ number_format($totalAmount, 2) }}
                </p>

            </a>

        </div>


        {{-- Latest Transactions --}}

        <div class="bg-white shadow-sm rounded-lg p-6">

            <div class="flex justify-between items-center mb-5">

                <h3 class="text-lg font-bold">
                    Latest Transactions
                </h3>

                <a href="{{ route('operations.index') }}"
                   class="text-blue-600 hover:underline">

                    View Operations

                </a>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-left">

                    <thead>

                        <tr class="border-b">

                            <th class="py-3">
                                Type
                            </th>

                            <th class="py-3">
                                Customer
                            </th>

                            <th class="py-3">
                                Amount
                            </th>

                            <th class="py-3">
                                User
                            </th>

                            <th class="py-3">
                                Date
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($latestTransactions as $transaction)

                        <tr class="border-b">

                            <td class="py-3">
                                {{ ucfirst($transaction->type) }}
                            </td>

                            <td class="py-3">
                                {{ $transaction->customer?->name ?? '-' }}
                            </td>

                            <td class="py-3">
                                {{ number_format($transaction->amount, 2) }}
                            </td>

                            <td class="py-3">
                                {{ $transaction->user?->name ?? '-' }}
                            </td>

                            <td class="py-3">
                                {{ $transaction->created_at?->format('Y-m-d') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="py-8 text-center text-gray-500">

                                No transactions found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>


    </div>

</div>
```

</x-app-layout>

<x-slot name="header">
    <div class="flex justify-between items-center">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Operations
        </h2>

        @if(Auth::user()->role?->permissions->contains('name', 'create_operations'))
            <a href="{{ route('operations.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                + New Operation
            </a>
        @endif

    </div>
</x-slot>


<div class="py-8">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded-lg overflow-hidden">

            <div class="p-6">

                <h3 class="text-lg font-bold mb-5">
                    Operations List
                </h3>


                {{-- Search & Filters --}}

                <form method="GET"
                      action="{{ route('operations.index') }}"
                      class="mb-6">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        {{-- Search --}}

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search..."
                            class="border rounded px-3 py-2">


                        {{-- Status --}}

                        <select
                            name="status"
                            class="border rounded px-3 py-2">

                            <option value="">
                                All Statuses
                            </option>

                            <option value="pending"
                                {{ request('status') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="approved"
                                {{ request('status') == 'approved' ? 'selected' : '' }}>
                                Approved
                            </option>

                            <option value="rejected"
                                {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                            <option value="completed"
                                {{ request('status') == 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>

                            <option value="cancelled"
                                {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>


                        {{-- Type --}}

                        <select
                            name="type"
                            class="border rounded px-3 py-2">

                            <option value="">
                                All Types
                            </option>

                            <option value="deposit"
                                {{ request('type') == 'deposit' ? 'selected' : '' }}>
                                Deposit
                            </option>

                            <option value="withdraw"
                                {{ request('type') == 'withdraw' ? 'selected' : '' }}>
                                Withdraw
                            </option>

                        </select>


                        {{-- Customer --}}

                        <select
                            name="customer_id"
                            class="border rounded px-3 py-2">

                            <option value="">
                                All Customers
                            </option>

                            @foreach($customers as $customer)

                                <option
                                    value="{{ $customer->id }}"
                                    {{ request('customer_id') == $customer->id ? 'selected' : '' }}>

                                    {{ $customer->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="mt-4 flex gap-2">

                        <button
                            type="submit"
                            class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded">

                            Search / Filter

                        </button>


                        <a
                            href="{{ route('operations.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded">

                            Reset

                        </a>

                    </div>

                </form>


                {{-- Operations Table --}}

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="border-b bg-gray-100">

                            <tr>

                                <th class="py-3 px-3 text-left">
                                    #
                                </th>

                                <th class="py-3 px-3 text-left">
                                    Customer
                                </th>

                                <th class="py-3 px-3 text-left">
                                    Type
                                </th>

                                <th class="py-3 px-3 text-left">
                                    Amount
                                </th>

                                <th class="py-3 px-3 text-left">
                                    Status
                                </th>

                                <th class="py-3 px-3 text-left">
                                    User
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

                        @forelse($operations as $operation)

                            <tr class="border-b hover:bg-gray-50">

                                <td class="px-3 py-3">
                                    {{ $operation->id }}
                                </td>

                                <td class="px-3 py-3">
                                    {{ $operation->customer?->name ?? '-' }}
                                </td>

                                <td class="px-3 py-3">
                                    {{ ucfirst($operation->type) }}
                                </td>

                                <td class="px-3 py-3 font-semibold">
                                    {{ number_format($operation->amount, 2) }}
                                </td>

                                <td class="px-3 py-3">

                                    @switch($operation->status)

                                        @case('approved')

                                            <span class="px-2 py-1 rounded bg-green-100 text-green-700">
                                                Approved
                                            </span>

                                            @break

                                        @case('rejected')

                                            <span class="px-2 py-1 rounded bg-red-100 text-red-700">
                                                Rejected
                                            </span>

                                            @break

                                        @case('completed')

                                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-700">
                                                Completed
                                            </span>

                                            @break

                                        @case('cancelled')

                                            <span class="px-2 py-1 rounded bg-gray-200 text-gray-700">
                                                Cancelled
                                            </span>

                                            @break

                                        @default

                                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700">
                                                Pending
                                            </span>

                                    @endswitch

                                </td>

                                <td class="px-3 py-3">
                                    {{ $operation->user?->name ?? '-' }}
                                </td>

                                <td class="px-3 py-3">
                                    {{ $operation->created_at?->format('Y-m-d H:i') }}
                                </td>

                                <td class="px-3 py-3 text-center space-x-2">

                                    {{-- View --}}

                                    <a
                                        href="{{ route('operations.show', $operation) }}"
                                        class="text-blue-600 hover:underline">

                                        View

                                    </a>


                                    {{-- Approval Actions --}}

                                    @if(
                                        $operation->status === 'pending' &&
                                        Auth::user()->role?->permissions->contains('name', 'approve_operations')
                                    )

                                        <form
                                            method="POST"
                                            action="{{ route('operations.approve', $operation) }}"
                                            class="inline">

                                            @csrf

                                            <button
                                                type="submit"
                                                class="text-green-600 hover:underline">

                                                Approve

                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            action="{{ route('operations.reject', $operation) }}"
                                            class="inline">

                                            @csrf

                                            <button
                                                type="submit"
                                                class="text-red-600 hover:underline">

                                                Reject

                                            </button>

                                        </form>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center py-8 text-gray-500">

                                    No operations found.

                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}

                <div class="mt-6">

                    {{ $operations->links() }}

                </div>

            </div>

        </div>

    </div>

</div>

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    <div class="bg-white shadow rounded-lg p-6">

        <h3 class="text-lg font-bold mb-5">
            Transactions List
        </h3>

        {{-- Search & Filters --}}

        <form method="GET"
              action="{{ route('transactions.index') }}"
              class="mb-6">

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- Search --}}

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search description or customer..."
                    class="border rounded px-3 py-2">

                {{-- Type --}}

                <select name="type"
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

                <select name="customer_id"
                        class="border rounded px-3 py-2">

                    <option value="">
                        All Customers
                    </option>

                    @foreach($customers as $customer)

                        <option value="{{ $customer->id }}"
                            {{ request('customer_id') == $customer->id ? 'selected' : '' }}>

                            {{ $customer->name }}

                        </option>

                    @endforeach

                </select>

                {{-- User --}}

                <select name="user_id"
                        class="border rounded px-3 py-2">

                    <option value="">
                        All Users
                    </option>

                    @foreach($users as $user)

                        <option value="{{ $user->id }}"
                            {{ request('user_id') == $user->id ? 'selected' : '' }}>

                            {{ $user->name }}

                        </option>

                    @endforeach

                </select>

                {{-- Date --}}

                <input
                    type="date"
                    name="date"
                    value="{{ request('date') }}"
                    class="border rounded px-3 py-2">

            </div>

            <div class="mt-4 flex gap-2">

                <button
                    type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2 rounded">

                    Search / Filter

                </button>

                <a
                    href="{{ route('transactions.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded">

                    Reset

                </a>

            </div>

        </form>


        {{-- Transactions Table --}}

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="border-b bg-gray-100">

                    <tr>

                        <th class="py-3 px-3 text-left">
                            #
                        </th>

                        <th class="py-3 px-3 text-left">
                            Operation
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
                            Date
                        </th>

                    </tr>

                </thead>

                <tbody>

                @forelse($transactions as $transaction)

                    <tr class="border-b hover:bg-gray-50">

                        <td class="px-3 py-3">
                            {{ $transaction->id }}
                        </td>

                        <td class="px-3 py-3">
                            #{{ $transaction->operation_id }}
                        </td>

                        <td class="px-3 py-3">
                            {{ $transaction->customer?->name ?? '-' }}
                        </td>

                        <td class="px-3 py-3">
                            {{ ucfirst($transaction->type) }}
                        </td>

                        <td class="px-3 py-3 font-semibold">
                            {{ number_format($transaction->amount, 2) }}
                        </td>

                        <td class="px-3 py-3">

                            <span class="px-2 py-1 rounded bg-green-100 text-green-700">

                                {{ ucfirst($transaction->status) }}

                            </span>

                        </td>

                        <td class="px-3 py-3">
                            {{ $transaction->user?->name ?? '-' }}
                        </td>

                        <td class="px-3 py-3">
                            {{ $transaction->created_at?->format('Y-m-d H:i') }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8"
                            class="text-center py-8 text-gray-500">

                            No transactions found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-6">
            {{ $transactions->links() }}
        </div>

    </div>

</div>

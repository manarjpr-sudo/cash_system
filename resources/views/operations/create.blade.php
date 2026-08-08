<x-app-layout>

```
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Create Operation
    </h2>
</x-slot>

<div class="py-12">

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow-sm rounded-lg p-6">

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('operations.store') }}">

                @csrf

                <div class="mb-4">
                    <label class="block mb-2 font-medium">
                        Customer
                    </label>

                    <select
                        name="customer_id"
                        class="border rounded w-full p-2"
                        required>

                        <option value="">
                            Select Customer
                        </option>

                        @foreach($customers as $customer)
                            <option
                                value="{{ $customer->id }}"
                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 font-medium">
                        Operation Type
                    </label>

                    <select
                        name="type"
                        class="border rounded w-full p-2"
                        required>

                        <option value="">Select Type</option>
                        <option value="deposit" {{ old('type') == 'deposit' ? 'selected' : '' }}>
                            Deposit
                        </option>
                        <option value="withdraw" {{ old('type') == 'withdraw' ? 'selected' : '' }}>
                            Withdraw
                        </option>

                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 font-medium">
                        Amount
                    </label>

                    <input
                        type="number"
                        name="amount"
                        step="0.01"
                        min="0"
                        value="{{ old('amount') }}"
                        class="border rounded w-full p-2"
                        required>
                </div>

                <div class="mb-6">
                    <label class="block mb-2 font-medium">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        class="border rounded w-full p-2">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center gap-3">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">
                        Save Operation
                    </button>

                    <a
                        href="{{ route('operations.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>
```

</x-app-layout>

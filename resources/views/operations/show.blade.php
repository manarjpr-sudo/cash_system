<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Operation Details
    </h2>
</x-slot>


<div class="py-12">

    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        {{-- Operation Information --}}

        <div class="bg-white shadow-sm rounded-lg p-6">

            <div class="flex justify-between items-center mb-6">

                <h3 class="text-lg font-bold">
                    Operation #{{ $operation->id }}
                </h3>


                @switch($operation->status)

                    @case('approved')

                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700">
                            Approved
                        </span>

                        @break

                    @case('rejected')

                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700">
                            Rejected
                        </span>

                        @break

                    @default

                        <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700">
                            Pending
                        </span>

                @endswitch

            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <strong>Customer:</strong>

                    <div class="mt-1 text-gray-600">
                        {{ $operation->customer?->name ?? '-' }}
                    </div>
                </div>


                <div>
                    <strong>Type:</strong>

                    <div class="mt-1 text-gray-600">
                        {{ ucfirst($operation->type) }}
                    </div>
                </div>


                <div>
                    <strong>Amount:</strong>

                    <div class="mt-1 text-gray-800 font-semibold">
                        {{ number_format($operation->amount, 2) }}
                    </div>
                </div>


                <div>
                    <strong>Created By:</strong>

                    <div class="mt-1 text-gray-600">
                        {{ $operation->user?->name ?? '-' }}
                    </div>
                </div>


                <div>
                    <strong>Created At:</strong>

                    <div class="mt-1 text-gray-600">
                        {{ $operation->created_at?->format('Y-m-d H:i') ?? '-' }}
                    </div>
                </div>


                <div>
                    <strong>Updated At:</strong>

                    <div class="mt-1 text-gray-600">
                        {{ $operation->updated_at?->format('Y-m-d H:i') ?? '-' }}
                    </div>
                </div>


                <div class="md:col-span-2">

                    <strong>Description:</strong>

                    <p class="mt-2 text-gray-600">
                        {{ $operation->description ?? '-' }}
                    </p>

                </div>

            </div>


            {{-- Approval Actions --}}

            @if(
                $operation->status === 'pending' &&
                Auth::user()->role?->permissions->contains('name', 'approve_operations')
            )

                <div class="mt-8 pt-6 border-t">

                    <h4 class="font-semibold mb-4">
                        Approval Actions
                    </h4>

                    <div class="flex gap-3">

                        <form
                            method="POST"
                            action="{{ route('operations.approve', $operation) }}">

                            @csrf

                            <button
                                type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded">

                                Approve

                            </button>

                        </form>


                        <form
                            method="POST"
                            action="{{ route('operations.reject', $operation) }}">

                            @csrf

                            <button
                                type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded">

                                Reject

                            </button>

                        </form>

                    </div>

                </div>

            @endif

        </div>


        {{-- Approval History --}}

        <div class="bg-white shadow-sm rounded-lg p-6 mt-6">

            <h3 class="text-lg font-bold mb-5">
                Approval History
            </h3>


            @forelse($operation->approvals as $approval)

                <div class="border-b last:border-b-0 py-4">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>

                            <strong>Status:</strong>

                            <div class="mt-1">

                                @if($approval->status === 'approved')

                                    <span class="px-2 py-1 rounded bg-green-100 text-green-700">
                                        Approved
                                    </span>

                                @else

                                    <span class="px-2 py-1 rounded bg-red-100 text-red-700">
                                        Rejected
                                    </span>

                                @endif

                            </div>

                        </div>


                        <div>

                            <strong>By:</strong>

                            <div class="mt-1 text-gray-600">
                                {{ $approval->user?->name ?? '-' }}
                            </div>

                        </div>


                        <div>

                            <strong>Date:</strong>

                            <div class="mt-1 text-gray-600">
                                {{ $approval->approved_at?->format('Y-m-d H:i') ?? '-' }}
                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <p class="text-gray-500">
                    No approval history yet.
                </p>

            @endforelse

        </div>


        {{-- Generated Transaction --}}

        @if($operation->transaction)

            <div class="bg-white shadow-sm rounded-lg p-6 mt-6">

                <div class="flex justify-between items-center mb-5">

                    <h3 class="text-lg font-bold">
                        Generated Transaction
                    </h3>

                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700">
                        {{ ucfirst($operation->transaction->status) }}
                    </span>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <strong>Transaction ID:</strong>

                        <div class="mt-1 text-gray-600">
                            #{{ $operation->transaction->id }}
                        </div>

                    </div>


                    <div>

                        <strong>Type:</strong>

                        <div class="mt-1 text-gray-600">
                            {{ ucfirst($operation->transaction->type) }}
                        </div>

                    </div>


                    <div>

                        <strong>Amount:</strong>

                        <div class="mt-1 font-semibold">
                            {{ number_format($operation->transaction->amount, 2) }}
                        </div>

                    </div>


                    <div>

                        <strong>Processed By:</strong>

                        <div class="mt-1 text-gray-600">
                            {{ $operation->transaction->user?->name ?? '-' }}
                        </div>

                    </div>


                    <div>

                        <strong>Created At:</strong>

                        <div class="mt-1 text-gray-600">
                            {{ $operation->transaction->created_at?->format('Y-m-d H:i') ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>

        @elseif($operation->status === 'approved')

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mt-6">

                <h3 class="font-bold text-yellow-800">
                    Transaction Not Found
                </h3>

                <p class="mt-2 text-yellow-700">
                    This operation is approved, but no transaction is currently linked to it.
                </p>

            </div>

        @endif


        {{-- Navigation --}}

        <div class="mt-6">

            <a
                href="{{ route('operations.index') }}"
                class="bg-gray-200 hover:bg-gray-300 px-5 py-2 rounded">

                Back to Operations

            </a>

        </div>

    </div>

</div>

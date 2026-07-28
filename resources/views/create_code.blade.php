<x-layout title="Create Code Page">

    <h2>Create Code Page</h2>

    @if (session('success'))
        <p class="size = 4">{{ session('success') }}</p>
    @endif

    <form action="{{ route('store.code', ['pack' => $pack]) }}" method="POST">
        @csrf

        <div>
            <p>Pack: {{ $pack->name }}</p>
            <p>Create date: {{ $pack->created_at->format('Y-m-d') }}</p>
        </div>

        <div style="margin-bottom: 15px">
            <label for="recipient_type">Recipient Type</label>

            <select id="recipient_type" name="recipient_type">
                @foreach (\App\Enums\RecipientType::cases() as $recipientType)
                    <option
                        value="{{ $recipientType->value }}"
                        @selected(old('recipient_type') === $recipientType->value)
                    >
                        {{ $recipientType->label() }}
                    </option>
                @endforeach
            </select>

            <x-error name="recipient_type"/>
        </div>

        <div>
            <label for="recipient_name">Recipient Name</label>

            <p>
                <input
                    id="recipient_name"
                    name="recipient_name"
                    type="text"
                    value=""
                >
            </p>

            <x-error name="recipient_name"/>
        </div>

        <div>
            <label for="recipient_email">Recipient Email</label>

            <p>
                <input
                    id="recipient_email"
                    name="recipient_email"
                    type="email"
                    value=""
                >
            </p>

            <x-error name="recipient_email"/>
        </div>

        <div>
            <label for="amount">Amount</label>

            <p>
                <input
                    id="amount"
                    name="amount"
                    type="number"
                    min="1"
                    step="1"
                    value=""
                >
            </p>

            <x-error name="amount"/>
        </div>

        <div style="margin-bottom: 1px">
            <label for="send_at">Send Date</label>

            <p>
                <input
                    id="send_at"
                    name="send_at"
                    type="datetime-local"
                    value="{{$pack->date}}"
                >
            </p>

            <x-error name="send_at"/>
        </div>


        <div style="margin-top: 1px">
            <label for="send_options">Send Options</label>
            <p>
                <select id="send_options" name="send_options">
                    <option
                        value="send_at"
                    >
                        Choose Date
                    </option>

                    <option
                        value='instant'
                    >
                        Instant Send
                    </option>

                    <option
                        value="{{'no_date'}}"
                    >
                        No date
                    </option>

                </select>
            </p>
        </div>

        <button type="submit">Save</button>
    </form>
    <form
        action="{{ route('show.code', ['pack' => $pack]) }}"
        method="GET"
        style="margin: 0;
        margin-top:10px"
    >
        <button type="submit">Show all Codes</button>
    </form>
</x-layout>

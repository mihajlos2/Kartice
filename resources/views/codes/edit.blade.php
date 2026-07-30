<x-layout title="Edit Code Page">

    <h2>Edit Code Page</h2>

    @if (session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    <div>
        <p>Pack: {{ $pack->name }}</p>

        <p>
            Create date:
            {{ $pack->created_at->format('Y-m-d') }}
        </p>
    </div>

    <form
        action="{{ route('update.code', [
            'pack' => $pack,
            'code' => $code
        ]) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <div style="margin-bottom: 15px">
            <label for="recipient_type">
                Recipient Type
            </label>

            <select id="recipient_type" name="recipient_type">
                @foreach (\App\Helpers\Enums\RecipientType::cases() as $recipientType)
                    <option
                        value="{{ $recipientType->value }}"
                        @selected(
                            old(
                                'recipient_type',
                                $code->recipient_type->value
                            ) === $recipientType->value
                        )
                    >
                        {{ $recipientType->label() }}
                    </option>
                @endforeach
            </select>

            <x-error name="recipient_type"/>
        </div>

        <div>
            <label for="recipient_name">
                Recipient Name
            </label>

            <p>
                <input
                    id="recipient_name"
                    name="recipient_name"
                    type="text"
                    value="{{ old('recipient_name', $code->name) }}"
                >
            </p>

            <x-error name="recipient_name"/>
        </div>

        <div>
            <label for="recipient_email">
                Recipient Email
            </label>

            <p>
                <input
                    id="recipient_email"
                    name="recipient_email"
                    type="email"
                    value="{{ old('recipient_email', $code->email) }}"
                >
            </p>

            <x-error name="recipient_email"/>
        </div>

        <div>
            <label for="amount">
                Amount
            </label>

            <p>
                <input
                    id="amount"
                    name="amount"
                    type="number"
                    min="1"
                    step="1"
                    value="{{ old('amount', $code->amount) }}"
                >
            </p>

            <x-error name="amount"/>
        </div>

        <div>
            <label for="send_at">
                Send Date
            </label>

            <p>
                <input
                    id="send_at"
                    name="send_at"
                    type="datetime-local"
                    value="{{ old('send_at',$code->date ?? $pack->date) }}"
                >
            </p>

            <x-error name="send_at"/>
        </div >

        <div style="margin-top: 1px; margin-bottom: 10px">
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

        <button type="submit" style="background: #157347">
            Save
        </button>

    </form>

    <form
        action="{{ route('show.code', ['pack' => $pack]) }}"
        method="GET"
        style="margin-top: 10px;
        "
    >
        <button type="submit" style="background: #4f46e5">Show all Codes</button>
    </form>

</x-layout>

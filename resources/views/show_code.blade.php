<x-layout title="Pack Codes">

    <h1>Pack Codes</h1>

    @if (session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    <p>Pack ID: {{ $pack->id }}</p>
    <p>Store Credit Pack: {{ $pack->name }}</p>
    <p>Count: {{ $codes->count() }}</p>

    <div
        style="
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        "
    >
        <form
            method="POST"
            action="{{ route('destroy.pack', ['pack' => $pack]) }}"
            style="margin: 0;"
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                style="
                    padding: 10px 16px;
                    border: 0;
                    color: white;
                    background-color: #b42318;
                    cursor: pointer;
                "
            >
                Delete Pack
            </button>
        </form>

        <a
            href="{{ route('create.code', ['pack' => $pack]) }}"
            style="
                display: inline-block;
                padding: 10px 16px;
                color: white;
                background-color: #1c1c1a;
                text-decoration: none;
            "
        >
            Add Code
        </a>
    </div>

    <table
        width="100%"
        border="1"
        style="border-collapse: collapse;"
    >
        <thead>
        <tr style="background-color: #eeeeee;">
            <th style="padding: 10px;">Name</th>
            <th style="padding: 10px;">Email</th>
            <th style="padding: 10px;">Amount</th>
            <th style="padding: 10px;">Send Date</th>
            <th style="padding: 10px;">Recipient Type</th>
            <th style="padding: 10px;">Code</th>
            <th style="padding: 10px;">Edit</th>
            <th style="padding: 10px;">Delete</th>
        </tr>
        </thead>

        <tbody>
        @forelse ($codes as $code)
            <tr>
                <td style="padding: 10px;">
                    {{ $code->name }}
                </td>

                <td style="padding: 10px;">
                    {{ $code->email }}
                </td>

                <td style="padding: 10px;">
                    {{ $code->amount }}
                </td>

                <td style="padding: 10px;">
                    {{ $code->date ?? 'No date set' }}
                </td>

                <td style="padding: 10px;">
                    {{ $code->recipient_type }}
                </td>

                <td style="padding: 10px;">
                    {{ $code->code }}
                </td>

                <td style="padding: 0; width: 90px;">
                    @if( $code->sent_at === null)
                    <form
                        method="GET"
                        action="{{ route('edit.code', [
                                    'pack' => $pack,
                                    'code' => $code
                                ]) }}"
                        style="margin: 0;"
                    >
                        <button
                            type="submit"
                            style="
                                    width: 100%;
                                    padding: 12px;
                                    border: 0;
                                    cursor: pointer;
                                "
                        >
                            Edit
                        </button>
                    </form>
                    @else
                        <button
                            style="
                                width: 100%;
                                padding: 12px;
                                border: 0;
                                cursor: pointer;
                            "
                        >
                            Sent
                        </button>
                    @endif
                </td>

                <td style="padding: 0; width: 90px;">
                    <form
                        method="POST"
                        action="{{ route('destroy.code', [
                                'pack' => $pack,
                                'code' => $code
                            ]) }}"
                        style="margin: 0;"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            style="
                                    width: 100%;
                                    padding: 12px;
                                    border: 0;
                                    color: white;
                                    background-color: #b42318;
                                    cursor: pointer;
                                "
                        >
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td
                    colspan="8"
                    style="padding: 10px; text-align: center;"
                >
                    This pack does not have any codes.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>


</x-layout>

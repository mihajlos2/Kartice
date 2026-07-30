<x-layout title="Pack Codes">

    <header class="page-header">
        <div>
            <h1>Pack Codes</h1>

            @if (session('success'))
                <p style="color: green;">
                    {{ session('success') }}
                </p>
            @endif

            <p>Pack ID: {{ $pack->id }}</p>
            <p>Store Credit Pack: {{ $pack->name }}</p>
            <p>Count: {{ $codes->total() }}</p>
        </div>
    </header>
    <div
        style="
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        "
    >
        <a
            href="{{ route('export.pack', ['pack' => $pack]) }}"
            style="
                border-radius: 8px;
                display: inline-block;
                padding: 10px 16px;
                color: white;
                cursor: pointer;
                background-color: #157347;
                text-decoration: none;
            "
        >
            Export
        </a>

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
                border-radius: 8px;
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

    <table class="codes-table">
        <thead>
        <tr style="background-color: #eeeeee;">
            <th style="padding: 10px;">Name</th>
            <th style="padding: 10px;">Email</th>
            <th style="padding: 10px;">Amount</th>
            <th style="padding: 10px;">Send Date</th>
            <th style="padding: 10px;">Recipient Type</th>
            <th style="padding: 10px;">Code</th>
            <th style="padding: 10px;">Edit</th>
            <th style="padding: 10px;">Export</th>
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
                    @if ($code->sent_at === null)
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
                            type="button"
                            disabled
                            style="
                                width: 100%;
                                padding: 12px;
                                border: 0;
                                cursor: not-allowed;
                            "
                        >
                            Sent
                        </button>
                    @endif
                </td>

                <td style="padding: 0; width: 90px;">
                    <a
                        href="{{ route('export.code', [
                            'pack' => $pack,
                            'code' => $code
                        ]) }}"
                        style="
                            border-radius: 8px;
                            display: block;
                            width: 100%;
                            box-sizing: border-box;
                            padding: 12px;
                            color: white;
                            background-color: #157347;
                            text-align: center;
                            text-decoration: none;"
                    >
                        Export
                    </a>
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
                    colspan="9"
                    style="padding: 10px; text-align: center;"
                >
                    This pack does not have any codes.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $codes->links() }}
    </div>

</x-layout>

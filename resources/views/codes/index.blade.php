@php use App\Helpers\Enums\RecipientType; @endphp
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

        <form
            id="bulk-delete-form"
            method="POST"
            class="bulk-actions"
            action="{{ route('codes.bulk-delete', ['pack' => $pack]) }}"
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
                Delete Codes
            </button>

            <x-error name="code_ids"/>
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

    <form
        class="table-filters"
        method="POST"
        action="{{ route('codes.import', ['pack' => $pack]) }}"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="filter-group code-import-file-group">
            <label for="csv_file">Import codes from CSV</label>

            <input
                id="csv_file"
                name="csv_file"
                type="file"
                accept=".csv,text/csv"
                required
            >

            <x-error name="csv_file"/>
        </div>

        <button class="btn btn-success code-import-submit" type="submit">
            Queue Import
        </button>
    </form>

    @if (session()->has('code_import_id'))
        <div
            class="alert alert-info code-import-status"
            data-code-import-status
            data-status-url="{{ route('codes.import.status', [
                'pack' => $pack,
                'importId' => session('code_import_id'),
            ]) }}"
            data-failure-message="{{ __('messages.code_import_status_unavailable') }}"
            role="status"
            aria-live="polite"
        >
            {{ __('messages.code_import_processing') }}
        </div>
    @endif

    <form
        class="table-filters"
        method="GET"
        action="{{ route('show.code', ['pack' => $pack]) }}"
    >
        <div class="filter-group">
            <label for="status">
                Delivery Status
            </label>

            <select id="status" name="status">
                <option
                    value="all"
                    @selected(request('status', 'all') === 'all')
                >
                    All Codes
                </option>

                <option
                    value="sent"
                    @selected(request('status') === 'sent')
                >
                    Sent Codes
                </option>

                <option
                    value="unsent"
                    @selected(request('status') === 'unsent')
                >
                    Unsent Codes
                </option>
            </select>
        </div>

        <div class="filter-group">
            <label for="recipient_type">
                Recipient Type
            </label>

            <select id="recipient_type" name="recipient_type">
                <option value="">
                    All Recipient Types
                </option>

                @foreach (RecipientType::cases() as $recipientType)
                    <option
                        value="{{ $recipientType->value }}"
                        @selected(
                            request('recipient_type') === $recipientType->value
                        )
                    >
                        {{ $recipientType->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label for="order">
                Order
            </label>

            <select id="order" name="order">
                <option
                    value="date_desc"
                    @selected(request('order', 'date_desc') === 'date_desc')
                >
                    Date – Descending
                </option>

                <option
                    value="date_asc"
                    @selected(request('order') === 'date_asc')
                >
                    Date – Ascending
                </option>

                <option
                    value="amount_desc"
                    @selected(request('order') === 'amount_desc')
                >
                    Amount – Highest First
                </option>

                <option
                    value="amount_asc"
                    @selected(request('order') === 'amount_asc')
                >
                    Amount – Lowest First
                </option>
            </select>
        </div>

        <button class="btn btn-primary" style="float: right" type="submit">
            Apply
        </button>

        <a
            class="btn btn-secondary"
            href="{{ route('show.code', ['pack' => $pack]) }}"
            style="float: right"

        >
            Reset
        </a>
    </form>

    <table class="codes-table">
        <thead>
        <tr style="background-color: #eeeeee;">
            <th class="selection-column">Select</th>
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

                <td class="selection-column">
                    <input
                        type="checkbox"
                        name="code_ids[]"
                        value="{{ $code->id }}"
                        form="bulk-delete-form"
                    >
                </td>

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
                    colspan="10"
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

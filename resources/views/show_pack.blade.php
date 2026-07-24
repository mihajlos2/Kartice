<x-layout title="Show Packs">

    <h1>All Packs</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @forelse ($packs as $pack)
        <div style="display: flex; gap: 12px; margin-bottom: 10px;">

            <p style="margin: 0; min-width: 150px;">
                Pack name: {{ $pack->name }}
            </p>

            <p style="margin: 0; min-width: 150px;">
                Default date: {{ $pack->date }}
            </p>

            <form
                action="{{ route('create.code', ['pack' => $pack]) }}"
                method="GET"
                style="margin: 0;"
            >
                <button type="submit">Add</button>
            </form>

            <form
                action="{{ route('destroy.pack', ['pack' => $pack]) }}"
                method="POST"
                style="margin: 0;"
            >
            @csrf
            @method('DELETE')
                <button type="submit">Delete</button>
            </form>

            <form
                action="{{ route('show.code', ['pack' => $pack]) }}"
                method="GET"
                style="margin: 0;"
            >

                <button type="submit">Show all Codes</button>
            </form>

        </div>
    @empty
        <p>No packs have been created.</p>
    @endforelse

</x-layout>

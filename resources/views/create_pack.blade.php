<x-layout title="Create Pack">

    <h1>Create Pack</h1>

    <form method="POST" action="{{ route('packs.store') }}">
        @csrf

        <label for="name">Pack name:</label>
        <p>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
            >
        <x-error name="name"/>
        <div>
            <label for="date">Default Date: </label>
            <p>
                <input
                    id="date"
                    name="date"
                    type="datetime-local"
                    value="{{ old('date') }}"
                >
            <x-error name="date"/>
            </p>
        </div>

        <button type="submit">Create</button>
    </form>


</x-layout>

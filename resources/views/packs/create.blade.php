<x-layout title="Create Pack">

    <section class="page-section">
        <header class="page-header">
            <div>
                <p class="page-eyebrow">Store credits</p>
                <h1>Create Pack</h1>
                <p class="page-description">
                    Create a new pack and choose its default delivery date.
                </p>
            </div>
        </header>

        <div class="form-card">
            <form
                class="stacked-form"
                method="POST"
                action="{{ route('packs.store') }}"
            >
                @csrf

                <div class="form-group">
                    <label for="name">Pack name</label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="Enter pack name"
                    >

                    <x-error name="name"/>
                </div>

                <div class="form-group">
                    <label for="date">Default delivery date</label>

                    <input
                        id="date"
                        name="date"
                        type="datetime-local"
                        value="{{ old('date') }}"
                    >

                    <x-error name="date"/>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">
                        Create Pack
                    </button>
                </div>
            </form>
        </div>
    </section>

</x-layout>

<x-layout title="Show Packs">

    <section class="page-section">
        <header class="page-header">
            <div>
                <p class="page-eyebrow">Store credits</p>
                <h1>All Packs</h1>
                <p class="page-description">
                    Manage your store credit packs and their codes.
                </p>
            </div>
        </header>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="pack-grid">
            @forelse ($packs as $pack)
                <article class="pack-card">
                    <div class="pack-card-content">
                        <p class="page-eyebrow">
                            Pack #{{ $loop->iteration }}
                        </p>

                        <h2>{{ $pack->name }}</h2>

                        <p class="pack-date">
                            Default delivery:
                            {{ $pack->date ?? 'No default date' }}
                        </p>
                    </div>

                    <div class="pack-actions">
                        <a
                            class="btn btn-primary"
                            href="{{ route('show.code', ['pack' => $pack]) }}"
                        >
                            View Codes
                        </a>

                        <a
                            class="btn btn-success"
                            href="{{ route('create.code', ['pack' => $pack]) }}"
                        >
                            Add Code
                        </a>

                        <form
                            class="inline-form"
                            action="{{ route('destroy.pack', ['pack' => $pack]) }}"
                            method="POST"
                        >
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger" type="submit">
                                Delete
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <h2>No packs yet</h2>

                    <p>
                        Create your first pack to start adding store credit codes.
                    </p>

                    <a class="btn btn-primary" href="{{ route('packs.create') }}">
                        Create First Pack
                    </a>
                </div>
            @endforelse
        </div>
    </section>

</x-layout>

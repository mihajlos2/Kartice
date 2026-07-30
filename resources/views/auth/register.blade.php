<x-layout title="Register Page">

    <div class="auth-page">
        <div class="auth-card">

            <h1>Create account</h1>

            <form action="#" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">
                        Name
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Enter your name"
                        required
                        autocomplete="name"
                    >

                    <x-error name="name"/>
                </div>

                <div class="form-group">
                    <label for="email">
                        Email address
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="example@email.com"
                        required
                        autocomplete="email"
                    >

                    <x-error name="email"/>
                </div>

                <div class="form-group">
                    <label for="password">
                        Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        autocomplete="new-password"
                    >
                    <x-error name="password"/>
                </div>
                <button class="auth-button" type="submit">
                    Register
                </button>
            </form>

        </div>
    </div>
</x-layout>

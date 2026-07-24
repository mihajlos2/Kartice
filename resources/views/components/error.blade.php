@props(['name'])

@error($name)
<p class="error-message">
    {{ $message }}
</p>
@enderror

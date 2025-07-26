<form method="post" action="{{ route('profile.update') }}" class="mt-4">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label">Nom</label>
        <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
    </div>

    <div class="d-flex align-items-center gap-4">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        @if (session('status') === 'profile-updated')
            <p class="text-success small">Enregistré.</p>
        @endif
    </div>
</form>

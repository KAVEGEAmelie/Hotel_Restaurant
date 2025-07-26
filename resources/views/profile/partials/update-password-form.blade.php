<form method="post" action="{{ route('password.update') }}" class="mt-4">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="current_password" class="form-label">Mot de passe actuel</label>
        <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Nouveau mot de passe</label>
        <input id="password" name="password" type="password" class="form-control" autocomplete="new-password">
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
    </div>

    <div class="d-flex align-items-center gap-4">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        @if (session('status') === 'password-updated')
            <p class="text-success small">Enregistré.</p>
        @endif
    </div>
</form>

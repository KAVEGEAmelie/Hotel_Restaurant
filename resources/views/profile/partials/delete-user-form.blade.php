<div class="mt-4">
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
        Supprimer le compte
    </button>
</div>

<div class="modal fade" id="confirm-user-deletion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title">Êtes-vous sûr de vouloir supprimer votre compte ?</h5>
                </div>
                <div class="modal-body">
                    <p>Cette action est irréversible.</p>
                    <div class="mt-3">
                        <label for="password" class="form-label">Veuillez entrer votre mot de passe pour confirmer.</label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Mot de passe" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer le Compte</button>
                </div>
            </form>
        </div>
    </div>
</div>

(function () {
    const modal = document.getElementById('form-page-modal');
    const modalTitle = document.getElementById('form-modal-title');
    const modalBody = document.getElementById('form-modal-body');

    if (!modal || !modalTitle || !modalBody) {
        return;
    }

    let returnFocus = null;

    function isModalPath(pathname) {
        return /^\/admin\/[^/]+\/create\/?$/.test(pathname)
            || /^\/admin\/[^/]+\/[^/]+\/edit\/?$/.test(pathname)
            || /^\/contact\/?$/.test(pathname)
            || /^\/profile\/?$/.test(pathname)
            || /^\/(login|register|forgot-password|confirm-password|verify-email)\/?$/.test(pathname)
            || /^\/reset-password\/[^/]+\/?$/.test(pathname)
            || /^\/reservations\/[^/]+\/confirm\/?$/.test(pathname);
    }

    function showModal() {
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('form-modal-open');
    }

    function closeModal() {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('form-modal-open');
        modalBody.replaceChildren();
        if (returnFocus && document.contains(returnFocus)) {
            returnFocus.focus();
        }
    }

    function contentFromDocument(documentNode) {
        const source = documentNode.querySelector('main')
            || documentNode.querySelector('.login-container')
            || documentNode.querySelector('.min-h-screen')
            || documentNode.body;
        const title = source.querySelector('h1, h2')?.textContent.trim()
            || documentNode.title
            || 'Formulaire';
        const content = document.createElement('div');
        content.innerHTML = source.innerHTML;
        content.querySelectorAll('script').forEach((script) => script.remove());
        content.querySelector('h1')?.remove();

        return { title, content };
    }

    async function openModal(url, trigger) {
        returnFocus = trigger || document.activeElement;
        modalTitle.textContent = 'Chargement...';
        modalBody.innerHTML = '<div class="form-modal-loading" role="status">Chargement...</div>';
        showModal();

        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { Accept: 'text/html' },
            });
            if (!response.ok) {
                throw new Error('Impossible de charger le formulaire.');
            }

            const documentNode = new DOMParser().parseFromString(await response.text(), 'text/html');
            const { title, content } = contentFromDocument(documentNode);
            modalTitle.textContent = title;
            modalBody.replaceChildren(content);
            modalBody.scrollTop = 0;
            modalBody.querySelector('input:not([type="hidden"]), select, textarea')?.focus({ preventScroll: true });
        } catch (error) {
            modalTitle.textContent = 'Formulaire indisponible';
            modalBody.textContent = error.message || 'Une erreur est survenue.';
        }
    }

    function clearErrors(form) {
        form.querySelectorAll('[data-form-error], .form-modal-summary').forEach((error) => error.remove());
        form.querySelectorAll('[aria-invalid="true"]').forEach((field) => field.removeAttribute('aria-invalid'));
    }

    function showErrors(form, errors) {
        clearErrors(form);
        const messages = Object.values(errors).flat();
        const summary = document.createElement('div');
        summary.className = 'form-modal-summary';
        summary.setAttribute('role', 'alert');
        summary.textContent = messages.join(' ');
        form.prepend(summary);

        Object.entries(errors).forEach(([name, fieldMessages]) => {
            const escapedName = CSS.escape(name);
            const arrayName = CSS.escape(name.replace(/\.\d+$/, '[]'));
            const field = form.querySelector(`[name="${escapedName}"]`)
                || form.querySelector(`[name="${arrayName}"]`);
            if (!field) {
                return;
            }

            field.setAttribute('aria-invalid', 'true');
            const error = document.createElement('div');
            error.dataset.formError = '';
            error.textContent = fieldMessages[0];
            field.insertAdjacentElement('afterend', error);
        });

        form.querySelector('[aria-invalid="true"]')?.focus({ preventScroll: true });
    }

    document.addEventListener('click', (event) => {
        const closeButton = event.target.closest('[data-form-modal-close]');
        if (closeButton) {
            closeModal();
            return;
        }

        if (event.target === modal) {
            closeModal();
            return;
        }

        const localTrigger = event.target.closest('[data-form-modal-template]');
        if (localTrigger) {
            const template = document.getElementById(localTrigger.dataset.formModalTemplate);
            if (template instanceof HTMLTemplateElement) {
                returnFocus = localTrigger;
                modalTitle.textContent = template.dataset.title || 'Formulaire';
                modalBody.replaceChildren(template.content.cloneNode(true));
                showModal();
            }
            return;
        }

        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target) {
            return;
        }

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin || !isModalPath(url.pathname)) {
            return;
        }

        event.preventDefault();
        openModal(url.href, link);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !modalBody.contains(form)) {
            return;
        }

        const action = new URL(form.action, window.location.href);
        if (/\/payment\/initiate\//.test(action.pathname)) {
            return;
        }

        event.preventDefault();
        clearErrors(form);
        const submitButton = form.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }

        try {
            const response = await fetch(action.href, {
                method: (form.method || 'POST').toUpperCase(),
                body: new FormData(form),
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.status === 422) {
                const payload = await response.json();
                showErrors(form, payload.errors || { form: [payload.message || 'Vérifie les champs du formulaire.'] });
                return;
            }

            if (response.redirected) {
                const destination = new URL(response.url);
                if (destination.origin === window.location.origin
                    && destination.pathname === window.location.pathname
                    && action.pathname === '/contact') {
                    modalTitle.textContent = 'Message envoyé';
                    const confirmation = document.createElement('p');
                    confirmation.className = 'mb-0';
                    confirmation.textContent = 'Votre message a bien été envoyé ! Nous vous répondrons dès que possible.';
                    modalBody.replaceChildren(confirmation);
                    return;
                }

                if (destination.origin === window.location.origin && isModalPath(destination.pathname)) {
                    await openModal(destination.href);
                } else {
                    window.location.assign(destination.href);
                }
                return;
            }

            if (!response.ok) {
                throw new Error('L’enregistrement a échoué. Réessaie dans un instant.');
            }
        } catch (error) {
            showErrors(form, { form: [error.message || 'Une erreur est survenue.'] });
        } finally {
            if (submitButton && document.contains(submitButton)) {
                submitButton.disabled = false;
            }
        }
    });

    document.addEventListener('change', (event) => {
        if (!(event.target instanceof HTMLInputElement) || event.target.type !== 'file' || event.target.name !== 'images[]') {
            return;
        }

        const preview = modalBody.querySelector('#images-preview');
        if (!preview) {
            return;
        }

        preview.replaceChildren();
        Array.from(event.target.files || []).forEach((file) => {
            if (!file.type.startsWith('image/')) {
                return;
            }

            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = file.name;
            image.className = 'img-thumbnail';
            image.style.cssText = 'width: 120px; height: 90px; object-fit: cover;';
            preview.appendChild(image);
        });
    });
})();
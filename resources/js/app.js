import './bootstrap';

// Native dialogs keep focus inside the form and return it to the opener on close.
document.querySelectorAll('[data-dialog-open]').forEach(opener => {
    opener.addEventListener('click', event => {
        const dialog = document.getElementById(opener.dataset.dialogOpen);
        if (!dialog?.showModal) return;
        event.preventDefault();
        const form = dialog.querySelector('form');
        form.reset();
        form.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(field => field.value = '');
        form.action = opener.dataset.action;
        const method = form.elements.namedItem('_method');
        method.disabled = opener.dataset.method === 'POST';
        method.value = opener.dataset.method;
        form.elements.namedItem('_record_id').value = '';
        const values = JSON.parse(opener.dataset.values || '{}');
        Object.entries(values).forEach(([name, value]) => {
            const field = form.elements.namedItem(name);
            if (field) field.value = value ?? '';
        });
        dialog.querySelector('[data-dialog-title]').textContent = opener.dataset.title;
        dialog.querySelectorAll('[data-field-error]').forEach(error => error.hidden = true);
        dialog.querySelectorAll('[aria-invalid]').forEach(field => field.removeAttribute('aria-invalid'));
        dialog.showModal();
        dialog.querySelector('input:not([type="hidden"])')?.focus();
    });
});

document.querySelectorAll('dialog').forEach(dialog => {
    dialog.querySelectorAll('[data-dialog-close]').forEach(button => {
        button.addEventListener('click', () => dialog.close());
    });
    if (dialog.hasAttribute('data-auto-open') && dialog.showModal) {
        dialog.showModal();
        dialog.querySelector('[aria-invalid="true"]')?.focus();
    }
});

// Explain oversized files before submitting; the server remains authoritative.
document.querySelectorAll('input[type="file"][name="dokumen"]').forEach(input => {
    input.addEventListener('change', () => {
        const file = input.files[0];
        input.setCustomValidity(file && file.size > 30 * 1024 * 1024
            ? 'Ukuran PDF maksimal 30 MB. Pilih dokumen yang lebih kecil.' : '');
        if (!input.checkValidity()) input.reportValidity();
    });
});

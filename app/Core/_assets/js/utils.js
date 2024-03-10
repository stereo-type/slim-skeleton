import * as DOMPurify from "dompurify";

function showLoader() {
    const loader = document.getElementById('--catalog-loader');
    loader.classList.remove('d-none');
    loader.classList.add('d-block');
}

function dismissLoader() {
    const loader = document.getElementById('--catalog-loader');
    loader.classList.remove('d-block');
    loader.classList.add('d-none');
}

function cleanForm(formData, formElement) {
    formData.forEach((element, key, form) => {
        const clean = DOMPurify.sanitize(element.trim());
        if (clean !== element) {
            const filterElement = formElement.querySelector(`[name=${key}]`);
            if (filterElement) {
                filterElement.value = clean;
                form.set(key, clean);
            }
        }
    });
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

export {
    showLoader,
    dismissLoader,
    cleanForm,
    escapeHtml,
}
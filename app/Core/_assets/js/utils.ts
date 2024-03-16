import * as DOMPurify from "dompurify";

function showLoader(): void {
    const loader = document.getElementById('--catalog-loader');
    if (loader) {
        loader.classList.remove('d-none');
        loader.classList.add('d-block');
    }
}

function dismissLoader(): void {
    const loader = document.getElementById('--catalog-loader');
    if (loader) {
        loader.classList.remove('d-block');
        loader.classList.add('d-none');
    }
}

function cleanForm(formData: FormData, formElement: HTMLElement): void {
    formData.forEach((element, key, form) => {
        if (typeof element === "string") {
            const clean = DOMPurify.sanitize(element.trim());
            if (clean !== element) {
                const filterElement = formElement.querySelector(`[name="${key}"]`) as HTMLInputElement;
                if (filterElement) {
                    filterElement.value = clean;
                    form.set(key, clean);
                }
            }
        }
    });
}

function escapeHtml(unsafe: string): string {
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

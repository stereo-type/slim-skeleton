import '../css/catalog_filter.css';
import { updateTable } from "./catalog_table";

document.addEventListener('DOMContentLoaded', () => {
    const tables = document.querySelectorAll('.--live-catalog-container');
    tables.forEach((table) => {
        const formElement = table.querySelector<HTMLFormElement>('#--live-catalog-filter');
        const id = table.getAttribute('id');
        const clear = formElement?.querySelector('button[type="submit"][title="clear"]');
        if (clear) {
            clear.addEventListener('click', (evt) => {
                evt.preventDefault();
                const formData = new FormData(formElement!);
                formData.forEach((value, key, form) => {
                    const filterElement = formElement!.querySelector(`[name="${key}"]`);
                    if (filterElement) {
                        if (filterElement.tagName.toLowerCase() === 'select') {
                            (filterElement as HTMLSelectElement).selectedIndex = 0;
                        } else {
                            (filterElement as HTMLInputElement).value = '';
                            form.set(key, '');
                        }
                    }
                });
                updateTable(id!);
            });
        }
    });
});

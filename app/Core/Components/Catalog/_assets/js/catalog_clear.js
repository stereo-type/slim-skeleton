import '../css/catalog_filter.css';
import {updateTable} from "./catalog_table";

document.addEventListener('DOMContentLoaded', function () {
    const tables = document.querySelectorAll('.--live-catalog-container');
    tables.forEach((table) => {
        const formElement = table.querySelector('#--live-catalog-filter');
        const id = table.getAttribute('id');
        const clear = formElement.querySelector('button[type="submit"][title="clear"]');
        if (clear) {
            clear.addEventListener('click', function (evt) {
                evt.preventDefault();
                const formData = new FormData(formElement);
                formData.forEach(function (value, key, form) {
                    const filterElement = formElement.querySelector(`[name="${key}"]`);
                    if (filterElement) {
                        if (filterElement.tagName.toLowerCase() === 'select') {
                            filterElement.selectedIndex = 0;
                        } else {
                            filterElement.value = null;
                            form.set(key, null);
                        }
                    }
                });
                updateTable(id);
            });
        }
    });
});

import { initTable } from "./catalog_table";

document.addEventListener('DOMContentLoaded', () => {
    const tables = document.querySelectorAll<HTMLDivElement>('.--live-catalog-container');
    tables.forEach(table => {
        const tableId = table.getAttribute('id');
        if (tableId) {
            initTable(tableId);
        }
    });
});

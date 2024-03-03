import {initTable} from "./catalog_table";

document.addEventListener('DOMContentLoaded', function () {
    const tables = document.querySelectorAll('.--live-catalog-container');
    tables.forEach((table) => {
        initTable(table.getAttribute('id'));
    });
});

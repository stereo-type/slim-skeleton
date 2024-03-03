import '../css/catalog_filter.css';
import {post} from "../../../../_assets/js/ajax";
import {modal} from "../../../../_assets/js/modal";

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

function sendFilterRequest(formElement) {
    const formData = new FormData(formElement);
    const action = formElement.getAttribute('action');
    if (!action) {
        throw new Error('unspecified action');
    }
    showLoader();
    post(action, formData).then((response) => {
        dismissLoader();
        try {
            if (response.ok) {
                const parentContainer = formElement.closest('.--live-catalog-container');
                const resultContainer = parentContainer.querySelector('.--live-catalog-table-wrap');
                response.json().then(data => {
                    resultContainer.innerHTML = data.table;
                });
            }
        } catch (_) {
            modal(_.message).then(() => {
            });
        }
    });
}

function updateTable(tableContainerElementId) {
    const container = document.getElementById(tableContainerElementId);
    const filterForm = container.querySelector('#--live-catalog-filter');
    sendFilterRequest(filterForm);
}

function initTable(tableContainerElementId) {
    const container = document.getElementById(tableContainerElementId);
    const filterForm = container.querySelector('#--live-catalog-filter');
    filterForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Предотвращаем стандартное действие формы
        sendFilterRequest(filterForm);
    });

}

export {
    initTable,
    updateTable,
}
import '../css/catalog_filter.css';

import {cleanForm, showLoader, dismissLoader, escapeHtml} from "../../../../_assets/js/utils";
import {post} from "../../../../_assets/js/ajax";
import {modal} from "../../../../_assets/js/modal";
import config from "../../../../_assets/js/config";

function sendFilterRequest(formElement) {
    const formData = new FormData(formElement);
    cleanForm(formData, formElement);
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
                const paginbar = parentContainer.querySelector('.--live-catalog-paginbar');
                response.json().then(data => {
                    resultContainer.innerHTML = data['table'];
                    if (data['filter_changed']) {
                        paginbar.innerHTML = data['paginbar'];
                    }
                }).catch(error => {
                        modal(escapeHtml(error.message)).then(() => {
                            if (config.DEBUG) {
                                console.error(error.message);
                            }
                        });
                    }
                );
            }
        } catch (error) {
            modal(error.message).then(() => {
                if (config.DEBUG) {
                    console.error(error.message);
                }
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
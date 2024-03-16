import '../css/catalog_filter.css';

import {cleanForm, showLoader, dismissLoader, escapeHtml} from "../../../../_assets/js/utils";
import {post} from "../../../../_assets/js/ajax";
import {modal} from "../../../../_assets/js/modal";
import {run_entity_action} from "./catalog_actions";
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

    const buttonsGroups = container.querySelectorAll('.--catalog-buttons-group');

    buttonsGroups.forEach(function (buttons) {
        buttons.addEventListener('click', function (evt) {
            const target = evt.target;
            if (target instanceof Element) {
                let actionElement = null;
                if (target.tagName.toLowerCase() === 'i') {
                    const link = target.closest('a.--catalog-buttons-action');
                    if (link) {
                        actionElement = link;
                    }
                } else if (target.tagName.toLowerCase() === 'a' && target.classList.contains('--catalog-buttons-action')) {
                    actionElement = target;
                }

                if (actionElement) {
                    const action = actionElement.getAttribute('action');
                    const id = actionElement.getAttribute('target-id');
                    run_entity_action(id, action);
                }
            }
        })
    });

}

export {
    initTable,
    updateTable,
}
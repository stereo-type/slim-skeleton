import '../css/catalog_filter.css';

import {cleanForm, showLoader, dismissLoader, escapeHtml} from "../../../../_assets/js/utils";
import {post} from "../../../../_assets/js/ajax";
import {modal} from "../../../../_assets/js/modal";
import run_entity_action from "./catalog_actions";
import config from "../../../../_assets/js/config";

function sendFilterRequest(formElement: HTMLFormElement): void {
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
                const parentContainer = formElement.closest<HTMLDivElement>('.--live-catalog-container');
                const resultContainer = parentContainer?.querySelector<HTMLDivElement>('.--live-catalog-table-wrap');
                const paginbar = parentContainer?.querySelector<HTMLDivElement>('.--live-catalog-paginbar');
                response.json().then(data => {
                    if (resultContainer) resultContainer.innerHTML = data['table'];
                    if (data['filter_changed'] && paginbar) paginbar.innerHTML = data['paginbar'];
                }).catch(error => {
                    modal(escapeHtml(error.message)).then(() => {
                        if (config.DEBUG) {
                            console.error(error.message);
                        }
                    });
                });
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

function updateTable(tableContainerElementId: string): void {
    const container = document.getElementById(tableContainerElementId);
    const filterForm = container?.querySelector<HTMLFormElement>('#--live-catalog-filter');
    if (!filterForm) return;
    sendFilterRequest(filterForm);
}

function initTable(tableContainerElementId: string): void {
    const url = window.location.pathname;
    const container = document.getElementById(tableContainerElementId);
    if (!container) return;
    const filterForm = container.querySelector<HTMLFormElement>('#--live-catalog-filter');
    if (!filterForm) return;
    filterForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Предотвращаем стандартное действие формы
        sendFilterRequest(filterForm!);
    });


    container.addEventListener('click', function (evt) {
        const target = evt.target as HTMLElement;
        const actionElement = target.closest('a.--catalog-buttons-action');
        if (actionElement) {
            const action = actionElement.getAttribute('action');
            const id = actionElement.getAttribute('target-id');
            if (action && id) {
                run_entity_action(id, action, url, () => updateTable(tableContainerElementId))
                    ?.then((modal) => {

                        // modal.getElement().addEventListener('hide.bs.modal', function (evt) {
                        //     // updateTable(tableContainerElementId);
                        // });
                    });


            }
        }
    });
}

export {
    initTable,
    updateTable,
};

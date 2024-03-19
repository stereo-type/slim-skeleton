import {modal, ModalActionType, ModalTemplateAjax, ModalTemplateParams, SlimModal} from "../../../../_assets/js/modal";
import {post} from "../../../../_assets/js/ajax";

interface ProgressActions {
    [action: string]: boolean;
}

const _progress_actions: ProgressActions = {};

function is_action_in_progress(id: string, action: string): boolean {
    return _progress_actions.hasOwnProperty(action);
}

function run_entity_action(id: string, action: string): Promise<SlimModal | null> {
    if (!is_action_in_progress(id, action)) {
        return _run_entity_action(id, action);
    } else {
        console.log(`already running ${action}`);
    }
    return null;
}

function _run_edit(id: string): Promise<SlimModal | null> {

    const template = new ModalTemplateAjax(
        `edit_${id}`,
        '/demo_user_entities/form/' + id,
        {'id': id, 'modal': true},
        new ModalTemplateParams(ModalActionType.none, 'Редактировать')
    );

    const modalInstance = modal(template);
    modalInstance.then((modal) => {
        if (modal instanceof SlimModal) {
            const form = modal.getElement().querySelector('#entity_form') as HTMLFormElement | null;
            if (form) {
                const buttonClose = form.querySelector('button[type="button"][action="cancel"]');
                if (buttonClose) {
                    buttonClose.addEventListener('click', function (evt) {
                        evt.preventDefault();
                        modal.hide();
                    })
                }
                form.addEventListener('submit', function (evt) {
                    evt.preventDefault();
                    const data = new FormData(form);
                    post(template.get_route(), data, form).then(async response => {
                        /**Если ошибка, то она должна уже быть обработана*/
                        if (response.ok) {
                            const response_1 = await response.json();
                            if (response_1['success']) {
                                modal.hide();
                                /**AFTER SUCCESS**/
                            } else {
                                /**На всякий случай*/
                                alert('Ошибка сохранения формы');
                                console.error('Ошибка сохранения формы');
                            }
                        }
                    });
                })
            }
        }
    });
    return modalInstance;
}

function _run_entity_action(id: string, action: string): Promise<SlimModal | null> {
    switch (action) {
        case 'edit':
            return _run_edit(id);
        default:
            console.error(`not specified action ${action}`);
            break;
    }
}

export {
    run_entity_action,
};

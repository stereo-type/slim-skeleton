import {modal, ModalTemplate} from "../../../../_assets/js/modal";

const _progress_actions = {};

function is_action_in_progress(id, action) {
    return _progress_actions.hasOwnProperty(action);
}

function run_entity_action(id, action) {
    if (!is_action_in_progress(id, action)) {
        _run_entity_action(id, action);
    } else {
        console.log(`already running ${action}`);
    }
}

function _run_edit(id) {
    const template = new ModalTemplate(
        `edit_${id}`,
        'html',
        modalContent,
        modalTitle = '',
        modalClasses = '');
    modal('asdas');
}

function _run_entity_action(id, action) {
    // _progress_actions[action] = id;
    switch (action) {
        case 'edit':
            _run_edit(id);
            break;
        default:
            console.error(`not specified action ${action}`)
            break;
    }
}

export {
    run_entity_action,
}
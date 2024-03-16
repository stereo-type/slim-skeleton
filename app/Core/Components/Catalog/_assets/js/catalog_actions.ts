import { modal, ModalTemplate } from "../../../../_assets/js/modal";

interface ProgressActions {
    [action: string]: boolean;
}

const _progress_actions: ProgressActions = {};

function is_action_in_progress(id: string, action: string): boolean {
    return _progress_actions.hasOwnProperty(action);
}

function run_entity_action(id: string, action: string): void {
    if (!is_action_in_progress(id, action)) {
        _run_entity_action(id, action);
    } else {
        console.log(`already running ${action}`);
    }
}

function _run_edit(id: string): void {
    // const template = new ModalTemplate(
    //     `edit_${id}`,
    //     'html',
    //     'modalContent',
    //     'title'
    // );
    // modal('asdas');
}

function _run_entity_action(id: string, action: string): void {
    // _progress_actions[action] = id;
    switch (action) {
        case 'edit':
            _run_edit(id);
            break;
        default:
            console.error(`not specified action ${action}`);
            break;
    }
}

export {
    run_entity_action,
};

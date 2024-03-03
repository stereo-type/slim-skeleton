import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['name', 'output']

    search() {

        // this.outputTarget.textContent = `Hello, ${this.nameTarget.value}!`
    }

    update() {

    }
}
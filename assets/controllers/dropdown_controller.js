import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dropdown'];

    drop(event) {
        event.preventDefault();

        this.dropdownTarget.classList.toggle('hidden');
    }
}

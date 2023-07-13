import {Controller} from '@hotwired/stimulus';
import {Notyf} from 'notyf';

import 'notyf/notyf.min.css';

export default class extends Controller {
    static values = {
        type: {
            type: String, default: 'success'
        }, text: {
            type: String, default: ''
        },
    }

    connect() {
        const notyf = new Notyf({
            duration: 5000, position: {
                x: 'right', y: 'bottom',
            }, types: [{
                type: 'warning', background: 'orange', icon: {
                    className: 'material-icons', tagName: 'i', text: 'warning'
                }
            }, {
                type: 'error', background: 'indianred', dismissible: true
            }, {
                type: 'success', background: 'green', dismissible: true
            }]
        });

        notyf.open({
            type: this.typeValue,
            message: this.textValue
        });
    }
}

import {Controller} from '@hotwired/stimulus';
import {Notyf} from 'notyf';

import 'notyf/notyf.min.css';

export default class extends Controller {
    static values = {
        flashes: {
            type: "object" | "array",
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

        if (typeof this.flashesValue === "object") {
            for (const type in this.flashesValue) {
                this.flashesValue[type].forEach(message => {
                    notyf.open({
                        type, message
                    });
                })
            }
        }
    }
}

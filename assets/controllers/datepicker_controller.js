import {Controller} from '@hotwired/stimulus';
import datepicker from 'js-datepicker';
import 'js-datepicker/src/datepicker.scss';

export default class extends Controller {
    static targets = ['dateStart', 'dateEnd', 'labelDateStart', 'labelDateEnd'];

    connect() {
        datepicker(this.dateStartTarget, {
            id: 1, onSelect: instance => {
                this.changeDate(this.dateStartTarget, instance.dateSelected, 'start');
            }, minDate: new Date(), sibling: this.dateEndTarget,
        })
        datepicker(this.dateEndTarget, {
            id: 1, onSelect: instance => {
                this.changeDate(this.dateEndTarget, instance.dateSelected, 'end');
            }, minDate: new Date(), sibling: this.dateStartTarget,
        })
    }

    changeDate(target, instance, dateType) {
        switch (dateType) {
            case 'start':
                this.labelDateStartTarget.innerText = this.date_ddmmyyyy(instance);
                break;
            case 'end':
                this.labelDateEndTarget.innerText = this.date_ddmmyyyy(instance);
                break;
        }
        target.value = this.formattedDate(instance);
    }

    formattedDate(date) {
        let year = date.getFullYear();
        let month = (date.getMonth() + 1).toString().padStart(2, "0"); // Mois (valeurs de 01 à 12)
        let day = date.getDate().toString().padStart(2, "0"); // Jour (valeurs de 01 à 31)
        let hours = date.getHours().toString().padStart(2, "0"); // Heures (valeurs de 00 à 23)
        let minutes = date.getMinutes().toString().padStart(2, "0"); // Minutes (valeurs de 00 à 59)

        return year + "-" + month + "-" + day + "T" + hours + ":" + minutes;
    }

    date_ddmmyyyy(date) {
        let year = date.getFullYear();
        let month = (date.getMonth() + 1).toString().padStart(2, "0"); // Mois (valeurs de 01 à 12)
        let day = date.getDate().toString().padStart(2, "0"); // Jour (valeurs de 01 à 31)

        return day + "/" + month + "/" + year;
    }

    resetForm(event) {
        event.preventDefault();

        let inputs = this.element.getElementsByTagName('input');

        for (let input of inputs) {
            input.value = ''
        }
        this.labelDateEndTarget.innerText = '--/--/----';
    }
}

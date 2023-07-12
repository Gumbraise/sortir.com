import {Controller} from '@hotwired/stimulus';
import datepicker from 'js-datepicker';
import 'js-datepicker/dist/datepicker.min.css';

export default class extends Controller {
    static targets = ['date1', 'date2'];

    connect() {
        datepicker(this.date1Target, {
            id: 1, onSelect: (instance, date) => {
                this.changeDate(this.date1Target, date);
            }, minDate: new Date(), sibling: this.date2Target,
        })
        datepicker(this.date2Target, {
            id: 1, onSelect: instance => {
                this.changeDate(this.date2Target, instance.dateSelected);
            }, minDate: new Date(), sibling: this.date1Target,
        })
    }

    changeDate(target, instance) {
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

    resetDate(event) {
        event.preventDefault();

        this.date1Target.value = '';
        this.date2Target.value = '';
    }
}

import {Controller} from "@hotwired/stimulus";
import Swal from "sweetalert2";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        title: String, text: String, icon: String, confirmationButtonText: String, submitAsync: Boolean
    }

    onSubmit(event) {
        event.preventDefault();

        Swal.fire({
            title: this.titleValue || null,
            text: this.textValue || null,
            icon: this.iconValue || null,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: this.confirmationButtonTextValue || 'Oui',
            cancelButtonText: 'Annuler',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return this.submitForm();
            }
        });
    }

    async onSortieCancel(event) {
        event.preventDefault();

        const {value: text} = await Swal.fire({
            input: 'textarea',
            inputLabel: 'Raison de l\'annulation',
            inputPlaceholder: 'Raison de l\'annulation...',
            inputAttributes: {
                'aria-label': 'Raison de l\'annulation'
            },
            title: this.titleValue || null,
            text: this.textValue || null,
            icon: this.iconValue || null,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: this.confirmationButtonTextValue || 'Oui',
            cancelButtonText: 'Annuler',
            showLoaderOnConfirm: true,
            inputValidator: (value) => {
                return this.submitForm({
                    'raisonsAnnulation': value
                });
            }
        })
    }

    async submitForm(data) {
        if (!this.submitAsyncValue) {
            this.element.submit();

            return;
        }

        const formData = new FormData(this.element);
        for (const property in data) {
            formData.append(property, data[property]);
        }

        await fetch(this.element.action, {
            method: this.element.method,
            body: new URLSearchParams(formData)
        }).then(() => {
            window.location.reload();
        });
    }
}
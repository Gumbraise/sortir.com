import {Controller} from '@hotwired/stimulus';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIconPng from "leaflet/dist/images/marker-icon.png"

export default class extends Controller {
    static values = {
        'latitude': Number,
        'longitude': Number,
    };

    connect() {
        const map = L.map(this.element, {
            center: [this.latitudeValue, this.longitudeValue],
            zoom: 16,
        })

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        const myIcon = L.icon({
            iconUrl: markerIconPng,
            iconSize: [25, 41],
            iconAnchor: [12.5, 41],
            popupAnchor: [0, 0],
            tooltipAnchor: [0, 0]

        });

        L.marker([this.latitudeValue, this.longitudeValue], {icon: myIcon}).addTo(map);
    }

}

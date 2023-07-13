import { Controller } from '@hotwired/stimulus';
import Swiper from 'swiper/bundle';

import 'swiper/css/bundle';

export default class extends Controller {
    connect() {
        new Swiper(this.element, {
            breakpoints: {
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 50,
                },
            },
            slidesPerView: 1,
            spaceBetween: 20,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            loop: true
        });
    }
}

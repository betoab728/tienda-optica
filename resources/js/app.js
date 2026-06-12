import Swiper from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import TomSelect from 'tom-select';

window.TomSelect = TomSelect;

document.addEventListener('DOMContentLoaded', () => {
    const slider = document.querySelector('.mySwiper');
    if (!slider) {
        return;
    }

    new Swiper(slider, {
        modules: [Navigation, Pagination, Autoplay],
        loop: true,
        autoplay: { delay: 3500, disableOnInteraction: false },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
});
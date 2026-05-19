import Swiper from 'swiper';
import { Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

const init = () => {
  document.querySelectorAll('[data-product-swiper]').forEach((el) => {
    new Swiper(el, {
      modules: [Pagination],
      slidesPerView: 1,
      spaceBetween: 0,
      pagination: {
        el: el.querySelector('.swiper-pagination'),
        clickable: true,
      },
    });
  });
};

document.addEventListener('DOMContentLoaded', init);

import axios from 'axios';
import sort from '@alpinejs/sort';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Alpine.plugin(sort);

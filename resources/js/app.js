import './bootstrap';

import Alpine from 'alpinejs';

import wysiwyg from './wysiwyg';

window.Alpine = Alpine;

Alpine.data('wysiwyg', wysiwyg);

Alpine.start();

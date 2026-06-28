import './bootstrap';

import Alpine from 'alpinejs';

import wysiwyg from './wysiwyg';
import sortable from './sortable';
import structureEditor from './structureEditor';

window.Alpine = Alpine;

Alpine.data('wysiwyg', wysiwyg);
Alpine.data('sortable', sortable);
Alpine.data('structureEditor', structureEditor);

Alpine.start();

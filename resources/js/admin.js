import Alpine from 'alpinejs';
import fileDrop from './file-drop';

Alpine.data('fileDrop', fileDrop);

window.Alpine = Alpine;
Alpine.start();

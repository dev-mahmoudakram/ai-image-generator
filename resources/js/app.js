import cameraCapture from './camera';
import fileDrop from './file-drop';
import bootSelfieUploader from './selfie-uploader';

document.addEventListener('alpine:init', () => {
    window.Alpine?.data('cameraCapture', cameraCapture);
    window.Alpine?.data('fileDrop', fileDrop);
});

bootSelfieUploader();

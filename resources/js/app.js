import 'intl-tel-input/dist/css/intlTelInput.css';
import cameraCapture from './camera';
import fileDrop from './file-drop';
import bootSelfieUploader from './selfie-uploader';
import phoneInput from './phone-input';

document.addEventListener('alpine:init', () => {
    window.Alpine?.data('cameraCapture', cameraCapture);
    window.Alpine?.data('fileDrop', fileDrop);
    window.Alpine?.data('phoneInput', phoneInput);
});

bootSelfieUploader();

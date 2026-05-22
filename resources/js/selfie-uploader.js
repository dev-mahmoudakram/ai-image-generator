const initialized = new WeakSet();

export default function bootSelfieUploader() {
    const initAll = () => {
        document.querySelectorAll('[data-selfie-uploader]').forEach((element) => {
            if (!initialized.has(element)) {
                initialized.add(element);
                initSelfieUploader(element);
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll, { once: true });
    } else {
        initAll();
    }

    const observer = new MutationObserver(initAll);

    if (document.body) {
        observer.observe(document.body, { childList: true, subtree: true });
    } else {
        document.addEventListener('DOMContentLoaded', () => {
            observer.observe(document.body, { childList: true, subtree: true });
        }, { once: true });
    }
}

function initSelfieUploader(root) {
    const uploadUrl = root.dataset.uploadUrl;
    const csrfToken = root.dataset.csrfToken;
    const acceptedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    const elements = {
        inputs: root.querySelector('[data-selfie-inputs]'),
        success: root.querySelector('[data-selfie-success]'),
        status: root.querySelector('[data-selfie-status]'),
        dropZone: root.querySelector('[data-selfie-drop-zone]'),
        fileInput: root.querySelector('[data-selfie-file]'),
        preview: root.querySelector('[data-selfie-preview]'),
        fileName: root.querySelector('[data-selfie-file-name]'),
        video: root.querySelector('[data-camera-video]'),
        canvas: root.querySelector('[data-camera-canvas]'),
        placeholder: root.querySelector('[data-camera-placeholder]'),
        openCamera: root.querySelector('[data-camera-open]'),
        takePhoto: root.querySelector('[data-camera-capture]'),
        cancelCamera: root.querySelector('[data-camera-cancel]'),
        retakePhoto: root.querySelector('[data-camera-retake]'),
    };

    let stream = null;
    let uploading = false;

    const setStatus = (message, isError = false) => {
        if (!elements.status) return;

        elements.status.textContent = message || '';
        elements.status.hidden = !message;
        elements.status.classList.toggle('camera__error', isError);
    };

    const setBusy = (isBusy) => {
        uploading = isBusy;
        [elements.openCamera, elements.takePhoto, elements.cancelCamera, elements.retakePhoto, elements.fileInput].forEach((element) => {
            if (element) element.disabled = isBusy;
        });
    };

    const showCamera = (mode) => {
        if (elements.video) elements.video.hidden = mode !== 'video';
        if (elements.canvas) elements.canvas.hidden = mode !== 'canvas';
        if (elements.placeholder) elements.placeholder.hidden = mode !== 'placeholder';
        if (elements.openCamera) elements.openCamera.hidden = mode !== 'placeholder';
        if (elements.takePhoto) elements.takePhoto.hidden = mode !== 'video';
        if (elements.cancelCamera) elements.cancelCamera.hidden = mode !== 'video';
        if (elements.retakePhoto) elements.retakePhoto.hidden = mode !== 'canvas';
    };

    const stopCamera = () => {
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }

        if (elements.video) {
            elements.video.srcObject = null;
        }
    };

    const showSuccess = () => {
        stopCamera();
        if (elements.inputs) elements.inputs.hidden = true;
        if (elements.success) elements.success.hidden = false;
        setStatus('');
    };

    const validateFile = (file) => {
        if (!file) {
            return 'Please choose an image first.';
        }

        if (!acceptedTypes.includes(file.type)) {
            return 'Please upload a JPG, PNG, or WebP image.';
        }

        if (file.size > 8 * 1024 * 1024) {
            return 'Please upload an image smaller than 8 MB.';
        }

        return null;
    };

    const showPreview = (file) => {
        if (elements.fileName) {
            elements.fileName.textContent = file.name;
            elements.fileName.hidden = false;
        }

        if (elements.preview && file.type.startsWith('image/')) {
            elements.preview.src = URL.createObjectURL(file);
            elements.preview.hidden = false;
        }
    };

    const uploadFile = async (file) => {
        if (!uploadUrl) {
            setStatus('Upload is not ready yet. Please refresh and try again.', true);
            return;
        }

        const validationError = validateFile(file);

        if (validationError) {
            setStatus(validationError, true);
            return;
        }

        setBusy(true);
        setStatus('Uploading selfie...');

        try {
            const formData = new FormData();
            formData.append('selfie', file);

            const response = await fetch(uploadUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
                credentials: 'same-origin',
            });

            if (!response.ok) {
                const payload = await response.json().catch(() => ({}));
                const message = payload.message || Object.values(payload.errors || {})[0]?.[0] || 'Upload failed. Please try again.';
                throw new Error(message);
            }

            showSuccess();
        } catch (error) {
            setStatus(error.message || 'Upload failed. Please try again.', true);
        } finally {
            setBusy(false);
        }
    };

    elements.dropZone?.addEventListener('click', () => elements.fileInput?.click());
    elements.dropZone?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            elements.fileInput?.click();
        }
    });
    elements.dropZone?.addEventListener('dragover', (event) => {
        event.preventDefault();
        elements.dropZone.classList.add('drop-zone--active');
    });
    elements.dropZone?.addEventListener('dragleave', () => elements.dropZone.classList.remove('drop-zone--active'));
    elements.dropZone?.addEventListener('drop', (event) => {
        event.preventDefault();
        elements.dropZone.classList.remove('drop-zone--active');
        const file = event.dataTransfer?.files?.[0];
        if (file) {
            showPreview(file);
            uploadFile(file);
        }
    });

    elements.fileInput?.addEventListener('click', (event) => event.stopPropagation());
    elements.fileInput?.addEventListener('change', () => {
        const file = elements.fileInput.files?.[0];
        if (file) {
            showPreview(file);
            uploadFile(file);
        }
    });

    elements.openCamera?.addEventListener('click', async () => {
        setStatus('');

        if (!navigator.mediaDevices?.getUserMedia) {
            setStatus(window.isSecureContext === false
                ? 'Camera requires HTTPS or localhost. Use the upload option below.'
                : 'Camera capture is not supported in this browser. Use the upload option below.', true);
            return;
        }

        try {
            stopCamera();
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 3840 }, height: { ideal: 3840 } },
                audio: false,
            });
            elements.video.srcObject = stream;
            await elements.video.play();
            showCamera('video');
        } catch (error) {
            setStatus(cameraErrorMessage(error), true);
            showCamera('placeholder');
        }
    });

    elements.takePhoto?.addEventListener('click', () => {
        if (!elements.video?.videoWidth || uploading) return;

        elements.canvas.width = elements.video.videoWidth;
        elements.canvas.height = elements.video.videoHeight;
        elements.canvas.getContext('2d').drawImage(elements.video, 0, 0);
        stopCamera();
        showCamera('canvas');

        elements.canvas.toBlob((blob) => {
            if (!blob) {
                setStatus('Could not capture the image. Please try again.', true);
                return;
            }

            uploadFile(new File([blob], 'selfie.png', { type: 'image/png' }));
        }, 'image/png');
    });

    elements.cancelCamera?.addEventListener('click', () => {
        stopCamera();
        showCamera('placeholder');
    });

    elements.retakePhoto?.addEventListener('click', () => elements.openCamera?.click());
    window.addEventListener('beforeunload', stopCamera);
    showCamera('placeholder');
}

function cameraErrorMessage(error) {
    if (!error) return 'Could not access the camera. Please try again.';
    if (error.name === 'NotAllowedError') return 'Camera permission denied. Please allow access and try again.';
    if (error.name === 'NotFoundError') return 'No camera found on this device.';
    if (error.name === 'NotReadableError') return 'Camera is in use by another application.';
    if (error.name === 'OverconstrainedError') return 'Camera does not meet the required constraints.';

    return 'Could not access the camera. Please try again.';
}

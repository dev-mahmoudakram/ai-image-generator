/**
 * Alpine camera capture component.
 *
 * Handles: permission request, live preview, capture-to-blob, stream teardown.
 * Hands the resulting File to Livewire via $wire.upload('selfie', file, ...).
 *
 * x-ref bindings expected: video, canvas
 * Exposes: streaming, captured, uploading, uploadProgress, error, cameraAvailable
 */
export default function cameraCapture($wire = null) {
    return {
        stream:          null,
        streaming:       false,
        captured:        false,
        uploading:       false,
        uploadProgress:  0,
        error:           '',
        cameraAvailable: true,   // set false on init if getUserMedia is missing

        init() {
            // Check for camera API availability up-front so the UI can adapt
            if (!navigator.mediaDevices?.getUserMedia) {
                this.cameraAvailable = false;
                this.error = window.isSecureContext === false
                    ? 'Camera requires a secure connection (HTTPS or localhost).'
                    : 'Camera capture is not supported in this browser.';
            }

            this.boundStop = () => this.stop();
            window.addEventListener('beforeunload', this.boundStop);
        },

        destroy() {
            window.removeEventListener('beforeunload', this.boundStop);
            this.stop();
        },

        async start() {
            if (!this.cameraAvailable) return;
            this.error = '';
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 1280 } },
                    audio: false,
                });
                const video = this.$refs.video;
                video.srcObject = this.stream;
                await video.play().catch(() => {});
                this.streaming = true;
                this.captured  = false;
            } catch (e) {
                this.error = this._errorMessage(e);
            }
        },

        capture() {
            const video  = this.$refs.video;
            const canvas = this.$refs.canvas;
            if (!video || !canvas || !video.videoWidth) return;

            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob((blob) => {
                if (!blob) {
                    this.error = 'Could not capture the image. Please try again.';
                    return;
                }
                const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
                this.captured = true;
                this.stop();
                this._upload(file);
            }, 'image/jpeg', 0.92);
        },

        retake() {
            if (this.uploading) return;
            this.captured = false;
            this.uploadProgress = 0;
            this.start();
        },

        stop() {
            if (this.stream) {
                this.stream.getTracks().forEach((t) => t.stop());
                this.stream = null;
            }
            this.streaming = false;
        },

        _errorMessage(e) {
            if (!e)                               return 'Camera not available.';
            if (e.name === 'NotAllowedError')     return 'Camera permission denied. Please allow access and try again.';
            if (e.name === 'NotFoundError')       return 'No camera found on this device.';
            if (e.name === 'NotReadableError')    return 'Camera is in use by another application.';
            if (e.name === 'OverconstrainedError') return 'Camera does not meet the required constraints.';
            return 'Could not access the camera. Please try again.';
        },

        _upload(file) {
            if (!$wire?.upload) {
                this.captured = false;
                this.error = 'Image captured, but upload is not available. Please use the upload option below.';
                return;
            }

            this.error = '';
            this.uploading = true;
            this.uploadProgress = 0;

            $wire.upload(
                'selfie',
                file,
                () => {
                    this.uploading = false;
                    this.uploadProgress = 100;
                },
                () => {
                    this.uploading = false;
                    this.captured = false;
                    this.uploadProgress = 0;
                    this.error = 'Upload failed. Please try again or use the upload option below.';
                },
                (event) => {
                    this.uploadProgress = event.detail?.progress ?? this.uploadProgress;
                },
                () => {
                    this.uploading = false;
                    this.captured = false;
                    this.uploadProgress = 0;
                    this.error = 'Upload cancelled.';
                },
            );
        },
    };
}

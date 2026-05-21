/**
 * Alpine camera capture component.
 *
 * Handles: permission request, live preview, capture-to-blob,
 * stream teardown. Hands the resulting File to Livewire via
 * `@this.upload('selfie', file)`.
 *
 * Public API (used from Blade):
 *   x-data="cameraCapture"
 *   x-ref="video"
 *   x-ref="canvas"
 *   @click="start" / "capture" / "retake" / "stop"
 *   x-show / x-text bindings against: streaming, captured, error
 */
export default function cameraCapture() {
    return {
        stream: null,
        streaming: false,
        captured: false,
        error: '',

        init() {
            this.boundStop = () => this.stop();
            window.addEventListener('beforeunload', this.boundStop);
        },

        destroy() {
            window.removeEventListener('beforeunload', this.boundStop);
            this.stop();
        },

        async start() {
            this.error = '';
            try {
                if (!navigator.mediaDevices?.getUserMedia) {
                    throw new Error('UNSUPPORTED');
                }
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width:  { ideal: 1280 },
                        height: { ideal: 1280 },
                    },
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
                this.$wire.upload('selfie', file, () => {
                    this.captured = true;
                    this.stop();
                });
            }, 'image/jpeg', 0.92);
        },

        retake() {
            this.captured = false;
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
            if (!e) return 'Camera not available.';
            if (e.name === 'NotAllowedError')      return 'Camera permission denied.';
            if (e.name === 'NotFoundError')        return 'No camera was found on this device.';
            if (e.name === 'NotReadableError')     return 'Camera is in use by another application.';
            if (e.message === 'UNSUPPORTED')       return 'Your browser does not support camera capture.';
            return 'Could not access the camera. Please try again.';
        },
    };
}

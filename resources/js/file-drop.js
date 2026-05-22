/**
 * Alpine drop-zone component.
 *
 * Visual-only: preview, badge, drag state, upload spinner.
 * Actual upload is handled by wire:model on the hidden input (Livewire)
 * or by regular form submission (admin forms).
 *
 * No dependency on $wire — Livewire events bubble up and drive state.
 */
export default function fileDrop() {
    return {
        dragging:   false,
        uploading:  false,
        fileName:   null,
        previewUrl: null,
        errorMsg:   null,

        init() {
            // Track Livewire's upload lifecycle events (bubbled from the hidden input)
            this.$el.addEventListener('livewire-upload-start',  () => { this.uploading = true;  this.errorMsg = null; });
            this.$el.addEventListener('livewire-upload-finish', () => { this.uploading = false; });
            this.$el.addEventListener('livewire-upload-error',  () => { this.uploading = false; this.errorMsg = 'Upload failed. Please try again.'; });
        },

        onDrop(e) {
            this.dragging = false;
            const file = e.dataTransfer?.files?.[0];
            if (!file) return;
            this._showPreview(file);
            this._setInputFile(file);   // triggers wire:model change listener
        },

        onInputChange() {
            // Skip synthetic events we dispatched ourselves (drag path)
            if (this._programmatic) return;
            const file = this.$refs.input?.files?.[0];
            if (file) this._showPreview(file);
            // Livewire's wire:model change listener handles the actual upload
        },

        _showPreview(file) {
            this.errorMsg  = null;
            this.fileName  = file.name;
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (ev) => { this.previewUrl = ev.target.result; };
                reader.readAsDataURL(file);
            }
        },

        // For drag-drop: put the file into the hidden input and fire change
        // so wire:model (or native form submission) picks it up.
        _setInputFile(file) {
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                this._programmatic = true;      // guard against re-entry in onInputChange
                this.$refs.input.files = dt.files;
                this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
            } catch (_) { /* DataTransfer not supported */ }
            finally    { this._programmatic = false; }
        },
    };
}

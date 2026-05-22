import intlTelInput from 'intl-tel-input';

export default function phoneInput() {
    return {
        _debounce: null,

        init() {
            // Store iti on window (raw) — Alpine's Proxy wrapper breaks
            // intl-tel-input's private class fields (#selectedCountry etc.)
            window.__iti = intlTelInput(this.$refs.input, {
                initialCountry: 'sa',
                separateDialCode: true,
                loadUtils: () => import('intl-tel-input/utils'),
                dropdownContainer: document.body,
            });

            // Capture raw references so the sync closure doesn't go through Proxy
            const inputEl = this.$refs.input;
            const wire    = this.$wire;

            const buildE164 = () => {
                const { dialCode = '966' } = window.__iti.getSelectedCountryData();
                let local = inputEl.value.replace(/\D/g, '');
                if (!local) return '';
                if (local.startsWith('0')) local = local.slice(1);
                return `+${dialCode}${local}`;
            };

            const syncNow = () => {
                const e164 = buildE164();
                if (e164) wire.set('phone', e164);
            };

            // Exposed for the form's x-on:submit to await before calling submitForm
            window.__syncPhone = async () => {
                const e164 = buildE164();
                if (e164) await wire.set('phone', e164);
            };

            inputEl.addEventListener('input', () => {
                const cleaned = inputEl.value.replace(/\D/g, '');
                if (inputEl.value !== cleaned) inputEl.value = cleaned;

                clearTimeout(this._debounce);
                this._debounce = setTimeout(syncNow, 300);
            });

            inputEl.addEventListener('countrychange', () => {
                if (inputEl.value) syncNow();
            });
        },

        destroy() {
            window.__iti?.destroy();
            window.__iti       = null;
            window.__syncPhone = null;
            clearTimeout(this._debounce);
        },
    };
}

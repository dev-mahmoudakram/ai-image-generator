import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

import cameraCapture from './camera';

Alpine.data('cameraCapture', cameraCapture);

Livewire.start();

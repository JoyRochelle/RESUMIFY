import "../css/app.css";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import "./shared/ai-quota-widget";
import "./shared/count-up";
import "./shared/landing-anime";
import "./shared/template-preview-frames";

Alpine.plugin(collapse);
window.Alpine = Alpine;
// Alpine.start() is called by @livewireScripts in each layout.
// Do NOT call it here — calling it twice breaks Livewire components.

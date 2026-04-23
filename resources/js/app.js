// import "./bootstrap";
import { createIcons, icons } from "lucide";

// tampilkan icon lucide saat halaman pertama kali dimuat
document.addEventListener("DOMContentLoaded", () => {
    createIcons({ icons });
});

// tampilkan icon lucide (promise)
const obs = new MutationObserver(() => {
    clearTimeout(window.__lucideDebounce);
    window.__lucideDebounce = setTimeout(() => {
        createIcons({ icons });
    }, 50);
});

obs.observe(document.body, {
    childList: true,
    subtree: true,
});

window.toggleClass = function (id, className) {
    const element = document.getElementById(id);
    element.classList.toggle(className);
};

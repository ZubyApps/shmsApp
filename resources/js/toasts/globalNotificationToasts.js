import * as bootstrap from 'bootstrap';
import { ucFirst } from "../helpers";

const showToast = (message, type = 'success') => {
    const toastEl = document.getElementById('globalToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastTitle = document.getElementById('toastTitle');

    // 1. Set the content
    toastMessage.textContent = message;
    toastTitle.textContent = type === 'success' ? 'Success' : ucFirst(type);

    // 2. Add some color based on type (bg-success, bg-danger, etc.)
    toastEl.classList.remove('text-white', 'bg-success', 'bg-danger', 'bg-warning');
    
    if (type === 'success') toastEl.classList.add('bg-primary', 'text-white');
    if (type === 'error') toastEl.classList.add('bg-danger', 'text-white');
    if (type === 'warning') toastEl.classList.add('bg-warning', 'text-dark');

    if (type !== 'success' && navigator.vibrate) {
        navigator.vibrate(200); // Give a little haptic feedback on errors
    }

    // 3. Initialize and Show
    const bsToast = new bootstrap.Toast(toastEl, { delay: 5000 });
    bsToast.show();
};

export{showToast};
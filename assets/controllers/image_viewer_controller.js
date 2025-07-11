import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['img'];

    open(event) {
        const src = event.currentTarget.getAttribute('src');
        const alt = event.currentTarget.getAttribute('alt') || '';
        const overlay = document.createElement('div');
        overlay.className = 'image-viewer-overlay';

        const img = document.createElement('img');
        img.src = src;
        img.alt = alt;
        img.className = 'image-viewer-img';

        overlay.appendChild(img);

        overlay.addEventListener('click', () => {
            overlay.remove();
        });

        document.body.appendChild(overlay);
    }
}
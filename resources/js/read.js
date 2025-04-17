import { post } from './ajax';
import { bootstrapAlert } from './components';

window.addEventListener('DOMContentLoaded', function () {
    const toggleTickBtn = this.document.querySelector('.toggle-tick-btn');
    const toggleTickIcon = this.document.querySelector('.toggle-tick-icon');

    toggleTickBtn.addEventListener('click', () => {
        const blogUUId = toggleTickBtn.getAttribute('data-blog-uuid');

        post(`/blog/${blogUUId}/tick`).then(response => response.json()).then(response => {
            if (response.interaction_error) {
                bootstrapAlert('Please login to interact with this post!', 'info');
            } else if (response.ok) {
                toggleTickIcon.classList.toggle('bi-check-circle');
                toggleTickIcon.classList.toggle('bi-check-circle-fill');
            }
        });
    });

});

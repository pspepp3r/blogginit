import { post } from './ajax';
import { addLoader, bootstrapAlert } from './components';

window.addEventListener('DOMContentLoaded', function () {
    const updateProfileBtn = this.document.querySelector('.update-profile-btn');
    const updateSecurityBtn = this.document.querySelector('.update-security-btn');

    if (updateProfileBtn) {
        updateProfileBtn.addEventListener('click', function (e) {
            e.preventDefault();

            addLoader(this);

            const form = this.closest('form')
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            post(form.action, data, form).then(response => {
                if (response.ok) {
                    bootstrapAlert('Profile settings updated!');
                }
            });
        });
    }

    if (updateSecurityBtn) {
        updateSecurityBtn.addEventListener('click', function (e) {
            e.preventDefault();

            addLoader(this);

            const form = this.closest('form')
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            post(form.action, data, form).then(response => {
                if (response.ok) {
                    bootstrapAlert('Profile settings updated!');
                }
            });
        });
    }
});

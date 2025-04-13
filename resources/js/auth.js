import "../css/auth.scss";

import { post } from './ajax';
import { addLoader } from './components';

window.addEventListener('DOMContentLoaded', function () {
    const loginBtn = this.document.querySelector('.login-btn');
    const twoFALogin = this.document.querySelector('.two-fa-login-btn');
    const registerBtn = this.document.querySelector('.register-btn');

    if (loginBtn) {
        loginBtn.addEventListener('click', function (e) {
            e.preventDefault();

            addLoader(this, "Login");

            const form = this.closest('form')
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            post(form.action, data, form).then(response => response.json()).then(response => {
                if (response.two_factor) {
                    window.location = '/two-factor-form';
                } else {
                    window.location = '/';
                }
            });
        });
    }

    if (registerBtn) {
        registerBtn.addEventListener('click', function (e) {
            e.preventDefault();

            addLoader(this, "Register");

            const form = this.closest('form')
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            post(form.action, data, form).then(response => {
                if (response.ok) {
                    window.location = '/dashboard';
                }
            });
        });
    }

    if (twoFALogin) {
        twoFALogin.addEventListener('click', function (e) {
            let code = '';
            const pinArray = document.querySelectorAll('.pin-input');

            for (const pinBox of pinArray) {
                code += pinBox.value;
            }

            addLoader(this, "Login");

            const form = this.closest('form')
            const data = { code }

            post(form.action, data, form).then(response => {
                if (response.ok) {
                    window.location = '/dashboard';
                }
            });
        });
    }

    // 2FA ONLY
    document.querySelectorAll('.pin-input').forEach(input => {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                moveFocus(this, -1);
            } else {
                moveFocus(this, 1);
            }
        });
    });
});

function moveFocus(current, direction) {
    const inputs = document.querySelectorAll('.pin-input');
    const currentIndex = Array.from(inputs).indexOf(current);

    if (direction === 1) {
        if (current.value.length === 1 && currentIndex < inputs.length - 1) {
            inputs[currentIndex + 1].removeAttribute('disabled');

            inputs[currentIndex + 1].focus();
        }
    } else if (direction === -1 && inputs[(currentIndex - 1)]) {
        inputs[currentIndex].value = '';
        inputs[currentIndex].setAttribute('disabled', 'disabled');
        inputs[currentIndex - 1].focus();
    }
}

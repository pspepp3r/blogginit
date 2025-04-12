import { Alert, Modal } from "bootstrap";

const addLoader = (domElement) => {

    const loaderDiv = document.createElement('div');


    loaderDiv.classList.add('spinner-border');
    loaderDiv.classList.add('w-auto');
    loaderDiv.classList.add('h-auto');
    loaderDiv.classList.add('text-light');

    domElement.textContent = "Loading...";
    domElement.appendChild(loaderDiv);


    domElement.setAttribute('disabled', 'disabled')
    setInterval(() => {
        domElement.removeAttribute('disabled');
        domElement.textContent = "Save Changes";
    }, 3000);
}

/**
 *
 * @param {string} message
 * @param {string} type
 *
 * allowed in type: 'success', 'danger' and 'info'.
 */
const bootstrapAlert = (message, type = 'success') => {
    const alertText = document.querySelector('.alert-text');
    let alertBox = document.querySelector('.alert');

    if (type == 'fail') {
        alertBox.classList.remove('alert-primary')
        alertBox.classList.add('alert-danger')
    }

    alertText.textContent = message;

    alertBox.style.display = 'block';
    
    setTimeout(() => {
        alertBox.style.display = 'none';
    }, 2000);
}

export {
    addLoader,
    bootstrapAlert
}

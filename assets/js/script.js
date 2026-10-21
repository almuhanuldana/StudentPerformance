document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[onsubmit]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
            }
        });
    });
});

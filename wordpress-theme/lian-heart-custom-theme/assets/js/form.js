(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    onReady(function () {
        var form = document.querySelector('.js-contact-form');
        if (!form || !window.lhContact) {
            return;
        }

        var status = form.querySelector('.form-status');
        var submit = form.querySelector('.contact-form__submit');
        var sourceField = form.querySelector('input[name="source_url"]');
        var success = document.querySelector('.contact-form-success');

        if (sourceField) {
            sourceField.value = window.location.href;
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            var formData = new FormData(form);
            var payload = {};
            formData.forEach(function (value, key) {
                payload[key] = value;
            });
            payload.privacy = formData.get('privacy') ? 1 : 0;
            payload.source_url = window.location.href;

            submit.disabled = true;
            status.textContent = lhContact.messages.sending;
            status.classList.remove('is-error', 'is-success');

            try {
                var response = await fetch(lhContact.restUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': lhContact.nonce
                    },
                    body: JSON.stringify(payload)
                });

                var json = await response.json().catch(function () {
                    return {};
                });

                if (!response.ok) {
                    throw new Error(json.message || lhContact.messages.error);
                }

                form.hidden = true;
                if (success) {
                    success.hidden = false;
                }
                status.textContent = json.message || lhContact.messages.success;
                status.classList.add('is-success');
                form.reset();
            } catch (error) {
                status.textContent = error.message || lhContact.messages.error;
                status.classList.add('is-error');
            } finally {
                submit.disabled = false;
            }
        });
    });
}());

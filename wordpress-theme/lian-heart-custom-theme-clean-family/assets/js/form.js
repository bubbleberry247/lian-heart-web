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
        var submitWrap = submit ? submit.closest('.contact-form__submit-wrap') : null;

        if (sourceField) {
            sourceField.value = window.location.href;
        }

        function clearNotice() {
            status.textContent = '';
            status.classList.remove('is-error', 'is-success');
        }

        function setSubmitLabel(text) {
            if (submit) {
                submit.textContent = text;
            }
        }

        function buildPayload() {
            var formData = new FormData(form);
            var payload = {};
            formData.forEach(function (value, key) {
                payload[key] = value;
            });
            payload.privacy = formData.get('privacy') ? 1 : 0;
            payload.source_url = window.location.href;
            return payload;
        }

        function validate(payload) {
            if (!payload.name || !payload.email || !payload.phone || !payload.message || !payload.privacy) {
                return (lhContact.messages && lhContact.messages.required) || (lhContact.messages && lhContact.messages.error) || '必須項目を入力してください。';
            }

            if (!/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/.test(payload.email)) {
                return (lhContact.messages && lhContact.messages.required) || (lhContact.messages && lhContact.messages.error) || '必須項目を入力してください。';
            }

            return '';
        }

        function labelFor(name) {
            var field = form.querySelector('[name="' + name + '"]');
            if (!field) {
                return name;
            }
            var wrapper = field.closest('.contact-field');
            var label = wrapper ? wrapper.querySelector('.contact-form__field-title, .contact-form__label, span') : null;
            return label ? label.textContent.replace(/\s+/g, ' ').replace(/必須/g, '').trim() : name;
        }

        form.dataset.step = 'input';
        setSubmitLabel((lhContact.messages && lhContact.messages.confirm) || '内容を確認する');

        var confirmBlock = document.createElement('div');
        confirmBlock.className = 'contact-form__confirm';
        confirmBlock.setAttribute('aria-live', 'polite');

        var secondaryActions = document.createElement('div');
        secondaryActions.className = 'contact-form__actions-secondary';
        secondaryActions.innerHTML = '<button type="button" class="contact-form__back"></button>';

        if (submitWrap && submitWrap.parentNode) {
            submitWrap.parentNode.insertBefore(confirmBlock, submitWrap);
            submitWrap.parentNode.insertBefore(secondaryActions, submitWrap.nextSibling);
        }

        var backButton = secondaryActions.querySelector('.contact-form__back');
        if (backButton) {
            backButton.textContent = (lhContact.messages && lhContact.messages.back) || '入力に戻る';
            backButton.addEventListener('click', function () {
                form.dataset.step = 'input';
                confirmBlock.innerHTML = '';
                setSubmitLabel((lhContact.messages && lhContact.messages.confirm) || '内容を確認する');
                clearNotice();
            });
        }

        function renderConfirm(payload) {
            var order = ['name', 'email', 'phone', 'message'];
            confirmBlock.innerHTML = '';

            order.forEach(function (key) {
                if (!payload[key]) {
                    return;
                }

                var item = document.createElement('div');
                item.className = 'contact-form__confirm-item';
                item.innerHTML = '<div class="contact-form__confirm-label"></div><div class="contact-form__confirm-value"></div>';
                item.querySelector('.contact-form__confirm-label').textContent = labelFor(key);
                item.querySelector('.contact-form__confirm-value').textContent = payload[key];
                confirmBlock.appendChild(item);
            });

            form.dataset.step = 'confirm';
            setSubmitLabel('送信する');
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            var payload = buildPayload();
            var errorMessage = validate(payload);
            if (errorMessage) {
                status.textContent = errorMessage;
                status.classList.remove('is-success');
                status.classList.add('is-error');
                return;
            }

            if (form.dataset.step !== 'confirm') {
                clearNotice();
                renderConfirm(payload);
                return;
            }

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
                form.dataset.step = 'input';
                confirmBlock.innerHTML = '';
            } catch (error) {
                status.textContent = error.message || lhContact.messages.error;
                status.classList.add('is-error');
            } finally {
                submit.disabled = false;
                if (!form.hidden) {
                    setSubmitLabel(form.dataset.step === 'confirm' ? '送信する' : ((lhContact.messages && lhContact.messages.confirm) || '内容を確認する'));
                }
            }
        });
    });
}());

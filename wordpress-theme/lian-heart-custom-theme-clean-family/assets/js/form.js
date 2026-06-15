(function () {
    var BUSINESS_CATEGORY = 'business_proposal';
    var CATEGORY_LABELS = {
        admission_inquiry: 'ご本人・ご親族の入居相談',
        business_proposal: '入居相談以外の直接送信'
    };

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
        var policyVersionField = form.querySelector('input[name="policy_version"]');
        var success = document.querySelector('.contact-form-success');
        var submitWrap = submit ? submit.closest('.contact-form__submit-wrap') : null;
        var categoryFields = form.querySelectorAll('[name="category"]');
        var businessPanel = form.querySelector('.js-business-panel');
        var generalConsentField = form.querySelector('.js-consent-general');
        var generalConsentInput = form.querySelector('[name="consent_general"]');
        var businessConsentInputs = form.querySelectorAll('[name="consent_business"], [name="consent_business_authority"]');

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

        function selectedCategoryField() {
            return form.querySelector('[name="category"]:checked') || form.querySelector('input[type="hidden"][name="category"]') || form.querySelector('[name="category"]');
        }

        function selectedCategoryValue() {
            var selected = selectedCategoryField();
            return selected ? selected.value : '';
        }

        function isBusinessCategory() {
            return selectedCategoryValue() === BUSINESS_CATEGORY;
        }

        function updateConditionalFields() {
            var business = isBusinessCategory();

            if (businessPanel) {
                businessPanel.hidden = !business;
                businessPanel.setAttribute('aria-hidden', business ? 'false' : 'true');
            }

            if (generalConsentField) {
                generalConsentField.hidden = business;
                generalConsentField.setAttribute('aria-hidden', business ? 'true' : 'false');
            }

            if (generalConsentInput) {
                generalConsentInput.required = !business;
                generalConsentInput.disabled = business;
                if (business) {
                    generalConsentInput.checked = false;
                }
            }

            businessConsentInputs.forEach(function (input) {
                input.required = business;
                input.disabled = !business;
                if (!business) {
                    input.checked = false;
                }
            });
        }

        function buildPayload() {
            var formData = new FormData(form);
            var payload = {};
            formData.forEach(function (value, key) {
                payload[key] = value;
            });
            payload.category = selectedCategoryValue();
            payload.consent_privacy = formData.get('consent_privacy') ? '1' : '0';
            payload.consent_third_party = formData.get('consent_third_party') ? '1' : '0';
            payload.consent_general = formData.get('consent_general') ? '1' : '0';
            payload.consent_business = formData.get('consent_business') ? '1' : '0';
            payload.consent_business_authority = formData.get('consent_business_authority') ? '1' : '0';
            payload.privacy = formData.get('consent_privacy') ? 1 : 0;
            payload.policy_version = policyVersionField ? policyVersionField.value : (formData.get('policy_version') || '');
            payload.source_url = window.location.href;
            return payload;
        }

        function validate(payload) {
            if (!payload.category || !payload.name || !payload.email || !payload.message) {
                return (lhContact.messages && lhContact.messages.required) || (lhContact.messages && lhContact.messages.error) || '必須項目を入力してください。';
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) {
                return (lhContact.messages && lhContact.messages.required) || (lhContact.messages && lhContact.messages.error) || '必須項目を入力してください。';
            }

            if (payload.consent_privacy !== '1' || payload.consent_third_party !== '1') {
                return (lhContact.messages && lhContact.messages.consent) || '同意が必要な項目にチェックを入れてください。';
            }

            if (payload.category === BUSINESS_CATEGORY) {
                if (payload.consent_business !== '1' || payload.consent_business_authority !== '1') {
                    return 'このフォームでは営業・広告等を目的とした送信を受け付けていません。';
                }
            } else if (payload.consent_general !== '1') {
                return '入居相談としての送信確認に同意してください。';
            }

            return '';
        }

        function labelFor(name) {
            if (name === 'category') {
                return '用件';
            }

            var field = form.querySelector('[name="' + name + '"]');
            if (!field) {
                return name;
            }
            var wrapper = field.closest('.contact-field');
            var label = wrapper ? wrapper.querySelector('.contact-form__field-title, .contact-form__label, span') : null;
            return label ? label.textContent.replace(/\s+/g, ' ').replace(/必須/g, '').replace(/任意/g, '').replace(/\*/g, '').trim() : name;
        }

        function valueForConfirm(name, payload) {
            if (name === 'category') {
                var selected = selectedCategoryField();
                var option = selected ? selected.closest('.contact-category-option') : null;
                var title = option ? option.querySelector('.contact-category-option__title') : null;
                return title ? title.textContent : (CATEGORY_LABELS[payload[name]] || payload[name]);
            }

            return payload[name];
        }

        form.dataset.step = 'input';
        setSubmitLabel((lhContact.messages && lhContact.messages.confirm) || '内容を確認する');
        updateConditionalFields();

        categoryFields.forEach(function (field) {
            field.addEventListener('change', function () {
                updateConditionalFields();
                clearNotice();
            });
        });

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
                updateConditionalFields();
            });
        }

        function renderConfirm(payload) {
            var order = ['category', 'name', 'email', 'phone', 'message'];
            confirmBlock.innerHTML = '';

            order.forEach(function (key) {
                var value = valueForConfirm(key, payload);
                if (!value) {
                    return;
                }

                var item = document.createElement('div');
                item.className = 'contact-form__confirm-item';
                item.innerHTML = '<div class="contact-form__confirm-label"></div><div class="contact-form__confirm-value"></div>';
                item.querySelector('.contact-form__confirm-label').textContent = labelFor(key);
                item.querySelector('.contact-form__confirm-value').textContent = value;
                confirmBlock.appendChild(item);
            });

            form.dataset.step = 'confirm';
            setSubmitLabel('送信する');
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            updateConditionalFields();

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
                updateConditionalFields();
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

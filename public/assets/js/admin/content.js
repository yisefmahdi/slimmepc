(function () {
    'use strict';

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function showStatus(form, message, ok) {
        var status = form.querySelector('.form-status');
        if (!status) return;
        status.textContent = message;
        status.style.color = ok ? '#10b981' : '#ef4444';
        setTimeout(function () {
            status.textContent = '';
        }, 4000);
    }

    function updateSavedImages(form, saved) {
        Object.keys(saved).forEach(function (key) {
            var val = saved[key];
            if (typeof val !== 'string' || val.indexOf('assets/img/') !== 0) return;

            var hidden = form.querySelector('input[name="blocks[' + key + ']"]');
            if (!hidden) return;
            hidden.value = val;

            var block = hidden.closest('[data-image-block]');
            if (!block) return;

            var img = block.querySelector('[data-image-preview]');
            if (img) {
                img.src = '/' + val;
                img.style.display = '';
            }

            var name = block.querySelector('[data-image-name]');
            if (name) {
                name.textContent = val.split('/').pop();
            }

            var fileInput = block.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';
        });
    }

    function submitForm(form) {
        var button = form.querySelector('button[type="submit"]');
        var status = form.querySelector('.form-status');
        var url = form.getAttribute('data-url');
        var label = button.querySelector('.btn-label');
        var spinner = button.querySelector('.btn-spinner');
        var icon = button.querySelector('.btn-icon');

        var fileInputs = form.querySelectorAll('input[type="file"]');
        for (var i = 0; i < fileInputs.length; i++) {
            var file = fileInputs[i].files && fileInputs[i].files[0];
            if (file && file.size > 5 * 1024 * 1024) {
                showStatus(form, 'Afbeelding is te groot (maximaal 5 MB).', false);
                return;
            }
        }

        var data = new FormData(form);
        if (status) {
            status.textContent = 'Bezig met opslaan...';
            status.style.color = '#8b5cf6';
        }
        button.disabled = true;
        if (label) label.textContent = 'Opslaan...';
        if (spinner) spinner.style.display = '';
        if (icon) icon.style.display = 'none';

        axios.post(url, data, {
            headers: { 'X-CSRF-TOKEN': csrfToken() }
        }).then(function (response) {
            if (response.data && response.data.saved) {
                updateSavedImages(form, response.data.saved);
            }
            showStatus(form, 'Opgeslagen!', true);
        }).catch(function (error) {
            var msg = 'Opslaan mislukt.';
            var data = error.response && error.response.data;
            if (data && data.message) {
                msg = data.message;
            } else if (data && data.errors) {
                var firstKey = Object.keys(data.errors)[0];
                if (firstKey) msg = data.errors[firstKey][0];
            }
            showStatus(form, msg, false);
        }).finally(function () {
            button.disabled = false;
            if (label) label.textContent = 'Opslaan';
            if (spinner) spinner.style.display = 'none';
            if (icon) icon.style.display = '';
        });
    }

    function init() {
        var forms = document.querySelectorAll('.section-form, #design-form');

        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                submitForm(form);
            });

            form.addEventListener('click', function (e) {
                var addBtn = e.target.closest('[data-add-row]');
                if (addBtn) {
                    var block = addBtn.closest('[data-json-block]');
                    if (!block) return;
                    var rowsContainer = block.querySelector('.json-rows');
                    var template = block.querySelector('[data-row-template]');
                    if (!template) return;

                    var index = rowsContainer.querySelectorAll('.json-row').length;
                    var html = template.innerHTML.replace(/__INDEX__/g, index);

                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    var newRow = wrapper.firstChild;
                    var numSpan = newRow.querySelector('[data-row-number]');
                    if (numSpan) {
                        var numVal = index + 1;
                        numSpan.textContent = numVal < 10 ? '0' + numVal : numVal;
                    }
                    rowsContainer.appendChild(newRow);
                }

                var removeBtn = e.target.closest('[data-remove-row]');
                if (removeBtn) {
                    var row = removeBtn.closest('.json-row');
                    var rowsContainer = row.parentElement;
                    if (rowsContainer.querySelectorAll('.json-row').length > 1) {
                        row.remove();
                    }
                }
            });

            form.addEventListener('input', function (e) {
                var target = e.target;
                if (target.type === 'color') {
                    var hex = target.parentElement.querySelector('input[type="text"]');
                    if (hex) hex.value = target.value;
                }
                if (target.type === 'text' && target.name.endsWith('_hex')) {
                    var color = target.parentElement.querySelector('input[type="color"]');
                    if (color && /^#[0-9a-fA-F]{6}$/.test(target.value)) {
                        color.value = target.value;
                    }
                }
            });

            form.addEventListener('change', function (e) {
                var fileInput = e.target;
                if (fileInput.type !== 'file' || !fileInput.files || !fileInput.files[0]) return;

                var preview = fileInput.closest('div').parentElement.querySelector('[data-image-preview]');
                if (!preview) return;

                var reader = new FileReader();
                reader.onload = function () {
                    preview.src = reader.result;
                    preview.style.display = '';
                };
                reader.readAsDataURL(fileInput.files[0]);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();


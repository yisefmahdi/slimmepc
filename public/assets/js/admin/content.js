(function () {
    'use strict';

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function toastMessage(message, ok) {
        if (!window.SlimmePC || !window.SlimmePC.toast) return;
        if (ok) {
            window.SlimmePC.toast.success(message);
        } else {
            window.SlimmePC.toast.error(message);
        }
    }

    function updateSavedImages(form, saved) {
        Object.keys(saved).forEach(function (key) {
            var val = saved[key];

            if (typeof val === 'string' && val.indexOf('assets/img/') === 0) {
                var hidden = form.querySelector('input[name="blocks[' + key + ']"]');
                if (!hidden) return;
                hidden.value = val;

                var block = hidden.closest('[data-image-block]');
                if (!block) return;

                var img = block.querySelector('[data-image-preview]');
                if (img) {
                    img.src = val.indexOf('/') !== -1 ? '/' + val : '/assets/img/landing/' + val;
                    img.style.display = '';
                }

                var name = block.querySelector('[data-image-name]');
                if (name) {
                    name.textContent = val.split('/').pop();
                }

                var fileInput = block.querySelector('input[type="file"]');
                if (fileInput) fileInput.value = '';
            }

            if (Array.isArray(val)) {
                var jsonBlock = form.querySelector('[data-json-block][data-block-key="' + key + '"]');
                if (!jsonBlock) return;
                var rows = jsonBlock.querySelectorAll('.json-row');
                val.forEach(function (item, i) {
                    var row = rows[i];
                    if (!row || !item || typeof item !== 'object') return;
                    Object.keys(item).forEach(function (fieldKey) {
                        var fv = item[fieldKey];
                        if (typeof fv !== 'string') return;

                        var hidden = row.querySelector('input[name="blocks[' + key + '][' + i + '][' + fieldKey + ']"]');
                        if (!hidden) return;

                        var imageBlock = hidden.closest('[data-image-block]');
                        if (!imageBlock) return;

                        hidden.value = fv;

                        var img = imageBlock.querySelector('[data-image-preview]');
                        if (img) {
                            img.src = fv.indexOf('/') !== -1 ? '/' + fv : '/assets/img/landing/' + fv;
                            img.style.display = '';
                        }

                        var name = imageBlock.querySelector('[data-image-name]');
                        if (name) {
                            name.textContent = fv.split('/').pop();
                        }

                        var fileInput = imageBlock.querySelector('input[type="file"]');
                        if (fileInput) fileInput.value = '';
                    });
                });
            }
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
            // Video uploads are handled progressively and may exceed the image limit.
            if (file && file.size > 5 * 1024 * 1024 && !fileInputs[i].classList.contains('js-media-input')) {
                toastMessage('Afbeelding is te groot (maximaal 5 MB).', false);
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
            if (status) status.textContent = '';
            toastMessage('Opgeslagen!', true);
        }).catch(function (error) {
            var msg = 'Opslaan mislukt.';
            var data = error.response && error.response.data;
            if (data && data.message) {
                msg = data.message;
            } else if (data && data.errors) {
                var firstKey = Object.keys(data.errors)[0];
                if (firstKey) msg = data.errors[firstKey][0];
            }
            if (status) status.textContent = '';
            toastMessage(msg, false);
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
                var nestedAddBtn = e.target.closest('[data-add-nested-row]');
                if (nestedAddBtn) {
                    var nestedBlock = nestedAddBtn.closest('[data-nested-block]');
                    if (!nestedBlock) return;
                    var nestedRows = nestedBlock.querySelector('.json-nested-rows');
                    var nestedTemplate = nestedBlock.querySelector('[data-nested-row-template]');
                    if (!nestedRows || !nestedTemplate) return;

                    var nIndex = nestedRows.querySelectorAll('.json-nested-row').length;
                    var nHtml = nestedTemplate.innerHTML.replace(/__NINDEX__/g, nIndex);

                    var nWrapper = document.createElement('div');
                    nWrapper.innerHTML = nHtml.trim();
                    var newNestedRow = nWrapper.firstChild;
                    var nNumSpan = newNestedRow.querySelector('[data-nested-row-number]');
                    if (nNumSpan) {
                        var nNumVal = nIndex + 1;
                        nNumSpan.textContent = nNumVal < 10 ? '0' + nNumVal : nNumVal;
                    }
                    nestedRows.appendChild(newNestedRow);
                    return;
                }

                var nestedRemoveBtn = e.target.closest('[data-remove-nested-row]');
                if (nestedRemoveBtn) {
                    var nestedRow = nestedRemoveBtn.closest('.json-nested-row');
                    var nestedContainer = nestedRow ? nestedRow.parentElement : null;
                    if (nestedContainer && nestedContainer.querySelectorAll('.json-nested-row').length > 1) {
                        nestedRow.remove();
                    }
                    return;
                }

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

        form.addEventListener('change', function (e) {
            var input = e.target;
            if (!input.classList || !input.classList.contains('js-media-input')) return;
            if (input.getAttribute('data-media-type') !== 'video') return;
            var file = input.files && input.files[0];
            if (!file) return;

            var block = input.closest('[data-video-block]');
            if (!block) return;

            var progress = block.querySelector('.js-media-progress');
            var bar = block.querySelector('.js-media-bar');
            var pct = block.querySelector('.js-media-pct');
            var hidden = block.querySelector('[data-video-value]');

            if (progress) progress.classList.remove('hidden');
            if (bar) bar.style.width = '0%';
            if (pct) pct.textContent = '0%';

            var fd = new FormData();
            fd.append('file', file);

            axios.post('/admin/content/media', fd, {
                headers: { 'X-CSRF-TOKEN': csrfToken() },
                onUploadProgress: function (ev) {
                    if (!ev.total) return;
                    var percent = Math.round((ev.loaded / ev.total) * 100);
                    if (bar) bar.style.width = percent + '%';
                    if (pct) pct.textContent = percent + '%';
                }
            }).then(function (response) {
                var path = response.data && response.data.path;
                if (!path) return;
                if (hidden) hidden.value = path;
                var preview = block.querySelector('[data-video-preview]');
                if (preview) {
                    preview.src = '/' + path;
                    preview.style.display = '';
                }
                var name = block.querySelector('[data-video-name]');
                if (name) name.textContent = path.split('/').pop();
                input.value = '';
                if (progress) progress.classList.add('hidden');
            }).catch(function (error) {
                if (progress) progress.classList.add('hidden');
                var msg = 'Video upload mislukt.';
                var d = error.response && error.response.data;
                if (d && d.message) msg = d.message;
                toastMessage(msg, false);
            });
        });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();


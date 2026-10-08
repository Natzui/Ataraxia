/* ==========================================================
   Mini Social - client-side behaviour (vanilla JS)
   - Bootstrap form validation + password match
   - Image validation + preview
   - Character counters
   - Confirm dialogs for delete actions
   - Inline comment editing
   - AJAX like button (falls back to a normal form post)
   ========================================================== */
(function () {
    'use strict';

    var MAX_BYTES = window.APP_MAX_UPLOAD || 2 * 1024 * 1024;
    var IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    /* ---------- Submit handling (validation, confirm, ajax like) ---------- */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement)) { return; }

        if (form.classList.contains('needs-validation')) {
            form.classList.add('was-validated');
            if (!form.checkValidity()) {
                e.preventDefault();
                var firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) { firstInvalid.focus(); }
                return;
            }
        }

        var message = form.getAttribute('data-confirm');
        if (message && !window.confirm(message)) {
            e.preventDefault();
            return;
        }

        if (form.classList.contains('like-form')) {
            e.preventDefault();
            toggleLike(form);
        }
    });

    /* ---------- Password confirmation must match ---------- */
    function checkMatch(input) {
        var target = document.querySelector(input.getAttribute('data-match'));
        if (!target) { return; }
        input.setCustomValidity(input.value === target.value ? '' : 'Passwords do not match.');
    }
    document.querySelectorAll('[data-match]').forEach(function (input) {
        var target = document.querySelector(input.getAttribute('data-match'));
        input.addEventListener('input', function () { checkMatch(input); });
        if (target) { target.addEventListener('input', function () { checkMatch(input); }); }
    });

    /* ---------- Image input: validate type + size, show preview ---------- */
    document.querySelectorAll('[data-image-input]').forEach(function (input) {
        var preview = document.querySelector(input.getAttribute('data-preview'));
        var originalSrc = preview ? preview.getAttribute('src') : null;

        function resetPreview() {
            if (!preview) { return; }
            if (originalSrc) { preview.src = originalSrc; } else { preview.classList.add('d-none'); }
        }

        input.addEventListener('change', function () {
            input.classList.remove('is-invalid');
            var file = input.files && input.files[0];

            if (!file) {
                resetPreview();
                return;
            }
            if (IMAGE_TYPES.indexOf(file.type) === -1) {
                input.value = '';
                resetPreview();
                alert('Only JPG, PNG, GIF or WEBP images are allowed.');
                return;
            }
            if (file.size > MAX_BYTES) {
                input.value = '';
                resetPreview();
                alert('That image is too large. The maximum size is ' + (MAX_BYTES / 1048576) + ' MB.');
                return;
            }
            if (preview) {
                var reader = new FileReader();
                reader.onload = function (ev) {
                    preview.src = ev.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    });

    /* ---------- Live character counters ---------- */
    document.querySelectorAll('[data-counter]').forEach(function (field) {
        var counter = document.querySelector(field.getAttribute('data-counter'));
        if (!counter) { return; }
        var max = field.getAttribute('maxlength') || '';
        function update() { counter.textContent = field.value.length + (max ? ' / ' + max : ''); }
        field.addEventListener('input', update);
        update();
    });

    /* ---------- Inline comment editing ---------- */
    document.addEventListener('click', function (e) {
        var editBtn = e.target.closest('.js-edit-comment');
        var cancelBtn = e.target.closest('.js-cancel-edit');
        if (!editBtn && !cancelBtn) { return; }

        var wrapper = (editBtn || cancelBtn).closest('[id^="comment-"]');
        if (!wrapper) { return; }
        var text = wrapper.querySelector('.comment-text');
        var form = wrapper.querySelector('.comment-edit-form');
        if (!text || !form) { return; }

        if (editBtn) {
            text.classList.add('d-none');
            form.classList.remove('d-none');
            var ta = form.querySelector('textarea');
            if (ta) { ta.focus(); ta.setSelectionRange(ta.value.length, ta.value.length); }
        } else {
            form.classList.add('d-none');
            text.classList.remove('d-none');
        }
    });

    /* ---------- AJAX like / unlike ---------- */
    function toggleLike(form) {
        var button = form.querySelector('button');
        if (button.disabled) { return; }
        button.disabled = true;

        fetch(form.getAttribute('action'), {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (res) {
            if (!res.ok) { throw new Error('Request failed'); }
            return res.json();
        })
        .then(function (data) {
            var icon = button.querySelector('i');
            button.querySelector('.like-count').textContent = data.count;
            button.querySelector('.like-label').textContent = data.count === 1 ? 'Like' : 'Likes';
            button.classList.toggle('text-danger', data.liked);
            button.classList.toggle('text-muted', !data.liked);
            button.setAttribute('aria-pressed', data.liked ? 'true' : 'false');
            icon.classList.toggle('bi-heart-fill', data.liked);
            icon.classList.toggle('bi-heart', !data.liked);
            button.disabled = false;
        })
        .catch(function () {
            form.submit();      // fall back to a normal page reload
        });
    }
})();

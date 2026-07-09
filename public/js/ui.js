// Blocks double requests. Submitted forms get a spinner on the submit button
// and further submits are prevented; data-loader navigation links show a
// full-page loader overlay instead. pageshow resets everything, which also
// covers back/forward cache restores.
(function () {
    var navigating = false;

    function showPageLoader() {
        if (!document.getElementById('page-loader')) {
            var el = document.createElement('div');
            el.id = 'page-loader';
            el.className = 'page-loader';
            document.body.appendChild(el);
        }
    }

    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) {
            return;
        }

        var form = e.target;

        if (form.dataset.submitting) {
            e.preventDefault();
            return;
        }

        form.dataset.submitting = '1';

        form.querySelectorAll('button[type="submit"]').forEach(function (btn) {
            btn.classList.add('is-loading');
            setTimeout(function () {
                btn.disabled = true;
            }, 0);
        });
    });

    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[data-loader]');

        if (!link) {
            return;
        }

        // Opening in a new tab must not lock the current page.
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0 || link.target === '_blank') {
            return;
        }

        if (navigating) {
            e.preventDefault();
            return;
        }

        navigating = true;

        // Small delay so the overlay does not flash on instant navigations.
        setTimeout(showPageLoader, 120);
    });

    window.addEventListener('pageshow', function () {
        navigating = false;

        var overlay = document.getElementById('page-loader');
        if (overlay) {
            overlay.remove();
        }

        document.querySelectorAll('.is-loading').forEach(function (el) {
            el.classList.remove('is-loading');
            el.disabled = false;
        });

        document.querySelectorAll('form[data-submitting]').forEach(function (form) {
            delete form.dataset.submitting;
        });
    });
})();

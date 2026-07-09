// Blocks double requests: once a form is submitted or a data-loader link is
// clicked, the trigger gets a spinner and further clicks are ignored until
// the next page load (pageshow also covers back/forward cache restores).
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

    if (link.dataset.loading) {
        e.preventDefault();
        return;
    }

    link.dataset.loading = '1';
    link.classList.add('is-loading');
});

window.addEventListener('pageshow', function () {
    document.querySelectorAll('.is-loading').forEach(function (el) {
        el.classList.remove('is-loading');
        el.disabled = false;
        delete el.dataset.loading;
    });

    document.querySelectorAll('form[data-submitting]').forEach(function (form) {
        delete form.dataset.submitting;
    });
});

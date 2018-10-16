require('../less/main.less');

// Tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

// Shoot page redirection
$(document).ready(function() {
    jQuery('#linkToShootPage').click(function() {
        setTimeout(function() {
            document.location.href = '/dashboard';
        }, 1000);
    });
});
require('../less/main.less');

//----------------------------------------------------------------
// DASHBOARD SIDEBAR
//----------------------------------------------------------------

jQuery(document).ready(function() {
    function showSidebar() {
        jQuery("#dashboard-sidebar").show();
        jQuery('body').addClass('sidebarDisplayed');
    }

    function hideSidebar() {
        jQuery("#dashboard-sidebar").hide();
        jQuery('body').removeClass('sidebarDisplayed');
    }

    if (jQuery(document).width() < 650) {
        hideSidebar();
    } else {
        showSidebar();
    }

    jQuery("#menu-toggle").click(function(e) {
        if (jQuery('#dashboard-sidebar').is(':visible')) {
            hideSidebar();
        } else {
            showSidebar();
        }

        return false;
    });
});




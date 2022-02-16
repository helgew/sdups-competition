'use strict';

(function ($) {
    $(document).ready(function () {
        new $.SDUPSAdminCommon().submissionsTable($('table[id="submissions"]'));
    });
})(jQuery);

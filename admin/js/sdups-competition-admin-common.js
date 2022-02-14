'use strict';

(function ($) {
        $.SDUPSAdminCommon = function () {
        }
        $.SDUPSAdminCommon.prototype = {
            init: function (onAjaxSuccess) {
                // WP makes notices jump around :(
                $('.notice').show();

                $('form.ajax').on('submit', function (e) {
                    e.preventDefault();
                    var data = $(this).serializeArray().reduce(
                        function (o, kv) {
                            o[kv.name] = kv.value;
                            return o;
                        }, {});
                    var that = $(this);
                    $.ajax({
                        url: cpm_object.ajax_url,
                        type: "POST",
                        dataType: 'json',
                        data: {
                            action: 'process_ajax',
                            data: JSON.stringify(data)
                        }, success: function (response) {
                            that.find('.error-message').hide();

                            if ('status' in response && response.status === 302) {
                                window.location.replace(response.url);
                            }

                            if (onAjaxSuccess && typeof onAjaxSuccess === 'function') {
                                onAjaxSuccess(that, response);
                            }
                        }, error: function (response) {
                            this.handleAjaxError(that, response.responseJSON.data);
                        }
                    });
                });
            },

            handleAjaxError: function (form, data) {
                var div = form.find(".error-message");
                if (div) {
                    div.html($('<div/>', {
                        class: "notice notice-error",
                        style: "display: block;"
                    }).append($('<p/>').text(data.message)));
                    div.show();
                }
            },

            submissionsTable: function (element) {
                element.DataTable({
                    "ajax": {
                        "url": cpm_object.ajax_url,
                        "data": function (data) {
                            data['action'] = "process_ajax";
                            data['data'] = '{"action":"get_submissions"}';
                        },
                        "type": "POST",
                        "error": function (response) {
                            this.handleAjaxError(fieldsForm, response.responseJSON.data);
                        }
                    },
                    "columns": [
                        {"data": "name"},
                        {"data": "email"},
                        {"data": "date"},
                        {"data": "division"},
                        {"data": "category"},
                        {
                            "data": "upload",
                            "className": 'dt-body-center',
                            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).html(getLinkForUpload(oData.upload));
                            }
                        },
                    ],
                    "order": [[2, "desc"]],
                    "processing": true,
                    "language": {
                        processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> '
                    }
                });
            }
        }

        function getLinkForUpload(url) {
            return '<a href="' + url + '">' +
                (url !== '' && url.endsWith('4') ? 'video' : 'image') + '</a>'
        }
    }
)(jQuery);

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

            submissionsTable: function (element, form, processDataFunction, getDataSourceFunction, options) {
                if (!form) {
                    form = element.closest('form');
                }

                if (!form) {
                    form = element;
                }

                var config = {
                    "ajax": {
                        "url": cpm_object.ajax_url,
                        "data": function (data) {
                            data['action'] = "process_ajax";
                            data['data'] = '{"action":"get_submissions"}';

                            if (processDataFunction && typeof processDataFunction === 'function') {
                                processDataFunction(data);
                            }
                        },
                        "dataSrc": (getDataSourceFunction && typeof getDataSourceFunction === 'function') ?
                            getDataSourceFunction : "data",
                        "type": "POST",
                        "error": function (response) {
                            this.handleAjaxError(form, response.responseJSON.data);
                        }
                    },
                    "rowId": "entry_id",
                    "columns": this.submissionsTableDefaultColumns,
                    "order": [[2, "desc"]],
                    "processing": true,
                    "language": {
                        processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> '
                    },
                };

                if (options) {
                    config = {...config, ...options};
                }

                var dt = element.DataTable(config);
                dt.on('init.dt', initComplete);

                return dt;
            },

            submissionsTableDefaultColumns: [
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
            ]
        }

        function getLinkForUpload(url) {
            return '<a href="' + url + '" target="_blank">' +
                (url !== '' && url.endsWith('4') ? 'video' : 'image') + '</a>'
        }


        function initComplete(e, settings, json) {
            var api = new $.fn.dataTable.Api(settings);
            var table = api.table();

            api.columns().every(function () {
                var column = this;
                var tr = $(column.header()).parent();
                $('input[type=text]', tr.prev().children().eq(column.index()))
                    .on('keyup change clear', function () {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
            });

            $('input[type=checkbox', table.header()).on('change', function () {
                var isChecked = $(this).is(':checked');
                $.fn.dataTable.ext.search.pop();
                $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {
                        var row = api.row(dataIndex).node();
                        if ($(row).find('input').prop('checked')) {
                            return true;
                        }
                        return !isChecked;
                    }
                );

                api.draw();
            });

            var n = 0;
            $('tr', table.header()).first().find('th').each(function () {
                var select = $(this).find('select');
                if (select.length === 1) {
                    api.columns(n).every(function () {
                        var column = this;
                        $('<option value="">-- Any --</option>').appendTo(select);
                        select.on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );

                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });

                        column.data().unique().sort().each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                    });
                }
                n++;
            });

            api.columns.adjust();
        }
    }
)(jQuery);

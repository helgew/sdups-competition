'use strict';

var submissionsTable = null;

(function ($) {
        var categoriesForm = null;

        $(document).ready(function () {
            var admin = new $.SDUPSAdminCommon();
            var subs = $('#submissions');

            var searchHeader = $('<tr/>');

            var cols = admin.submissionsTableDefaultColumns;
            cols.splice(4, 1);
            cols.unshift(
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return '<input type="checkbox" name="entry_id" ' +
                            'value="' + row.entry_id + '" ' +
                            'checked/>';
                    },
                    className: 'dt-body-center',
                    orderable: false
                }
            );

            var options = {
                "columns": cols,
                "order": [[3, "desc"]],
            };

            categoriesForm = $('#categories-form');
            categoriesForm.on('submit', function (e) {
                e.preventDefault();
                $('#submissions-table-container').show();
                if (submissionsTable === null) {
                    submissionsTable =
                        admin.getSubmissionsTable(subs, categoriesForm,
                            processCategoriesFormData, null, options);
                    submissionsTable.on('draw', function () {
                        subs.find('input[type=checkbox]').change(function () {
                            if (this.checked) {
                                $(this.closest('tr')).removeClass('strikethrough');
                            } else {
                                $(this.closest('tr')).addClass('strikethrough');
                            }
                        });
                    });
                } else {
                    submissionsTable.ajax.reload();
                }
            });
        });

        function processCategoriesFormData(data) {
            var formData = categoriesForm.serializeArray().reduce(
                function (o, kv) {
                    o[kv.name] = kv.value;
                    return o;
                }, {});
            data['data'] = JSON.stringify(formData);
        }
    }
)
(jQuery);

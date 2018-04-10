<script type="text/javascript">
    $(document).ready(function () {
        var templates = {
                    actions: '<i class="fa fa-pencil text-success edit"></i><i class="fa fa-remove text-danger remove"></i><i class="fa fa-reorder reorder"></i>',
                    text: '<label for="title" class="col-md-4 control-label"><span class="field-label">:LABEL</span></label>' +
                    '<div class="col-md-8">' +
                    '<div class="input-group"><div class="form-control"></div><span class="input-group-addon">:ACTIONS</span></div>' +
                    '</div>',
                    textarea: '<label for="title" class="col-md-4 control-label"><span class="field-label">:LABEL</span></label>' +
                    '<div class="col-md-8">' +
                    '<div class="input-group"><div class="form-control" style="height: 120px"></div><span class="input-group-addon">:ACTIONS</span></div>' +
                    '</div>',
                    checkbox: '<div class="col-md-offset-4 col-md-8">' +
                    '<div class="input-group"><div class="checkbox"><label><input type="checkbox" disabled> <span class="field-label">:LABEL</span></label></div><span class="input-group-addon">:ACTIONS</span></div>' +
                    '</div>'
                },
                titles = {
                    text: 'Text Field name?',
                    textarea: 'Textarea Field name?',
                    checkbox: 'Checkbox Field name?'
                };

        function restoreFields(input_form, preview) {
            var fields = $(input_form).val();
            if (fields) {
                fields = $.parseJSON(fields);
                $.each(fields, function (i, field) {
                    addPreviewField(field.type, field.label, preview);
                });
            }
        }

        function updateInput(input_form, preview) {
            // Serialize fields
            var fields = $('.field-group', $(preview)).map(function () {
                return {
                    type: $(this).data('type'),
                    label: $('.field-label', this).html()
                };
            }).get();
            $(input_form).val($.toJSON(fields));
        }

        function addPreviewField(type, label, preview) {
            var html = '<div class="form-group field-group" data-type="' + type + '">' + templates[type] + '</div>';
            html = html.replace(':LABEL', label).replace(':ACTIONS', templates['actions']);
            $(preview).append(html);
        }

        function validateLabel(label) {
            if (label) {
                return label.replace(/[\:\|]/g, '');
            } else {
                return false;
            }
        }

        $('.form-editor').each(function () {
            var preview = $('.form-editor-preview', this),
                    input_form = $(this).data('input');
            $('.actions a', this).click(function (e) {
                e.preventDefault();
                var type = $(this).data('type');
                bootbox.prompt({
                    title: titles[type],
                    callback: function (result) {
                        result = validateLabel(result);
                        if (result) {
                            addPreviewField(type, result, preview);
                            updateInput(input_form, preview);
                        }
                    }
                });
            });
            preview.on('click', '.input-group-addon i.remove', function () {
                var field = $(this).parents('div.field-group');
                field.remove();
                updateInput(input_form, preview);
            });

            preview.on('click', '.input-group-addon i.edit', function () {
                var field = $(this).parents('div.field-group'),
                        type = field.data('type'),
                        label = $('.field-label', field);
                bootbox.prompt({
                    title: titles[type],
                    value: label.html(),
                    callback: function (result) {
                        result = validateLabel(result);
                        if (result) {
                            label.html(result);
                            updateInput(input_form, preview);
                        }
                    }
                });
            });
        });
        @foreach($input_forms as $input => $preview)
        restoreFields('#{!! $input !!}', '#{!! $preview !!}');
        Sortable.create(
                document.getElementById('{!! $preview !!}'),
                {
                    handle: ".reorder",
                    ghostClass: "placeholder",
                    onUpdate: function (evt) {
                        updateInput('#{!! $input !!}', '#{!! $preview !!}');
                    }
                }
        );
        @endforeach
    });
</script>
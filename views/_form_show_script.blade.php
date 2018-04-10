@if($form)
    <script type="text/javascript">
        $(document).ready(function () {
            var templates = {
                    text: '<label for="title" class="col-md-4 control-label"><span class="field-label">:LABEL</span></label>' +
                    '<div class="col-md-8">' +
                    '<div class="form-control"></div>' +
                    '</div>',
                    textarea: '<label for="title" class="col-md-4 control-label"><span class="field-label">:LABEL</span></label>' +
                    '<div class="col-md-8">' +
                    '<div class="form-control" style="height: 120px"></div>' +
                    '</div>',
                    checkbox: '<div class="col-md-offset-4 col-md-8">' +
                    '<div class="checkbox"><label><input type="checkbox" disabled> <span class="field-label">:LABEL</span></label></div>' +
                    '</div>'
                };
            function restoreFields(selector, form) {
                if(form) {
                    var preview = $(selector),
                            fiels = $.parseJSON(form);
                    $.each(fiels, function (i, field) {
                        var html = '<div class="form-group field-group">' + templates[field.type] + '</div>';
                        html = html.replace(':LABEL', field.label);
                        preview.append(html);
                    });
                }
            }
            @if(is_string($form))
                restoreFields('#form-editor-preview', '{!! $form !!}');
            @else
                @foreach($form as $id => $f)
                    restoreFields('{!! $id !!}', '{!! $f !!}');
                @endforeach
            @endif
        });
    </script>
@endif
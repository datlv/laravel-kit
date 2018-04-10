<div class="display-options">
    <div class="form form-inline">
        <div class="buttons">
            {!! $options->link('type', 'th', 'th', trans('common.display_th')) !!}
            {!! $options->link('type', 'list', 'list', trans('common.display_list')) !!}
        </div>
        <div class="pull-right">
            <div class="form-group">
                {!! $options->select('sort', trans('common.sort'), trans('common.sort_hint')) !!}
            </div>
            <div class="form-group">
                {!! $options->select('page_size', trans('common.page_size'), $page_hint) !!}
            </div>
        </div>
    </div>
</div>
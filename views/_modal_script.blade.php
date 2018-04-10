<script type="text/javascript">
    var modal = window.parent.$.fn.mbHelpers.getParentModal(window.parent, window);
    if(modal){
        modal.modal('hide');
    }
    @if (isset($message))
        window.parent.$.fn.mbHelpers.showMessage('{{ $message["type"] }}', '{!! $message["content"] !!}');
    @endif
    @if (!empty($reloadPage))
        if (window.parent.$.fn.mbHelpers.reloadPage) {
            window.parent.$.fn.mbHelpers.reloadPage();
        }
    @endif
    @if (!empty($reloadTable))
        window.parent.LaravelDataTables['{{ $reloadTable }}'].ajax.reload();
    @endif
</script>
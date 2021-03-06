@extends('admin.app')

@section('page_title', $data['page_title'])
@section('page_class', $data['page_class'])

@section('content')
  @include('admin.components.flash-message')
  <div class="panel panel-default">
    <div class="table-responsive">
      @include('admin.components.datatable-loading')
      <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-xs">{{__('admin.add')}}</a>
      <table class="panel-body table table-hover table-bordered table-striped" style="width:100%" id="datatable-roles">
        <thead class="hidden">
          <tr>
            <th>Roles</th>
            <th></th>
            <th></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="table-responsive">
      @include('admin.components.datatable-loading')
      <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-xs">{{__('admin.add')}}</a>
      <table class="panel-body table table-hover table-bordered table-striped" style="width:100%" id="datatable-permissions">
        <thead class="hidden">
          <tr>
            <th>Permissions</th>
            <th></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
@endsection

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.3/js/dataTables.rowReorder.min.js"></script>
<script type="text/javascript">

$(document).ready(function() {
    var table_roles = $('#datatable-roles').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      rowReorder: false,
      colReorder: false,
      paging:   false,
      dom       : '<"panel-heading"f> <"panel-body"t> <"panel-footer"<li>p>',
      initComplete: function(settings, json) {
          $('#datatable-roles_wrapper').siblings('.datatable-loading').hide();
        },
      ajax: '{{ route('admin.roles.getdata') }}',
      language: {
        "search": '',
        searchPlaceholder: "Roles",
        "paginate": {
          "previous": '&larr;',
          "next": '&rarr;'
        },
        lengthMenu: "{{__('admin.datatable_lengthMenu')}}",
        zeroRecords: "{{__('admin.datatable_zeroRecords')}}",
        info: "{{__('admin.datatable_info')}}",
      },
      columns: [
        {data: 'name', name: 'name', orderable: false, class: 'main-column'},
        {data: 'updated_at', name: 'title', searchable: false, orderable: false, class: 'hidden-small'},
        {data: 'action', name: 'action', orderable: false, searchable: false, class:'faded'}
      ]
    });
  var table_permissions = $('#datatable-permissions').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      rowReorder: false,
      colReorder: false,
      paging:   false,
      dom       : '<"panel-heading"f> <"panel-body"t> <"panel-footer"<li>p>',
      initComplete: function(settings, json) {
          $('#datatable-permissions_wrapper').siblings('.datatable-loading').hide();
        },
      ajax: '{{ route('admin.permissions.getdata') }}',
      language: {
        "search": '',
        searchPlaceholder: "Permissions",
        "paginate": {
          "previous": '&larr;',
          "next": '&rarr;'
        },
      },
      columns: [
        {data: 'name', name: 'name', orderable: false, class: 'main-column'},
        {data: 'action', name: 'action', orderable: false, searchable: false, class:'faded'}
      ]
    });
    $.fn.dataTable.ext.errMode = 'throw';

});
</script>
@endsection

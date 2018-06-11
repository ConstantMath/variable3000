<?php
  if(!empty($parent_article)):
    $parent_id = $parent_article->id;
    $page_title = $parent_article->title;
    $bt_add_label = __('admin.add_subpage');
  else:
    $parent_id = 0;
    $page_title = $data['page_title'];
    $bt_add_label = __('admin.add');
  endif;
?>

@extends('admin.app')

@section('page_title', $data['page_title'])
@section('page_class', $data['page_class'])

@section('content')
  <div class="panel panel-default">
    <div class="table-responsive">
      <a href="{{ route('admin.pages.create', $parent_id) }}" class="btn btn-primary btn-xs"> Add</a>

      <table class="panel-body table table-hover table-bordered table-striped table-reorderable" id="datatable" style="width:100%">
          <thead>
            <tr>
              <th class="is-published"></th>
              <th class="main-column">Title</th>
              <th></th>
            </tr>
          </thead>
      </table>
    </div>
  </div>

 {{-- <div class="panel panel-default">
   <div class="panel-heading">
     <h3>{{ $page_title }}</h3>
     <a href="{{ route('admin.pages.create', $parent_id) }}" class="pull-right"><i class="fa fa-plus-circle"></i> {{ $bt_add_label }}</a>
   </div>
   <div class="panel-body table-responsive">
     <table class="table">
       <tbody id="sortable" class="sortable">
         @if($articles) @foreach ($articles as $node)
           @include('admin.components.table-row-pages')
         @endforeach @endif
       </tbody>
     </table>
   </div>
 </div> --}}
@endsection

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.3/js/dataTables.rowReorder.min.js"></script>
<script type="text/javascript">
function format ( parent ) {
  // `d` is the original data object for the row
  var request = '{{ route('admin.' .$data['table_type']. '.getdata', ':id') }}';
  request = request.replace(':id', parent.id);
  var h;
  $.ajaxSetup({async:false});
  $.get(request, function(data) {
    var data = data.data;
    if (data.length > 0) {
      h = '<table>';
      for(var i = 0; i < data.length; i++) {
        h += '<tr>';
        if(data[i].published == 1){
          h += '<td class="is-published"><i class="fa fa-circle icon-published"></i></td>';
        }else {
          h += '<td class="is-published"><i class="fa fa-circle-o icon-published"></i></td>';
        }
        h += '<td><div class="text-content">'+ data[i].title +'</div></td>';
        var edit = '{{ route('admin.pages.edit', ':id') }}';
        edit = edit.replace(':id', data[i].id);
        h += '<td><a href="'+ edit +'" class="link">Edit</a></td>';
        h += '</td>';
      }
      h += '</table>';
    }
  });
  $.ajaxSetup({async:true});
  return h;
}

$(document).ready(function() {
    var table = $('#datatable').DataTable({
      responsive: true,
      autoWidth: false,
      processing: true,
      serverSide: true,
      rowReorder: true,
      colReorder: false,
      dom       : '<"panel-heading"f> <"panel-body"t> <"panel-footer"<li>p>',
      ajax: '{{ route('admin.' .$data['table_type']. '.getdata', $parent_id) }}',
      language: {
        "search": '',
        searchPlaceholder: "Search",
        "paginate": {
          "previous": '&larr;',
          "next": '&rarr;'
        },
      },
      columns: [
        {data: 'order', render: function ( data, type, row, meta ) {
          if(row.published == 1){
            return '<i class="fa fa-circle icon-published"></i>';
          }else {
            return '<i class="fa fa-circle-o icon-published"></i>';
          }
        }, name: 'order', searchable: false, class: 'is-published'},
        {data: 'title', render: function ( data, type, row, meta ) {
          return '<div class="text-content">'+ data + '</div>';
        }, name: 'title', orderable: false, class: 'main-column'},
        {data: 'action', name: 'action', orderable: false, searchable: false, class:'faded'}
      ]
    });

    table.on( 'row-reorder', function ( e, diff, edit ) {
      var articlesArray = [];
      for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
        var rowData = table.row( diff[i].node ).data();
        var newOrder = diff[i].newPosition;
        articlesArray.push({
          id: rowData.id,
          position: newOrder
        });
      }
      var jsonString = JSON.stringify(articlesArray);
      $.ajax({
        url     : '{{ route('admin.reorder', ['table_type' => $data['table_type']]) }}',
        type    : 'POST',
        data    : jsonString,
        dataType: 'json',
        success : function ( json ) {
          $('#datatable').DataTable().ajax.reload(); // refresh datatable
            $.each(json, function (key, msg) {
        	  // handle json response
          });
        }
      });
    });
    $.fn.dataTable.ext.errMode = 'throw';

    // Add event listener for opening and closing details
    $('#datatable tbody').on('click', '.has-child .main-column', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
            tr.next().addClass('datatable-child');
        }
    } );
});
</script>
@endsection

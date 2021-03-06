<tr
  class="published published-{!! $node->published !!}"
  data-mediatable-type="{!! snake_case(str_plural(class_basename($node))) !!}"
  data-parent-id="{!! $node->parent_id !!}"
  data-article-id="@if(!empty($node->id)){{$node->id}}@endif"
>
  <td>
    <i class="fa fa-circle fa-published"></i>
    <?php $route_name = 'admin.'.snake_case(str_plural(class_basename($node))).'.edit'; ?>
    {!! link_to_route($route_name, $node->title, [$node->id], ['class' => '']) !!}
  </td>
  @if($parent_id == 0)
    <td class="attribute">
      <a href="{{ route('admin.pages.index', $node->id) }}">&darr; </a></td>
  @else
  <td class="attribute time">{{ $node->created_at }}</td>
  @endif
  {{-- @if(count($node->children) > 0)
    <table>
    @foreach ($node->children  as $node)
      @include('admin.components.table-row-pages')
    @endforeach
    </table>
  @endif --}}
</tr>

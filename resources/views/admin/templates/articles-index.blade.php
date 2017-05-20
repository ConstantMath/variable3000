@extends('admin.app')

@section('page_title', $data['page_title'])
@section('page_class', $data['page_class'])

@section('content')
  @include('admin.components.flash-message')
  @if($articles) @foreach ($articles as $article)
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3>{{ $article->title }}</h3>
      <a href="{{ route('admin.articles.create', [$article->slug]) }}" class="pull-right"><i class="fa fa-plus-circle"></i> Add</a>
    </div>
    <div class="panel-body table-responsive">
      <table class="table">
        <tbody id="index" class="sortable">
          @if($article->children)
            @foreach ($article->children as $child)
            <tr class="published published-{!! $child->published !!}" url="{{ URL::to('/') }}/admin/articles/{{$child->id}}/reorder" parent_id="{{$child->parent_id}}">
            <td>
              <i class="fa fa-circle"></i>
              {!! link_to_route('admin.articles.edit', $child->title, [$child->parent_id, $child->id], ['class' => '']) !!}
            </td>
            <td class="attribute time">{{ $child->created_at }}</td>
          </tr>
          @endforeach @endif
        </tbody>
      </table>
    </div>
  </div>
  @endforeach @endif
@endsection

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
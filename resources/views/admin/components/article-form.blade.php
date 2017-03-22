{!! Form::hidden('parent_id', $article->parent->id) !!}
{!! Form::hidden('order', (!empty($article->order))? $article->order : '') !!}
{!! Form::hidden('image_une', (!empty($article->image_une))? $article->image_une->id : '', ['id' => 'image_une']) !!}
{!! Form::hidden('mediagallery[]', null, ['id' => 'input-mediagallery']) !!}

{{-- Is published ? --}}
<div class="form-group">
  <div class="checkbox">
    <label>
      {!! Form::checkbox('published', 1, null) !!}Published
    </label>
  </div>
</div>

{{-- Title --}}
<div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
  <label for="title">Title</label>
  {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title']) !!}
  <span class="slug">
    @if(isset($article->slug))
    <i class="fa fa-link "></i>&nbsp;{{ $article->slug }}
    @endif
  </span>
  {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
</div>

@if($article->parent->slug != 'pages')
  {{-- Intro --}}
  <div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
    <label for="intro">Intro</label>
    {!! Form::textarea('intro', null, ['class' => 'form-control', 'placeholder' => 'Intro', 'rows' => '5']) !!}
  </div>
@endif

{{-- Texte --}}
<div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
  <label for="text">Text</label>
  {!! Form::textarea('text', null, ['class' => 'form-control', 'id' => 'rich-editor', 'placeholder' => 'Text']) !!}
</div>

<hr>

{{-- Taxonomy: category --}}
<div class="form-group">
  <label for="category">Category</label>
  {!! Form::select('categories[]', $categories, null, ['class' => 'form-control select2', 'id' => '']) !!}
</div>

{{-- Taxonomy: tags --}}
<div class="form-group">
  <label for="tags_general">Tags</label>
  {!! Form::select('tag_list[]', $tags, null, ['class' => 'form-control select2', 'multiple', 'style' => 'width:100%']) !!}
</div>

{{-- Submit buttons --}}
<div class="form-group submit">
  {!! Form::submit('save', ['class' => 'btn btn-invert', 'name' => 'save']) !!}
  {!! Form::submit('save & close', ['class' => 'btn btn-primary', 'name' => 'finish']) !!}
</div>

{!! Form::hidden('type', 'project') !!}
{!! Form::hidden('article_parent', '') !!}
<div class="form-group">
  <div class="checkbox">
    <label>
      {!! Form::checkbox('published', 1, null) !!}Published
    </label>
  </div>
</div>
<div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
  {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Nom']) !!}
  {!! $errors->first('title', '<small class="help-block">:message</small>') !!}
</div>
<div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
  {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Description']) !!}
</div>
{!! Form::submit($submitButtonText, ['class' => 'btn btn-primary pull-right']) !!}

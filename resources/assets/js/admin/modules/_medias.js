$(document).ready(function() {

  // ----- Medias panel display data----- //

  if( $('.media-panel').length ){
    $('.media-panel').each(function( index ) {
      var media_type = $(this).attr('data-media-type');
      var media_table_type = $(this).attr('data-media-table-type');
      // Build media list
      getMedias(media_type, media_table_type);
    });
  }


  // ----- Medias panel add & upload ----- //

  // input file > open
  $(".media-add").click(function() {
    var input = $(this).prev();
    input.attr("type", "file");
    input.trigger('click');
    return false;
  });


  // ----- Add single media w/ Ajax ----- //

  var single_media_options = {
    success:  mediaResponse,
    dataType: 'json'
  };

  $('body').delegate('.input-single-media-upload','change', function(){
    var panel = $(this).closest('.media-panel');
    var form = $(this).closest('.single-media-form');
    form.ajaxForm(single_media_options).submit();
    panel.addClass('loading');
  });

  function mediaResponse(response, statusText, xhr, $form){
    if(response.success == true){
      addMediaInput(response.id, response.type, response.mediatable_type);
    }else{
      var panel = $('#panel-' + response.type);
      var message_field = $('#panel-' + response.type + ' .message');
      panel.removeClass('loading');
      message_field.html(response.error).fadeOut(6000);
    }
  }

})

// ----- Mixins ----- //

/* manage data list */
function getMedias(media_type, mediatable_type) {
  mediatable_type = typeof mediatable_type  === 'undefined' ? 'articles' : mediatable_type;
  var main_form_id = 'main-form';
  var article_id = $('#' + main_form_id + ' input[name=id]').val();
  var current_medias = $('#' + main_form_id + ' #' + media_type).val();
  var panel = $("#panel-"+media_type);
  // Get from DB
  if(article_id){
    $.ajax({
        dataType: 'JSON',
        url: admin_url+'/medias/index/' + media_type + '/' + mediatable_type + '/' + article_id
    }).done(function(data){
      if(data.success == true){
        printList(data.medias, media_type, mediatable_type);
        panel.removeClass('loading');
      }
    });
  // Get from input field
  }else if(current_medias){
    var medias = [];
    medias = medias.concat(current_medias);
    $.ajax({
        type: 'POST',
        url: admin_url+'/medias/get',
        data: {medias}
    }).done(function(data){
      if(data.success == true){
        printList(data.medias, media_type, mediatable_type);
        panel.removeClass('loading');
      }
    });
  }else{
    panel.removeClass('loading');
  }
}

/* manage data list */
function printList(medias, media_type, mediatable_type) {
  mediatable_type = typeof mediatable_type  === 'undefined' ? 'articles' : mediatable_type;
  if(medias && media_type){
    var ul = $('#panel-'+media_type+' .media-list');
    var	li = '';
    // Json Medias loop
    $.each( medias, function( key, value ) {
      // Build <li>
      li = li + '<li class="list-group-item media-list__item" data-media-table-type="' + mediatable_type + '" data-media-id="' + value.id + '" data-article-id="' + value.mediatable_id + '" data-article-id="' + value.mediatable_id + '" data-media-type="' + value.type + '">';

      li = li + '<div class="media__infos"><p class="media__title">'+value.alt+'</p>';


      li = li + '<p><a href="" class="link link--edit" data-toggle="modal" data-target="#modal-media-edit" data-media-type="'+value.type+'" data-media-table-type="'+mediatable_type+'" data-media-id="'+value.id+'" data-media-description="'+value.description+'" data-media-ext="'+value.ext+'" data-media-alt="'+value.alt+'" data-media-name="'+value.name+'">edit</a></p>';

      li = li + '<p><a href="' + admin_url + '/medias/destroy/' + mediatable_type + '/' + value.id + '" class="link link--delete">delete</a></p></div>';

      //media preview
      if(value.ext == 'jpg' || value.ext == 'png' || value.ext == 'gif' || value.ext == 'svg' || value.ext == 'jpeg'){
        li = li + '<div class="media__preview" style="background-image:url(\'/imagecache/thumb/' + value.name + '\')"></div>';
      }else if(value.ext == 'pdf'){
        li = li + '<div class="media__preview"><span>PDF</span></div>';
      }else if(value.ext == 'mp4'){
        li = li + '<div class="media__preview"><span>VIDEO</span></div>';
      }else{
        li = li + '<div class="media__preview">FILE</div>';
      }
      li = li + '</li>';
    });
    ul.html(li);
  }
}

/* Update hidden medias inputs */
function addMediaInput(media_id, media_type, mediatable_type) {
  mediatable_type = typeof mediatable_type  === 'undefined' ? 'articles' : mediatable_type;
  var main_form_id = 'main-form';
  var medias = [];
  var inputField = $('#' + main_form_id + ' #' + media_type);
  var current_medias = inputField.val();
  if(current_medias){medias = medias.concat(current_medias)}
  medias.push(media_id);
  inputField.val(medias);
  getMedias(media_type, mediatable_type);
}
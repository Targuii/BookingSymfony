$('#add-image').click(function(){
    const index = +$('#widget-count').val();

    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g,index);

    $('#annonce_images').append(tmpl);

    $('#widget-count').val(index+1);

    deleteButtons()

});

function updateCounter(){

    const count = +$('#annonce_images div.form-group').length;
    $('#widget-count').val(count);
}

function deleteButtons(){

    $('button[data-action = "delete"]').click(function(){

        const target = this.dataset.target;

        $(target).remove();
    });
}
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

updateCounter();
deleteButtons();
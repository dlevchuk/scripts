$('.btn-toggle>.btn').click(function() {
    var parrent = $(this).parent('.btn-toggle');
    
    if (!$(this).hasClass('active')) {
        parrent.find('.btn').removeClass('active');
      $(this).toggleClass('active');  
    
        if (parrent.find('.btn-primary').size()>0) {
            parrent.find('.btn').toggleClass('btn-primary');
        }
        if (parrent.find('.btn-danger').size()>0) {
            parrent.find('.btn').toggleClass('btn-danger');
        }
        if (parrent.find('.btn-success').size()>0) {
            parrent.find('.btn').toggleClass('btn-success');
        }
        if (parrent.find('.btn-info').size()>0) {
            parrent.find('.btn').toggleClass('btn-info');
        }
        
        parrent.find('.btn').toggleClass('btn-default');
                          }
    
       
});

$("input.ch-alarm").click(function() {
      var ip_ping_alarm = $(this).attr("data-ip");
      var $this = $(this);
      $.ajax({
        url:"switch_log_un.php",
        type:"POST",
        data:{ip_ping_alarm:ip_ping_alarm},
        success: function(res) {
            alert(res);
            // $this.toggleClass(res);
        }
      });
});

$("input.sh_log").click(function() {
      var ip_ping = $(this).attr("data-ip");
      $.ajax({
        url:"switch_log_un.php",
        type:"POST",
        data:{ip_ping:ip_ping},
        success: function(res) {
            $(".show_res").html(res);
        }
      });
    });

$("button#add_dev_btn").click(function() {
    $("#add_dev").addClass("visible");
});

$(document).ready(function() { // вся мaгия пoслe зaгрузки стрaницы
 

$("#ajax-form").submit(function(event){
  // cancels the form submission
  event.preventDefault();
  submitForm($(this));  
});


function submitForm(form){
    var data = form.serialize();
    var error = false;    
    $.ajax({
        type: "POST",
        url: "test.php",
        dataType: 'json',
        data: data,
        success: function(data){ // сoбытиe пoслe удaчнoгo oбрaщeния к сeрвeру и пoлучeния oтвeтa
          if (data['error']) { // eсли oбрaбoтчик вeрнул oшибку
            alert(data['error']); // пoкaжeм eё тeкст
          } 
          else { // eсли всe прoшлo oк
            $('#myModal').modal('hide');
            $("#myModals").modal('show');
          }
        },
        error: function (xhr, ajaxOptions, thrownError) { // в случae нeудaчнoгo зaвeршeния зaпрoсa к сeрвeру
          alert('asdasd');
          alert(xhr.status); // пoкaжeм oтвeт сeрвeрa
          alert(thrownError); // и тeкст oшибки

        },
    });
}

$(function() {
    //при нажатии на кнопку с id="save"
    $('#save').click(function() {
      //переменная formValid
      var formValid = true;
      //перебрать все элементы управления input 
      $('input').each(function() {
      //найти предков, которые имеют класс .form-group, для установления success/error
      var formGroup = $(this).parents('.form-group');
      //найти glyphicon, который предназначен для показа иконки успеха или ошибки
      var glyphicon = formGroup.find('.form-control-feedback');
      //для валидации данных используем HTML5 функцию checkValidity
      if (this.checkValidity()) {
        //добавить к formGroup класс .has-success, удалить has-error
        formGroup.addClass('has-success').removeClass('has-error');
        //добавить к glyphicon класс glyphicon-ok, удалить glyphicon-remove
        glyphicon.addClass('glyphicon-ok').removeClass('glyphicon-remove');
      } else {
        //добавить к formGroup класс .has-error, удалить .has-success
        formGroup.addClass('has-error').removeClass('has-success');
        //добавить к glyphicon класс glyphicon-remove, удалить glyphicon-ok
        glyphicon.addClass('glyphicon-remove').removeClass('glyphicon-ok');
        //отметить форму как невалидную 
        formValid = false;  
      }
    });
  });
});


   
});
function clearlog(){
    $(".show_res").html("");
}
function clearAddDev(){
    $("#add_dev").html("");
}
 
function div_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }




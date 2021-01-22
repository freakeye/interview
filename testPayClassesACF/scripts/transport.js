// Отправить форму в handler.php на ajax
// Событие клика на текущей кнопке запускает отправку родительской формы
$(function() {
  var answBox = $('#answBox');
  
  $('button').on('click',
    function() {
      var curForm = $(this).closest('form'),
          actionBtn = $(this).val();
      answBox.html('');
      curForm.trigger('submit', actionBtn);
      return false;
    });

  $('form').on('submit',
    function(event, actionParametr) {
      event.preventDefault();
      var submitString = '&submit=' + actionParametr;
      var formData = $('form').serialize() + submitString;
      $.ajax({
          type: $('form').attr('method'),
          url: './lib/handler.php',
          data: formData
      }).done(function(data) {
          answBox.append('<div class=\'payBox\'>');
          answBox.children().html(data);
        }).fail(function(data) {
          answBox.children().html(data);
        });
      });
});

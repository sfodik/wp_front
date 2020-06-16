( function($) {
  $(document).ready(function() {
    $('#tasks_table').DataTable();

    $('a[href=\'#add_new_task\']').on('click', function(e) {
      e.preventDefault();
      $('#add_new_task_popup').modal('show');
    });

    $('#create-new-task').on('submit', function(e) {
      e.preventDefault();

      var form = $(this),
          form_data = new FormData();

      form_data.append('action', 'create_new_task');
      form_data.append('task_title', form.find('input[name=task_title]').val());
      form_data.append('freelancer', form.find('select[name=freelancer]').val());

      return fetch(taskTable.ajax_url, {
        method: 'post',
        body: form_data,
      }).then(function(res) {
        return res.json();
      }).then(function(response) {
        if (response.type === 'error') {
          alert(response.message);
        }
        else {
          alert(response.message);
          document.location.reload(true);
        }
      });
    });
  });
})(jQuery);
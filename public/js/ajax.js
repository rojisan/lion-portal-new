$(document).ready(function () {

    //Insert data
    $("body").on("click","#createNewadmin",function(e){
    
        e.preventDefault;
        $('#userCrudModal').html("Create admin");
        $('#submit').val("Create admin");
        $('#modal-id').modal('show');
        $('#admin_id').val('');
        $('#admindata').trigger("reset");
    
    });
    
    //Save data into database
    $('body').on('click', '#submit', function (event) {
        event.preventDefault()
        var id = $("#admin_id").val();
        var name = $("#name").val();
        var address = $("#address").val();
       
        $.ajax({
          url: store,
          type: "POST",
          data: {
            id: id,
            name: name,
            address: address
          },
          dataType: 'json',
          success: function (data) {
              
              $('#admindata').trigger("reset");
              $('#modal-id').modal('hide');
              Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Success',
                showConfirmButton: false,
                timer: 1500
              })
              get_company_data()
          },
          error: function (data) {
              console.log('Error......');
          }
      });
    });
    
    //Edit modal window
    $('body').on('click', '#editCompany', function (event) {
    
        event.preventDefault();
        var id = $(this).data('id');
       
        $.get(store+'/'+ id+'/edit', function (data) {
             
             $('#userCrudModal').html("Edit company");
             $('#submit').val("Edit company");
             $('#modal-id').modal('show');
             $('#company_id').val(data.data.id);
             $('#name').val(data.data.name);
             $('#address').val(data.data.address);
         })
    });
    
     //DeleteCompany
     $('body').on('click', '#deleteCompany', function (event) {
        if(!confirm("Do you really want to do this?")) {
           return false;
         }
    
         event.preventDefault();
        var id = $(this).attr('data-id');
     
        $.ajax(
            {
              url: store+'/'+id,
              type: 'DELETE',
              data: {
                    id: id
            },
            success: function (response){
              
                Swal.fire(
                  'Remind!',
                  'Company deleted successfully!',
                  'success'
                )
                get_company_data()
            }
         });
          return false;
       });
    
    }); 
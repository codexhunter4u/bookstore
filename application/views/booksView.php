<!DOCTYPE html>
<html>
    <head> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book CRUD Application for users</title>
    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css')?>" rel="stylesheet">
    <style type="text/css">
        .not-available{
            color: red;
            font-weight: 700;
        }
        .available{
            color: green;
            font-weight: 700;
        }
        .btn-group-sm>.btn, .btn-sm{
            padding: 4px 8px !important;
        }
        .divider{

            height:3px; border:none; color:rgb(60,90,180); background-color:rgb(60,90,180);
            margin-bottom: 0px;
        }
    </style>
    </head> 
<body>
    <div class="container">
        <h1 style="font-size:20pt">Book CRUD Application for users</h1>
        <hr class="divider">
        <br />
        <button class="btn btn-success" onclick="add_books()"><i class="glyphicon glyphicon-plus"></i> Add Book</button>
        <button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
        <br />
        <br />
        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Book Name</th>
                    <th>Author Name</th>
                    <th>Issued Date</th>
                    <th>Return Date</th>
                    <th>Book Status</th>
                    <th style="width:125px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>

            <tfoot>
            <tr>
                <th>Book Name</th>
                <th>Author Name</th>
                <th>Issued Date</th>
                <th>Return Date</th>
                <th>Book Status</th>
                <th>Action</th>
            </tr>
            </tfoot>
        </table>
    </div>

<script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js')?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js')?>"></script>
<script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js')?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.js')?>"></script>

<script type="text/javascript">

var save_method; //for save method string
var table;

$(document).ready(function() {

    //datatables
    table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('BookController/getBookList')?>",
            "type": "POST"
        },

        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ -1 ], //last column
            "orderable": false, //set not orderable
        },
        ],

    });

    //set input/textarea/select event when change value, remove class error and remove text help block 
    $("input").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });
    $("textarea").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });
    $("select").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });

});



function add_books()
{
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Add books'); // Set Title to Bootstrap modal title
}

function issue_books(id){

    $.ajax({
        url : "<?php echo site_url('BookController/issue_books/')?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
            if(data['status'] === false){
                alert("Ohh sorry..! This book is already issued to someone");
            }else{
                reload_table();
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error in issuing book');
        }
    });

}

function return_books(id){

    $.ajax({
        url : "<?php echo site_url('BookController/return_books/')?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
            reload_table();
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error in returning book');
        }
    });

}

function edit_books(id)
{
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo site_url('BookController/edit_books/')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id"]').val(data.id);
            $('[name="book_name"]').val(data.book_name);
            $('[name="author_name"]').val(data.author_name);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit books'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;

    if(save_method == 'add') {
        url = "<?php echo site_url('BookController/add_books')?>";
    } else {
        url = "<?php echo site_url('BookController/update_books')?>";
    }

    // ajax adding data to database
    $.ajax({
        url : url,
        type: "POST",
        data: $('#form').serialize(),
        dataType: "JSON",
        success: function(data)
        {

            if(data.status) //if success close modal and reload ajax table
            {
                $('#modal_form').modal('hide');
                reload_table();
            }
            else
            {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                }
            }
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 

        }
    });
}

function delete_books(id)
{
    if(confirm('Are you sure delete this book?'))
    {
        // ajax delete data to database
        $.ajax({
            url : "<?php echo site_url('BookController/delete_books')?>/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                //if success reload ajax table
                $('#modal_form').modal('hide');
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error deleting data');
            }
        });

    }
}

</script>

</body>
</html>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Students List</title>
</head>
<style>
    .required:after {
        content: " *";
        color: red;
    }

</style>

<body>
    <div class="container">
        <div class="row mt-2">
            <div class="col">
                <h1>Students List</h1>
                <a href="{{ route('students.create') }}" class="btn btn-primary float-end" data-bs-toggle="modal"
                    data-bs-target="#exampleModal">Add Student</a>
                <div class="alert alert-primary float-start d-none" id='success-alert'></div>

            </div>
        </div>
        {{-- table --}}
        <div class="row">
            <div class="col">
                <table class="table table-bordered table-striped table-hover mt-1">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Image</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody id='tablebody'>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- edit modal --}}
    <div class="modal" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Student Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" id='editform'>
                        @csrf
                        @method('PUT')
                        <ul class="alert-danger d-none" id="alert-edit"></ul>
                        <input type="hidden" name="id" id='eid'>
                        <div class="mb-3">
                            <label class="form-label required">Student Name:</label>
                            <input type="text" class="form-control" name="name" id="ename">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Email address:</label>
                            <input type="email" class="form-control" name="email" id="eemail">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Select image:</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Record</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- insert modal --}}
    <div class="modal" id="exampleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Student Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data"
                        id='insertform'>
                        @csrf
                        <ul class="alert-danger d-none" id="alert"></ul>
                        <div class="mb-3">
                            <label class="form-label required">Student Name:</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label required">Email address:</label>
                            <input type="email" class="form-control" name="email" id="exampleInputEmail1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Select image:</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            fatchdata();
            //fetch script
            function fatchdata() {
                $.ajax({
                    type: "GET",
                    url: "/students/fetchdata",
                    // data: "data",
                    dataType: "json",
                    success: function(response) {
                        $('#tablebody').html("");
                        response.students.forEach(student => {
                            $('#tablebody').append('<tr>\
                                        <th scope="row">' + student.id + '</th>\
                                        <td>' + student.name + '</td>\
                                        <td>' + student.email + '</td>\
                                        <td>' + student.image + '</td>\
                                        <td><button value=' + student.id + ' class="btn btn-primary" id="edit_btn">Edit</button>\
                                        <button type="button" value=' + student.id + ' class="btn btn-danger del" >Delete</button>\
                                            </td>\
                                    </tr>');
                        });
                    }
                });
            }

            //insert script
            $('#insertform').submit(function(e) {

                e.preventDefault();
                let formdata = new FormData(this);
                $('#alert').html("");
                $('#success-alert').html("");
                $.ajax({
                    type: "POST",
                    data: formdata,
                    contentType: false,
                    processData: false,
                    success: function(response) {

                        if (response.status == 400) {
                            // console.log(response.status);
                            // console.log(response.message);
                            $('#alert').removeClass('d-none');
                            $.each(response.message, function(key, error) {
                                $('#alert').append('<li>' + error + '</li>');
                            });

                        } else if (response.status == 200) {

                            $('#exampleModal').modal('toggle');
                            $('#insertform').trigger("reset");
                            alert(response.message);
                            fatchdata();
                        }
                    }
                });

            });

            //delete srcipt
            $('body').on("click", '.del', function(e) {
                e.preventDefault();
                let id = $(this).val();
                $.ajax({
                    type: "DELETE",
                    url: "/students/" + id,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        alert(response.message);
                        fatchdata();
                    }
                });
            });

            // show data in editModal script
            $('body').on('click', '#edit_btn', function(e) {
                e.preventDefault();
                let id = $(this).val();
                $.ajax({
                    type: "get",
                    url: "/students/" + id + "/edit",
                    success: function(response) {
                        $('#editModal').modal('toggle');
                        $('#eid').val(response.student.id);
                        $('#ename').val(response.student.name);
                        $('#eemail').val(response.student.email);
                    }
                });
            });

            // edit script
            $(document).on('submit', '#editform', function(e) {
                e.preventDefault();

                var fdata = new FormData($('#editform')[0]);
                fdata.append('_method', 'PUT');
                 let id = $('#eid').val();
                // let name=$('#ename').val();
                // let email=$('#eemail').val();
                // fdata.append('email', email);
                // console.log(fdata); 
                // console.log(name);
                // console.log(email);
                // for (var key of fdata.entries()) {
                //     console.log(key[0] + ', ' + key[1]);
                // }
                $('#alert-edit').html("");
                $('#success-alert').html("");
                
                $.ajax({
                    type: "POST",
                    url: "students/" + id,
                    data: fdata,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status == 400) {
                            // console.log(response.status);
                            // console.log(response.message);
                            $('#alert-edit').removeClass('d-none');
                            $.each(response.message, function(key, error) {
                                $('#alert-edit').append('<li>' + error + '</li>');
                            });

                        } else if (response.status == 200) {

                            $('#editModal').modal('toggle');
                            $('#editform').trigger("reset");
                            alert(response.message);
                            fatchdata();
                        }
                    }
                });
            });
           

        });
    </script>
</body>

</html>

@extends('layouts.app')
@section('css')
    <style>
        .image{
            width: 100%;
            height: 200px;
        }
        .files input {
            outline: 2px dashed #92b0b3;
            outline-offset: -10px;
            -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
            transition: outline-offset .15s ease-in-out, background-color .15s linear;
            padding: 120px 0px 85px 35%;
            text-align: center !important;
            margin: 0;
            width: 100% !important;
        }
        .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
            -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
            transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
        }
        .files{ position:relative}
        .files:after {  pointer-events: none;
            position: absolute;
            top: 60px;
            left: 0;
            width: 50px;
            right: 0;
            height: 56px;
            content: "";
            background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);
            display: block;
            margin: 0 auto;
            background-size: 100%;
            background-repeat: no-repeat;
        }
        .color input{ background-color:#f1f1f1;}
        .files:before {
            position: absolute;
            bottom: -5px;
            left: 0;  pointer-events: none;
            width: 100%;
            right: 0;
            height: 57px;
            content: " or drag it here. ";
            display: block;
            margin: 0 auto;
            color: #2ea591;
            font-weight: 600;
            text-transform: capitalize;
            text-align: center;
        }
        .img-fluid{
            object-fit: cover;
            height: 200px;
        }
        .overflow-hidden{
            overflow: hidden;
        }
        .animate-title{
            width: 100%;
            height: 30px;
            overflow: hidden;
        }
        .animate-title h4{
            position: absolute;
            white-space: nowrap;
            animation: floatText 5s infinite alternate ease-in-out;
        }

        @-webkit-keyframes floatText{
            from {
                left: 00%;
            }

            to {
                 /*left: auto;*/
                left: 100%;
            }
        }
    </style>
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-2">
            <button type="button" onclick="btnUpload()" class="btn btn-primary" data-toggle="modal" data-target="#uploadImagModal">Upload Images</button>
        </div>
        <div class="col-md-10">
            <input type="text" id="search_key" class="form-control" placeholder="Search">
        </div>
        <input hidden value="{{ json_encode($images) }}" id="imagesJson">
    </div>
    <div class="row" id="image-filter-lar">
        @foreach($images as $image)
            <div class="col-md-2 text-center mt-5 overflow-hidden">
                <div class="image">
                    <a class="thumbnail fancybox" rel="ligthbox" href="{{ url('storage/images/', $image->image) }}">
                        <img class="img-fluid" src="{{ url('storage/images/', $image->image) }}" alt="">
                    </a>
                </div>
                <div class="images-title {{ strlen($image->title) > 10 ? 'animate-title' : '' }}">
                    <h4>{{ $image->title }}</h4>
                </div>
                <a onclick="confirmDelete({{ $image->id }})" class="btn"><i class="fa fa-trash"></i> Remove</a>
            </div>
         @endforeach
    </div>
    <div id="image-filter">

    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="uploadImagModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="errors">

                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow=""
                         aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                        0%
                    </div>
                </div>
                <br>
                <div id="success">

                </div>
                <br>
                <form method="post" id="uploadForm" action="#" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group files color">
                        <label>Upload Your File </label>
                        <input type="file" class="form-control" name="image" id="image" accept="image/png">
                    </div>
                    <div class="form-group">
                        <label for="title">Image title</label>
                        <input type="text" class="form-control" name="title" id="title">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" onclick="submitForm()" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            $(".fancybox").fancybox({
                openEffect: "none",
                closeEffect: "none"
            });

            $("#search_key").on("keypress", function (event) {
                var searchKey = $(this).val() + event.key;

                if(searchKey === '')  {
                    $('#image-filter').html('');
                    return;
                }
                getData(searchKey);

            });
        });
       function submitForm(){
            var formData = new FormData();
           formData.append('title', $("#title").val());
           formData.append("image",$("#image")[0].files[0]);

            $.ajax({
                url: "<?php echo route('admin.upload') ?>",
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                type: "POST",
                data:formData,
                contentType: false,
                cache: false,
                processData:false,
                beforeSend:function(){
                    $('#success').empty();
                },
                uploadProgress:function(event, position, total, percentComplete)
                {
                    $('.progress-bar').text(percentComplete + '%');
                    $('.progress-bar').css('width', percentComplete + '%');
                },
                success:function(data)
                {
                    if(data.errors)
                    {
                        console.log(data.errors)
                        $('.progress-bar').text('0%');
                        $('.progress-bar').css('width', '0%');
                        $('#success').html('<span class="text-danger"><b>'+data.errors+'</b></span>');
                    }
                    if(data.success)
                    {
                        $("#imagesJson").val(JSON.stringify(data.images))
                        var em = '';
                        getData(em);
                        $('.progress-bar').text('Uploaded');
                        $('.progress-bar').css('width', '100%');
                        $('#success').html('<span class="text-success"><b>'+data.message+'</b></span><br /><br />');
                        $("#title").val('');
                        $("#image").val('');
                    }

                },
                error: function(xhr, status, error)
                {

                    $.each(xhr.responseJSON.errors, function (key, item)
                    {
                        $("#errors").append("<li class='alert alert-danger'>"+item+"</li>")
                    });

                }
            });
       }
       function getData(searchKey) {
           $("#image-filter-lar").css('display', 'none');
           var data = JSON.parse($("#imagesJson").val())
           var regex = new RegExp(searchKey, "i");
           var output = '<div class="row">';

           $.each(data, function(key, val){

               if (val.title.search(regex) !== -1) {
                   output += '<div class="col-md-2 text-center mt-5 overflow-hidden">';
                   output += '<div style="width: 100%; height: 200px; display: block;" class="image">';
                   output +='<a class="thumbnail fancybox" rel="ligthbox" href="storage/images/'+val.image+'">';
                   output +='<img style="width: 100%; height: 200px; object-fit: cover;" class="img-responsive" src="storage/images/'+val.image+'" alt="'+ val.title +'">';
                   output += '</a>';
                   output += '</div>';
                   output += '<div class="images-title">';
                   output += '<h4>' + val.title + '</h4>';
                   output += '</div>';
                   output += '<a onclick="confirmDelete('+val.id+')" href="#" class="btn"><i class="fa fa-trash"></i> Remove</a>';
                   output += '</div>';
               }

           });
           output += '</div>';
           $('#image-filter').html(output);
       }
       function btnUpload() {
           $('#success').empty();
           $('.progress-bar').css('width', '0%');
       }
       function confirmDelete(id) {
           swal({
               title: "Delete?",
               text: "Please ensure and then confirm!",
               type: "warning",
               showCancelButton: !0,
               confirmButtonText: "Yes, delete it!",
               cancelButtonText: "No, cancel!",
               reverseButtons: !0
           }).then(function (e) {
               if (e === true) {
                   var token = $('input[name="_token"]').val()
                   console.log(token)
                   $.ajax({
                       type: 'POST',
                       url: "{{url('/image/delete')}}/" + id,
                       data: {_token: token},
                       dataType: 'JSON',
                       success: function (results) {
                           $("#imagesJson").val(JSON.stringify(results.images))
                           var em = '';
                           getData(em);
                           if (results.success === true) {
                               swal("Done!", results.message, "success");
                               // location.reload();
                           } else {
                               swal("Error!", results.message, "error");
                           }
                       }
                   });

               } else {
                   e.dismiss;
               }

           }, function (dismiss) {
               // console.log(dismiss)
               return false;
           })
        }
    </script>
@endsection

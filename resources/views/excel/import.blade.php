
<section>
    <div class="container">
        <div class="row justify-content-center">
        <div class="card">
       <div class="card-header">Export excel File Example</div>
        <form action="/export" method="POST" enctype="multipart/form-data">
                @csrf
            <button type="sumbit" class="btn btn-primary"  />DownloadFile</button>
            </form>
            </div>

            <div class="card">
                <div class="card-header">import excel File Example</div>

                <div class="card-body">
                    @if ($message = Session::get('success'))

                        <div class="alert alert-success alert-block">

                            <button type="button" class="close" data-dismiss="alert">×</button>

                            <strong>{{ $message }}</strong>

                        </div>

                    @endif

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                        <form action="/import" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <input type="file" class="form-control-file" name="fileToUpload" id="exampleInputFile" aria-describedby="fileHelp">
                            </div>
                            <button type="submit" class="btn btn-primary">Import File</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
    </section>

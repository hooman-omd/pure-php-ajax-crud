<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Notes CRUD</title>
</head>

<body>
    <div class="container">
        <h1 class="text-center mt-4">Notes CRUD</h1>
        <div class="my-4 p-4 border rounded">
            <div class="row">
                <input type="hidden" name="note_id" id="note_id">
                <div class="col-lg-4 my-2"><input class="form-control" type="text" id="title" placeholder="title"></div>
                <div class="col-lg-4 my-2"><input class="form-control" type="text" id="description" placeholder="description"></div>
                <div class="col-lg-4 my-2"><button class="btn btn-primary" id="insertBtn">Save note</button></div>
            </div>
        </div>

        <div class="my-4 p-4 border rounded">
            <div class="row">
                <div class="col-lg-4 my-2"><input class="form-control" type="text" id="search-box" placeholder="Enter your key"></div>
                <div class="col-lg-4 my-2"><button class="btn btn-success" id="searchBtn">Search</button>
                    <button class="btn btn-warning" id="showAllBtn">Show all notes</button>
                </div>
            </div>
        </div>
        <table class="table mt-4 text-center">
            <thead>
                <tr>
                    <th width="10">id</th>
                    <th width="20">title</th>
                    <th width="36">description</th>
                    <th width="7">created_at</th>
                    <th width="7">updated_at</th>
                    <th width="20">operations</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        
        <div class="d-flex justify-content-center">
            <ul class="pagination">
            </ul>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function loadData(operation,offset) {
            $.ajax({
                url: 'control.php',
                dataType: 'json',
                method: 'get',
                data: {
                    'operation': operation,
                    'offset': offset,
                    'rows-per-page':4 ,
                    'searchTerm': $('#search-box').val()
                },
                success: function(result) {
                    $('table tbody').empty();
                    let notes = result.data;
                    let pages = result.pages;
                    let page=``;
                    for(let i=1;i<=pages;i++){
                        page += `
                            <li class="page-item ${offset == i-1 ? 'active':''}"><button class="page-link" data-page=${i-1}>${i}</button></li>
                        `;
                    }
                    $('ul.pagination').empty();
                    $('ul.pagination').append(page);
                    
                    $.each(notes, function(index, data) {
                        let row = `
                        <tr>
                            <td class="py-2">${data.id}</td>
                            <td class="py-2">${data.title}</td>
                            <td class="py-2">${data.description}</td>
                            <td class="py-2">${data.created_at}</td>
                            <td class="py-2">${data.updated_at}</td>
                            <td class="py-2"><button class="btn btn-primary me-3 d-inline-block updateBtn" data-noteid="${data.id}" data-title="${data.title}" data-description="${data.description}">update</button><button class="btn btn-danger d-inline-block deleteBtn" data-noteid="${data.id}">delete</button></td>
                        </tr>
                        `;
                        
                        $('table tbody').append(row);
                    });
                }
            });
        }

        $(function() {
            loadData('findAll',0);

            $(document).on('click', '.page-link', function(){
                let operation = $('#search-box').val() == '' ? 'findAll' : 'find';
                loadData(operation,$(this).data('page'));
            });

            $('#searchBtn').click(function() {
                loadData('find',0);
            });

            $('#showAllBtn').click(function() {
                loadData('findAll',0);
                $('#search-box').val('');
            });



            $('#insertBtn').click(function() {
                $.ajax({
                    url: 'control.php',
                    method: 'post',
                    data: {
                        'operation': 'insert',
                        'id': $('#note_id').val(),
                        'title': $('#title').val(),
                        'description': $('#description').val()
                    },
                    success: function(result) {
                        console.log('insertion done');
                        $('#note_id').val('');
                        $('#title').val('');
                        $('#description').val('');
                        loadData('findAll',0);
                    }
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                if (confirm('Are you sure you want to delete this note?')) {
                    $.ajax({
                        url: 'control.php',
                        method: 'post',
                        data: {
                            'operation': 'delete',
                            'id': $(this).data('noteid')
                        },
                        success: function(result) {
                            console.log('delete done');
                            loadData('findAll',0);
                        },
                        error: function(xhr, status, error) {
                            console.error('Delete failed:', error);
                        }
                    });
                }
            });

            $(document).on('click', '.updateBtn', function() {
                $('#note_id').val($(this).data('noteid'));
                $('#title').val($(this).data('title'));
                $('#description').val($(this).data('description'));
            });

            // $('.deleteBtn').click(function() {
            //     $.ajax({
            //         url: 'control.php',
            //         method: 'post',
            //         data: {
            //             'operation': 'delete',
            //             'id': $(this).data('noteid')
            //         },
            //         success: function(result) {
            //             console.log('delete done');
            //             loadData();
            //         }
            //     });
            // });
            // The issue with your delete functionality is that you're binding the click event to the .deleteBtn elements when the page loads, but these elements are dynamically added later when loadData() is called. You need to use event delegation to handle clicks on dynamically created elements.
        });
    </script>

</body>

</html>
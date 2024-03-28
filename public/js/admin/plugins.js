(function () {
  $('.dt-datatable').dataTable({ 
    'order': [], 
    'columnDefs': [{ 'targets': 'no-sort', 'orderable': false }] ,
    'language': {
      'url': 'dataTables.spanish.json'
    },
  });
} ())
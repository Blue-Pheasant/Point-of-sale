(function () {
  $('.dt-datatable').dataTable({ 
    'order': [], 
    'columnDefs': [{ 'targets': 'no-sort', 'orderable': false }] ,
    'language': {
      'url': window.location.protocol + '//' + window.location.host + '/js/admin/dataTables.vn.json'
    },
  });
} ())
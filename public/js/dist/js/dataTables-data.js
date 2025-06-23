/*DataTable Init*/

"use strict"; 

$(document).ready(function() {
	"use strict";
	
	$('#datable_1').DataTable(
		{ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		 "bSort": false,
    "oLanguage": {
        "sSearch": " "
      },
    dom: 'Blfrtip',
    buttons: [
        'csv',  { extend: 'excel',
        footer: true,
        exportOptions: {
            columns: ':visible'
             }
            },{ extend: 'pdf',
        footer: true,
        orientation: 'landscape',
        exportOptions: {
            columns: ':visible'
             }
            },{ extend:'print',
            footer: true,
            orientation: 'landscape',
            exportOptions: {
                columns: ':visible'
                 }
                }
	],"footerCallback": function ( row, data, start, end, display ) {
		var api = this.api(), data;

		// converting to interger to find total
		var intVal = function ( i ) {
			return typeof i === 'string' ?
				i.replace(/[\$,]/g, '')*1 :
				typeof i === 'number' ?
					i : 0;
		};

		// computing column Total of the complete result 
		var Total = api
			.column( 5, {page:'current'}  )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );
			

			var Totalall = api
			.column( 5 )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );
			
			
		
			
		// Update footer by showing the total with the reference of the column index 
	// $( api.column( 4 ).footer() ).html('Total');
		// $( api.column( 5 ).footer() ).html(Total+'('+Totalall+')');
		
	}
   }
	
	);
	$('#datable_2').DataTable({ "lengthChange": false});
	 
	$('#datable_3').DataTable(
		{ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "oLanguage": {
        "sSearch": " "
      },
    dom: 'Blfrtip',
    buttons: [
        'csv',  { extend: 'excel',
        footer: true,
        exportOptions: {
            columns: ':visible'
             }
            },{ extend: 'pdf',
        footer: true,
        orientation: 'landscape',
        exportOptions: {
            columns: ':visible'
             }
            },{ extend:'print',
            footer: true,
            orientation: 'landscape',
            exportOptions: {
                columns: ':visible'
                 }
                }
	],"footerCallback": function ( row, data, start, end, display ) {
		var api = this.api(), data;

		// converting to interger to find total
		var intVal = function ( i ) {
			return typeof i === 'string' ?
				i.replace(/[\$,]/g, '')*1 :
				typeof i === 'number' ?
					i : 0;
		};

		// computing column Total of the complete result 
		var Total = api
			.column( 4, {page:'current'}  )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );
			

			var Totalall = api
			.column( 4 )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );
			
			
		
			
		// Update footer by showing the total with the reference of the column index 
	$( api.column( 3 ).footer() ).html('Total');
		$( api.column( 4 ).footer() ).html(Total+'('+Totalall+')');
		
	}
   }
	
	);

	$('#datable_4').DataTable(
		{ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "oLanguage": {
        "sSearch": " "
      },
    dom: 'Blfrtip',
    buttons: [
        'csv',  { extend: 'excel',
        footer: true,
        exportOptions: {
            columns: ':visible'
             }
            },{ extend: 'pdf',
        footer: true,
        orientation: 'landscape',
        exportOptions: {
            columns: ':visible'
             }
            },{ extend:'print',
            footer: true,
            orientation: 'landscape',
            exportOptions: {
                columns: ':visible'
                 }
                }
	],"footerCallback": function ( row, data, start, end, display ) {
		var api = this.api(), data;

		// converting to interger to find total
		var intVal = function ( i ) {
			return typeof i === 'string' ?
				i.replace(/[\$,]/g, '')*1 :
				typeof i === 'number' ?
					i : 0;
		};

		// computing column Total of the complete result 
		var Total = api
			.column( 10, {page:'current'}  )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );
			

			var Totalall = api
			.column( 10 )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );

			var Total2 = api
			.column( 11, {page:'current'}  )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );
			

			var Totalall2 = api
			.column( 11 )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );
			
			var Total3 = api
			.column( 12, {page:'current'}  )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );
			

			var Totalall3 = api
			.column( 12 )
			.data()
			.reduce( function (a, b) {
				return (intVal(a) + intVal(b)).toFixed(2);
			}, 0 );
			
			
		
			
		// Update footer by showing the total with the reference of the column index 
	$( api.column( 4 ).footer() ).html('Total');
		$( api.column( 10 ).footer() ).html(Total+'('+Totalall+')');
		$( api.column( 11 ).footer() ).html(Total2+'('+Totalall2+')');
		$( api.column( 12 ).footer() ).html(Total3+'('+Totalall3+')');
		
	}
   }
	
	);



} );

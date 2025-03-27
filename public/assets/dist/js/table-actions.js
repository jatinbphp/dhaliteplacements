$(document).ready(function() {
    $('#l-Company').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#route_name").val(),
        columns: [
            {
                data: 'id', name: 'id',
                render: function(data, type, row) {
                    return '#' + data; // Prepend '#' to the 'id' data
                }
            },
            { data: 'company_name', name: 'company_name' },
            { data: 'address', name: 'address' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        "order": [[0, "DESC"]]
    });

    $('#b-Company').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#route_name").val(),
        columns: [
            {
                data: 'id', name: 'id',
                render: function(data, type, row) {
                    return '#' + data; // Prepend '#' to the 'id' data
                }
            },
            { data: 'company_name', name: 'company_name' },
            { data: 'address', name: 'address' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        "order": [[0, "DESC"]]
    });

    $('#p-Company').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#route_name").val(),
        columns: [
            {
                data: 'id', name: 'id',
                render: function(data, type, row) {
                    return '#' + data; // Prepend '#' to the 'id' data
                }
            },
            { data: 'company_name', name: 'company_name' },
            { data: 'address', name: 'address' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        "order": [[0, "DESC"]]
    });

    $('#our-Company').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#route_name").val(),
        columns: [
            {
                data: 'id', name: 'id',
                render: function(data, type, row) {
                    return '#' + data; // Prepend '#' to the 'id' data
                }
            },
            { data: 'company_name', name: 'company_name' },
            { data: 'address', name: 'address' },
            { data: 'phone', name: 'phone' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        "order": [[0, "DESC"]]
    });

    $('#visa').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#route_name").val(),
        columns: [
            {
                data: 'id', name: 'id',
                render: function(data, type, row) {
                    return '#' + data; // Prepend '#' to the 'id' data
                }
            },
            { data: 'name', name: 'name' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        "order": [[0, "DESC"]]
    });

    $('#candidate').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#route_name").val(),
        columns: [
            {
                data: 'id', name: 'id',
                render: function(data, type, row) {
                    return '#' + data; // Prepend '#' to the 'id' data
                }
            },
            { data: 'c_name', name: 'c_name' },
            { data: 'c_id', name: 'c_id' },
            { data: 'visa', name: 'visa' },
            { data: 'candidate_type', name: 'candidate_type' },
            { data: 'b_rate', name: 'b_rate' },
            { data: 'c_rate', name: 'c_rate' },
            { data: 'margin', name: 'margin' },
            { data: 'b_vendor', name: 'b_vendor' },
            { data: 'hr_ts', name: 'hr_ts' },
            { data: 'hr_inv', name: 'hr_inv' },
            { data: 'rem_hrs', name: 'rem_hrs' },
            { data: 'l_invoiced_date', name: 'l_invoiced_date' },
            { data: 'last_time', name: 'last_time' },
            { data: 'client', name: 'client' },
            { data: 'amt_inv', name: 'amt_inv' },
            { data: 'mapped_rec_amt', name: 'mapped_rec_amt' },
            { data: 'due_rec_amt', name: 'due_rec_amt' },
            { data: 'hrs_due', name: 'hrs_due' },
            { data: 'start_date', name: 'start_date' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        "order": [[0, "DESC"]]
    });

    $('#visa-candidate').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#route_name").val(),
        columns: [
            {
                data: 'id', name: 'id',
                render: function(data, type, row) {
                    return '#' + data; // Prepend '#' to the 'id' data
                }
            },
            { data: 'created_at', name: 'created_at' },
            { data: 'c_id', name: 'c_id' },
            { data: 'c_name', name: 'c_name' },
            { data: 'status', name: 'status' },
            { data: 'visa', name: 'visa' },
            { data: 'candidate_type', name: 'candidate_type' },
            { data: 'start_date', name: 'start_date' },
            { data: 'last_time_entry', name: 'last_time_entry' },
            { data: 'c_aggrement', name: 'c_aggrement' },
            { data: 'mec_sent_date', name: 'mec_sent_date' },
            { data: 'laptop_rec', name: 'laptop_rec' },
            { data: 'city_state', name: 'city_state' },
            { data: 'visa_start', name: 'visa_start' },
            { data: 'visa_end', name: 'visa_end' },
            { data: 'id_start', name: 'id_start' },
            { data: 'id_end', name: 'id_end' },
            { data: 'remaining_visa', name: 'remaining_visa' },
            { data: 'remaining_id', name: 'remaining_id' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        "order": [[0, "DESC"]]
    });

    $('#time-sheet').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#route_name").val(),
        columns: [
            { data: 'week_end_date', name: 'week_end_date' },
            { data: 'c_id', name: 'c_id' },
            { data: 'c_name', name: 'c_name' },
            { data: 'visa', name: 'visa' },
            { data: 'candidate_type', name: 'candidate_type' },
            { data: 'hr_ts', name: 'hr_ts' },
            { data: 'last_time', name: 'last_time' },
            { data: 'mon', name: 'mon' },
            { data: 'tue', name: 'tue' },
            { data: 'wed', name: 'wed' },
            { data: 'thu', name: 'thu' },
            { data: 'fri', name: 'fri' },
            { data: 'sat', name: 'sat' },
            { data: 'sun', name: 'sun' },
            { data: 'total_hours', name: 'total_hours' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        "order": [[0, "DESC"]]
    });
});

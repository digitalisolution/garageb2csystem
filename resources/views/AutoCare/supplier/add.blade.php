@extends('samples')
@section('content')

    <div class="container-fluid">
        <div class="bg-white p-3">
            <!-- Supplier Details Form -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link active" href="#tab-csv" data-toggle="tab"><strong>CSV Detail</strong></a></li>
                            <li class="nav-item"><a class="nav-link" href="#delivery_time" data-toggle="tab"><strong>Delivery Time</strong></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active in" id="tab-csv">
                                {{ Form::open(['url' => 'AutoCare/supplier/add', 'files' => true, 'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) }}
                                {{ csrf_field() }}
                                {{ Form::hidden('id', isset($id) ? $id : '', []) }}
                                <div class="box-header with-border">
                                    <h5>Please Fill Up Supplier Details</h5>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        @if ($errors->any())
                                            <ul class="alert alert-danger" style="list-style:none">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @if(session()->has('message.level'))
                                            <div class="alert alert-{{ session('message.level') }} alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!
                                                </h4>
                                                {!! session('message.content') !!}
                                            </div>
                                        @endif

                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label class="form-col-form-label" for="supplier_name">Supplier Name</label>
                                            {{ Form::text('supplier_name', isset($supplier_name) ? $supplier_name : '', ['class' => 'form-control', 'id' => 'supplier_name', 'required', 'placeholder' => 'Supplier Name']) }}
                                        </div>

                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label class="form-col-form-label" for="mob_num">Contact Number</label>
                                            {{ Form::number('mob_num', isset($mob_num) ? $mob_num : '', ['class' => 'form-control', 'placeholder' => 'Mobile Number']) }}
                                        </div>

                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label class="form-col-form-label" for="email">Email</label>
                                            {{ Form::text('email', isset($email) ? $email : '', ['class' => 'form-control', 'placeholder' => 'Email']) }}
                                        </div>

                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label class="form-col-form-label" for="gstin">VAT No.</label>
                                            {{ Form::text('gstin', isset($gstin) ? $gstin : '', ['class' => 'form-control', 'placeholder' => 'VAT NO.']) }}
                                        </div>

                                        <!-- Import Options -->
                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label for="import_method">Import Method</label>
                                            {{ Form::select('import_method', ['csv' => 'Upload CSV', 'ftp' => 'Fetch from FTP', 'file_path' => 'Import from File Path'], isset($import_method) ? $import_method : '', ['class' => 'form-control', 'id' => 'import_method', 'placeholder' => 'Select Import Method']) }}
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-12 form-group">
                                            <label class="form-col-form-label" for="address">Address</label>
                                            {{ Form::textarea('address', isset($address) ? $address : '', ['class' => 'form-control', 'style' => 'height:75px', 'placeholder' => 'Address']) }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- File Upload for CSV -->
                                        <div id="csv-upload" class="col-lg-3 col-md-6 col-12 import-option"
                                            style="display:none;">
                                            <div class="form-group">
                                                <label for="csv_file">Upload Supplier CSV</label>
                                                {{ Form::file('csv_file', ['class' => 'form-control', 'accept' => '.csv']) }}
                                            </div>
                                        </div>
                                        <!-- FTP Details -->
                                        <div class="col-lg-12">
                                            <div id="ftp-details" class="import-option" style="display:none;">
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="ftp_host">FTP Host</label>
                                                        {{ Form::text('ftp_host', isset($ftp_host) ? $ftp_host : '', ['class' => 'form-control', 'id' => 'ftp_host', 'placeholder' => 'Enter FTP Host']) }}
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="ftp_user">FTP Username</label>
                                                        {{ Form::text('ftp_user', isset($ftp_user) ? $ftp_user : '', ['class' => 'form-control', 'id' => 'ftp_user', 'placeholder' => 'Enter FTP Username']) }}
                                                    </div>
                                                    <div class="col-lg-2 col-md-6 col-12 form-group">
                                                        <label for="ftp_password">FTP Password</label>
                                                        {{ Form::password('ftp_password', ['class' => 'form-control', 'id' => 'ftp_password', 'placeholder' => 'Enter FTP Password']) }}
                                                    </div>
                                                    <div class="col-lg-2 col-md-6 col-12 form-group">
                                                        <label for="ftp_directory">FTP Directory</label>
                                                        {{ Form::text('ftp_directory', isset($ftp_directory) ? $ftp_directory : '', ['class' => 'form-control', 'id' => 'ftp_directory', 'placeholder' => 'Enter FTP Directory']) }}
                                                    </div>
                                                    <div class="col-lg-2 col-md-6 col-12 form-group">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-primary"
                                                            id="testConnectionButton">Test
                                                            Connection</button>
                                                        <p id="connectionStatusMessage" style="color: green;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- File Path Details -->
                                        <div id="file-path" class="col-lg-3 col-md-6 col-12 import-option"
                                            style="display:none;">
                                            <div class="form-group">
                                                <label for="file_path">File Path</label>
                                                {{ Form::text('file_path', isset($file_path) ? $file_path : '', ['class' => 'form-control', 'placeholder' => 'Enter File Path']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- API Order Enable (1 or 0) -->
                                        <div class="col-12">
                                            <h5 class="mt-3">API Order Details</h5>
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-12 form-group">
                                            <label for="api_order_enable">API Order Enable</label>
                                            {{ Form::select('api_order_enable', ['1' => 'Enabled', '0' => 'Disabled'], isset($api_order_enable) ? $api_order_enable : (isset($api_order_details['api_order_enable']) ? $api_order_details['api_order_enable'] : ''), ['class' => 'form-control', 'placeholder' => 'Select API Order Enable', 'id' => 'api_order_enable']) }}
                                        </div>

                                        <!-- Status Field -->
                                        <div class="col-lg-6 col-md-6 col-12 form-group">
                                            <label for="status">Status</label>
                                            {{ Form::select('status', ['1' => 'Active', '0' => 'Inactive'], isset($status) ? $status : '', ['class' => 'form-control', 'placeholder' => 'Select Status']) }}
                                        </div>

                                        <div class="col-lg-12">
                                            <div id="api_order_details"
                                                style="{{ isset($api_order_details['api_order_enable']) && $api_order_details['api_order_enable'] == '1' ? 'display: block;' : 'display: none;' }}">
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bond_supplieremail">Supplier Email</label>
                                                        {{ Form::text('api_order_details[bond_supplieremail]', isset($api_order_details['bond_supplieremail']) ? $api_order_details['bond_supplieremail'] : '', ['class' => 'form-control', 'placeholder' => 'Supplier Email']) }}
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bond_api_mode">API Mode</label>
                                                        {{ Form::select('api_order_details[bond_api_mode]', ['live' => 'Live', 'test' => 'Test'], isset($api_order_details['bond_api_mode']) ? $api_order_details['bond_api_mode'] : '', ['class' => 'form-control', 'placeholder' => 'Select Bond Api Mode']) }}
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bond_api_code">API Code</label>
                                                        {{ Form::text('api_order_details[bond_api_code]', isset($api_order_details['bond_api_code']) ? $api_order_details['bond_api_code'] : '', ['class' => 'form-control', 'placeholder' => 'API Code']) }}
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="trading_point">Trading Point</label>
                                                        {{ Form::text('api_order_details[trading_point]', isset($api_order_details['trading_point']) ? $api_order_details['trading_point'] : '', ['class' => 'form-control', 'placeholder' => 'Trading Point']) }}
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bond_status_autoorder">Auto Order Status</label>
                                                        {{ Form::select('api_order_details[bond_status_autoorder]', ['1' => 'Enabled', '0' => 'Disabled'], isset($api_order_details['bond_status_autoorder']) ? $api_order_details['bond_status_autoorder'] : '', ['class' => 'form-control', 'placeholder' => 'Select Auto Order Status']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Submit and Import Buttons -->
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                                </div>
                                {{ Form::close() }}
                            </div>
                            @include('AutoCare/supplier/leadtime')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const importMethodDropdown = document.getElementById('import_method');
            const csvUploadSection = document.getElementById('csv-upload');
            const ftpDetailsSection = document.getElementById('ftp-details');
            const filePathSection = document.getElementById('file-path');

            function toggleSections() {
                const selectedMethod = importMethodDropdown.value;
                csvUploadSection.style.display = selectedMethod === 'csv' ? 'block' : 'none';
                ftpDetailsSection.style.display = selectedMethod === 'ftp' ? 'block' : 'none';
                filePathSection.style.display = selectedMethod === 'file_path' ? 'block' : 'none';
            }

            toggleSections();

            importMethodDropdown.addEventListener('change', toggleSections);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tabContent = document.getElementById('ftp');
            var tabLink = document.getElementById('ftp-tab');
            document.getElementById('testConnectionButton').addEventListener('click', function () {
                var ftpHostElement = document.getElementById('ftp_host');
                var ftpUserElement = document.getElementById('ftp_user');
                var ftpPasswordElement = document.getElementById('ftp_password');

                if (!ftpHostElement || !ftpUserElement || !ftpPasswordElement) {
                    console.error('One or more form fields are missing!');
                    return;
                }

                var ftpHost = ftpHostElement.value;
                var ftpUser = ftpUserElement.value;
                var ftpPassword = ftpPasswordElement.value;

                // AJAX Request
                fetch('{{ route("ftp.test.connection") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ftp_host: ftpHost,
                        ftp_user: ftpUser,
                        ftp_password: ftpPassword
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        var connectionStatusMessage = document.getElementById('connectionStatusMessage');
                        if (data.success) {
                            connectionStatusMessage.style.color = 'green';
                            connectionStatusMessage.textContent = 'Connection successful! Directories loaded.';

                            var directorySelect = document.getElementById('ftp_directory');
                            directorySelect.innerHTML = ''; // Clear existing options
                            data.files.forEach(function (file) {
                                var option = document.createElement('option');
                                option.value = file;
                                option.textContent = file;
                                directorySelect.appendChild(option);
                            });
                        } else {
                            connectionStatusMessage.style.color = 'red';
                            connectionStatusMessage.textContent = 'Connection failed: ' + data.message;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while testing the connection.');
                    });
            });
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initially check the API Order Enable value and show/hide the API details section
            toggleApiOrderDetails();

            // Event listener to toggle the visibility when API Order Enable is changed
            document.getElementById('api_order_enable').addEventListener('change', toggleApiOrderDetails);
        });

        function toggleApiOrderDetails() {
            const apiOrderEnable = document.getElementById('api_order_enable').value;
            const apiOrderDetailsSection = document.getElementById('api_order_details');

            if (apiOrderEnable == '1') {
                apiOrderDetailsSection.style.display = 'block'; // Show API details
            } else {
                apiOrderDetailsSection.style.display = 'none'; // Hide API details
            }
        }
    </script>
@endsection
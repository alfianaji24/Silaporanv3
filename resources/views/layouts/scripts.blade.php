 <!-- build:js assets/vendor/js/core.js -->

 <script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
 <script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/hammer/hammer.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/i18n/i18n.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
 <script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/jquery.maskMoney.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/easy-number-separator.js') }}"></script>

 <!-- endbuild -->

 <!-- Vendors JS -->
 <!-- Vendors JS -->
<!-- Non-jQuery dependencies - can use defer -->
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}" defer></script>
<script src="{{ asset('assets/external/js/polyfill.js') }}" defer></script>
<script src="{{ asset('assets/vendor/js/feather.min.js') }}" defer></script>
<script src="{{ asset('assets/external/js/leaflet.js') }}" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin="" defer></script>
<script src="{{ asset('assets/external/js/leaflet-routing-machine.js') }}" defer></script>
<script src="{{ asset('assets/external/js/webcam.min.js') }}" defer></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="{{ asset('assets/external/js/ionicons.js') }}" defer></script>

<!-- jQuery-dependent scripts - must load after jQuery -->
 <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
 <script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
 <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
 <script src="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-start-end-date/index.min.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/toastr.min.js') }}"></script>
 <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
 <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
 <script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/freeze-table.js') }}"></script>
 <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
 <script src="{{ asset('assets/js/ui-popover.js') }}"></script>
 <script>
     $(function() {
         $(".flatpickr-date").flatpickr();

     });
 </script>
 <!-- Main JS -->
 @if ($message = Session::get('success'))
     <script>
         toastr.options.showEasing = 'swing';
         toastr.options.hideEasing = 'linear';
         toastr.options.progressBar = true;
         toastr.success("Berhasil", "{{ $message }}", {
             timeOut: 3000
         });
     </script>
 @endif

 @if ($message = Session::get('error'))
     <script>
         toastr.options.showEasing = 'swing';
         toastr.options.hideEasing = 'linear';
         toastr.options.progressBar = true;
         toastr.error("Gagal", "{{ $message }}", {
             timeOut: 3000
         });
     </script>
 @endif

 @if ($message = Session::get('warning'))
     <script>
         toastr.options.showEasing = 'swing';
         toastr.options.hideEasing = 'linear';
         toastr.options.progressBar = true;
         toastr.warning("Warning", "{{ $message }}", {
             timeOut: 3000
         });
     </script>
 @endif

 @if ($errors->any())
     @php
         $err = '';
     @endphp
     @foreach ($errors->all() as $error)
         @php
             $err .= $error;
         @endphp
     @endforeach
     <script>
         toastr.options.showEasing = 'swing';
         toastr.options.hideEasing = 'linear';
         toastr.options.progressBar = true;
         toastr.error(" Gagal", "{{ $err }}", {
             timeOut: 3000
         });
     </script>
 @endif
 <script>
     $('.delete-confirm').click(function(event) {
         var form = $(this).closest("form");
         var name = $(this).data("name");
         event.preventDefault();
         Swal.fire({
             title: `Apakah Anda Yakin Ingin Menghapus Data Ini ?`,
             text: "Jika dihapus maka data akan hilang permanent.",
             icon: "warning",
             buttons: true,
             dangerMode: true,
             showCancelButton: true,
             confirmButtonColor: "#554bbb",
             cancelButtonColor: "#d33",
             confirmButtonText: "Yes, Hapus Saja!"
         }).then((result) => {
             /* Read more about isConfirmed, isDenied below */
             if (result.isConfirmed) {
                 form.submit();
             }
         });
     });


     $('.cancel-confirm').click(function(event) {
         var form = $(this).closest("form");
         var name = $(this).data("name");
         event.preventDefault();
         Swal.fire({
             title: `Apakah Anda Yakin Ingin Membatalkan Data Ini ?`,
             text: "Data ini akan dibatalkan.",
             icon: "warning",
             buttons: true,
             dangerMode: true,
             showCancelButton: true,
             confirmButtonColor: "#554bbb",
             cancelButtonColor: "#d33",
             confirmButtonText: "Yes, Batalkan Saja Saja!"
         }).then((result) => {
             /* Read more about isConfirmed, isDenied below */
             if (result.isConfirmed) {
                 form.submit();
             }
         });
     });
 </script>

 <script>
     $(".money").maskMoney();
 </script>

 <!-- Global Logout Handler -->
 <script>
     function handleLogoutNav(event) {
         event.preventDefault();
            console.log('handleLogoutNav triggered - Swal available:', typeof Swal !== 'undefined');
            
            // Check if Swal is available
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 not loaded, using confirm() fallback');
                if (confirm("Anda Yakin Ingin Logout?")) {
                    const logoutForm = document.getElementById('logoutForm') || 
                                      document.getElementById('logoutFormNav1') || 
                                      document.getElementById('logoutFormNav2');
                    if (logoutForm) {
                        logoutForm.submit();
                    } else {
                        window.location.href = "{{ route('logout.get') }}";
                    }
                }
                return;
            }
            
            Swal.fire({
                title: "Anda Yakin?",
                text: "Anda akan logout dari sistem.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Logout!",
                cancelButtonText: "Batal"
            }).then((result) => {
                console.log('Swal result:', result);
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Logout Berhasil!",
                        text: "Anda telah keluar dari sistem.",
                        icon: "success",
                        timer: 1500,
                        timerProgressBar: false
                    }).then(() => {
                        console.log('Attempting to submit logout form');
                        // Try to find and submit logout form
                        const logoutForm = document.getElementById('logoutForm') || 
                                          document.getElementById('logoutFormNav1') || 
                                          document.getElementById('logoutFormNav2') ||
                                          document.querySelector('form[method="POST"][action*="logout"]');
                        
                        if (logoutForm) {
                            console.log('Form found, action:', logoutForm.action);
                            logoutForm.submit();
                        } else {
                            console.warn('No logout form found, redirecting directly via GET');
                            // Fallback: direct redirect to logout route
                            window.location.href = "{{ route('logout.get') }}";
                        }
                    });
                }
            }).catch((error) => {
                console.error('SweetAlert error:', error);
                // Emergency fallback
                const logoutForm = document.getElementById('logoutForm');
                if (logoutForm) {
                    logoutForm.submit();
                } else {
                    window.location.href = "{{ route('logout.get') }}";
                }
 <script src="{{ asset('/assets/js/main.js') }}"></script>



 @stack('myscript')

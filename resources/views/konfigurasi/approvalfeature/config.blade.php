<style>
    /* Theme Integration */
    :root {
        --primary-color: #0f766e; /* Dark Green / Teal matching sidebar */
        --primary-bg-subtle: #d1e7dd; /* Light Green */
    }

    .draggable-area {
        min-height: 400px;
        border-radius: 12px;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    /* Left Panel: Available Roles */
    .source-panel {
        background: #fff;
        border: 1px solid #d9dee3;
    }
    
    .source-header {
        background: #f5f5f9;
        padding: 15px;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid #d9dee3;
        font-weight: 600;
        color: #566a7f;
    }

    /* Right Panel: Approval Timeline */
    .target-panel {
        background: #f8f9fa;
        border: 2px dashed #0f766e; /* Theme Green */
    }

    .target-header {
        background: #0f766e; /* Theme Green */
        color: white;
        padding: 15px;
        border-radius: 10px 10px 0 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Role Cards */
    .role-card {
        background: white;
        border: 1px solid #d9dee3;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(67, 89, 113, 0.04);
        cursor: grab;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .role-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 89, 113, 0.08);
        border-color: #0f766e;
    }

    .role-card:active {
        cursor: grabbing;
    }

    .role-icon {
        width: 32px;
        height: 32px;
        background: #d1e7dd; /* Light Green */
        color: #0f766e; /* Theme Green */
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    /* Timeline Styling for Target */
    .timeline-connector {
        position: absolute;
        left: 38px;
        top: 60px;
        bottom: 20px;
        width: 2px;
        background: #d1e7dd;
        z-index: 0;
        display: none; 
    }

    #approvalSteps .role-card {
        border-left: 4px solid #0f766e;
        position: relative;
        z-index: 1;
    }

    #approvalSteps .step-badge {
        background: #0f766e;
        color: white;
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 20px;
        font-weight: 700;
        margin-right: 10px;
        min-width: 70px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(15, 118, 110, 0.4);
    }
    
    /* Dragging States */
    .ui-sortable-ghost {
        background: #d1e7dd !important;
        border: 2px dashed #0f766e !important;
        opacity: 0.8;
    }
    
    .ui-sortable-chosen {
        background: #fff;
    }

    /* Empty State */
    .empty-placeholder {
        text-align: center;
        padding: 40px 20px;
        color: #a1acb8;
    }
    .empty-placeholder i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #d9dee3;
    }

    /* Remove Button */
    .btn-remove {
        color: #ff3e1d;
        padding: 6px;
        border-radius: 6px;
        transition: background 0.2s;
        cursor: pointer;
    }
    .btn-remove:hover {
        background: #ffe0db;
    }

</style>

<form action="{{ route('approvalfeature.updateConfig', $feature->id) }}" method="POST" id="formConfig">
    @csrf
    <div class="row g-4">
        <!-- Available Roles (Source) -->
        <div class="col-md-6">
            <div class="draggable-area source-panel h-100">
                <div class="source-header">
                    <i class="ti ti-users me-2"></i> Daftar Role
                </div>
                <div class="p-3 flex-grow-1" style="background: #fdfdfd;">
                    <div id="availableRoles" class="h-100">
                        @foreach ($roles as $role)
                            <div class="role-card" data-role="{{ $role->name }}">
                                <div class="d-flex align-items-center">
                                    <div class="role-icon">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <span class="fw-medium text-heading">{{ $role->name }}</span>
                                </div>
                                <i class="ti ti-grip-vertical text-muted cursor-move"></i>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Workflow (Target) -->
        <div class="col-md-6">
            <div class="draggable-area target-panel h-100 position-relative">
                <div class="target-header">
                    <span><i class="ti ti-git-fork me-2"></i> Alur Persetujuan</span>
                    <span class="badge bg-white text-primary rounded-pill">{{ count($approvalLayers) }} Tahap</span>
                </div>
                
                <div class="p-4 flex-grow-1 position-relative">
                    <!-- Connector Line (Visual only) -->
                    <div class="timeline-connector" id="timelineLine"></div>

                    <div id="approvalSteps" class="h-100">
                         @if($approvalLayers->isEmpty())
                            <div class="empty-placeholder">
                                <i class="ti ti-drag-drop"></i>
                                <h5>Belum ada Approval Layer</h5>
                                <p class="mb-0 small">Tarik role dari kiri ke sini untuk menambahkan.</p>
                            </div>
                        @endif

                        @foreach ($approvalLayers as $index => $layer)
                            <div class="role-card" data-role="{{ $layer->role_name }}">
                                <div class="d-flex align-items-center">
                                    <div class="step-badge">
                                        LEVEL {{ $index + 1 }}
                                    </div>
                                    <span class="fw-bold text-heading">{{ $layer->role_name }}</span>
                                </div>
                                <div class="btn-remove remove-role">
                                    <i class="ti ti-trash"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer px-0 pb-0 mt-4 border-top pt-3">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="btnSaveConfig">
            <i class="ti ti-check me-2"></i> Simpan Perubahan
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        var availableRoleList = document.getElementById('availableRoles');
        var approvalStepsList = document.getElementById('approvalSteps');

        function updateState() {
            var items = $('#approvalSteps .role-card');
            
            // Update Badges
            items.each(function(index) {
                $(this).find('.step-badge').text('LEVEL ' + (index + 1));
            });

            // Update Header Count
            $('.target-header .badge').text(items.length + ' Tahap');

            // Handle Empty State
            if (items.length === 0) {
                 if($('#approvalSteps .empty-placeholder').length === 0) {
                     $('#approvalSteps').html(`
                        <div class="empty-placeholder">
                            <i class="ti ti-drag-drop"></i>
                            <h5>Belum ada Approval Layer</h5>
                            <p class="mb-0 small">Tarik role dari kiri ke sini untuk menambahkan.</p>
                        </div>
                     `);
                 }
                 $('#timelineLine').hide();
            } else {
                $('#approvalSteps .empty-placeholder').remove();
                $('#timelineLine').show();
            }
        }

        new Sortable(availableRoleList, {
            group: {
                name: 'shared',
                pull: 'clone',
                put: false
            },
            animation: 150,
            sort: false,
            ghostClass: 'ui-sortable-ghost'
        });

        new Sortable(approvalStepsList, {
            group: 'shared',
            animation: 200,
            ghostClass: 'ui-sortable-ghost',
            onAdd: function (evt) {
                var item = evt.item;
                var roleName = $(item).data('role');
                
                // Transform item styling for the target list
                $(item).removeAttr('style'); // Remove inline styles from dragging
                $(item).html(`
                    <div class="d-flex align-items-center">
                        <div class="step-badge">LEVEL #</div>
                        <span class="fw-bold text-heading">${roleName}</span>
                    </div>
                    <div class="btn-remove remove-role">
                        <i class="ti ti-trash"></i>
                    </div>
                `);
                
                updateState();
            },
            onUpdate: function () {
                updateState();
            },
            onRemove: function () {
                 updateState();
            }
        });

        // Event delegation for remove button
        $('#approvalSteps').on('click', '.remove-role', function() {
            $(this).closest('.role-card').slideUp(200, function() {
                $(this).remove();
                updateState();
            });
        });

        // Initialize state
        updateState();

        $('#formConfig').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            
            $(form).find('input[name="roles[]"]').remove();

            $('#approvalSteps .role-card').each(function() {
                var role = $(this).data('role');
                if(role) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'roles[]',
                        value: role
                    }).appendTo(form);
                }
            });

            form.submit();
        });
    });
</script>

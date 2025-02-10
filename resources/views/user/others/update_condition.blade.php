<!-- Modal for updating farm Condition -->
<div class="modal fade" id="updateFarmCondition{{ $farmId }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Condition</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('update.condition') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p><strong>Note:</strong> If your condition is set as <i>"is infected"</i>, then you will be able to hire a technician.</p>
                    <input type="hidden" name="farm_id" value="{{ $farmId }}">
                    <div class="form-check">
                        <input 
                            type="checkbox" 
                            class="form-check-input custom-checkbox" 
                            name="condition" 
                            id="toggleCondition_{{ $farmId }}" 
                            value="{{ $farmCondition === 'is Healthy' ? 'is Infected' : 'is Healthy' }}">
                        <label class="form-check-label" for="toggleCondition_{{ $farmId }}">
                            Mark as <strong>{{ $farmCondition === 'is Healthy' ? 'Infected' : 'Healthy' }}</strong> 
                        </label>
                    </div>
                    <small class="text-muted">
                        Current condition: <strong>{{ $farmCondition }}</strong>
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="update-btn btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                </div>
                <h5 class="modal-title mb-3">Condition updated successfully!</h5>
                <div class="d-flex justify-content-end">
                    @if ($farmCondition === 'is Healthy')
                        <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">Close</button>
                    @else
                        <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">Later</button>
                        <a href="{{ route('user.services') }}" class="btn btn-success mx-2">Hire a Technician now</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <script>
        window.onload = function () {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'), {});
            successModal.show();
        };
    </script>
@endif

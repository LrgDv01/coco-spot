<!-- Add Farm Modal -->
<div class="modal fade" id="addFarmModal" tabindex="-1" aria-labelledby="addFarmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">  <!-- Add modal-dialog-centered here -->
        <div class="modal-content">
            <form id="add-farm-form" method="POST" action="{{ route('farm.store') }}"> 
                <div class="modal-header">
                    <h5 class="modal-title" id="addFarmModalLabel">New Farm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Address</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="district" class="form-label">District</label>
                        <select class="form-select" id="district" name="district" required>
                            <option value="" disabled selected hidden>Select District</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hectares" class="form-label">Hectares</label>
                        <input type="number" class="form-control" id="hectares" name="hectares" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="tree_age" class="form-label">Tree Age</label>
                        <input type="number" class="form-control" id="tree_age" name="tree_age" required>
                    </div>
                    <div class="mb-3">
                        <label for="planted_coconut" class="form-label">Planted Coconut</label>
                        <input type="number" class="form-control" id="planted_coconut" name="planted_coconut" required>
                    </div>
                    <div class="mb-3">
                        <label for="variety" class="form-label">Variety</label>
                        <select class="form-select" id="variety" name="variety" required>
                            <option value="" disabled selected hidden>Select Variety</option>
                            <option value="Laguna Tall">Laguna Tall</option>
                            <option value="Dwarf Coconut">Dwarf Coconut</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary fw-100 rounded rounded-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success fw-100 rounded rounded-3">Add Farm</button>
                </div>
            </form>
        </div>
    </div>
</div>



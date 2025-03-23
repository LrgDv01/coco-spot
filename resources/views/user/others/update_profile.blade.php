<!-- Modal for updating profile -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><strong>Update Profile</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="updateProfileForm">
        @csrf
        <div class="modal-body"> 
          <div class="mb-3">
            <label for="new_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="new_name" name="new_name" placeholder="Enter new name">
          </div>
          <div class="mb-3">
            <label for="new_contact" class="form-label">Contact No.</label>
            <input type="text" class="form-control" id="new_contact" name="new_contact" placeholder="Enter new contact number">
          </div>
          <div class="mb-3">
            <label for="new_email" class="form-label">Email</label>
            <input type="new_email" class="form-control" id="new_email" name="new_email" placeholder="Enter new email">
          </div> 
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

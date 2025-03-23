@include('user.partials.__header')
@include('user.partials.__nav')
<main id="main" class="main">
    <section class="section settings">
        <div class="container">
            <h4 class="mb-4"><strong>About Me</strong></h4>
            <div class="list-group">
                @php
                    $prof_img = $user->profile_img ? URL::asset('storage/' . $user->profile_img) 
                                : ($user->user_type === 0 ? URL::asset('img/admin.png') 
                                : ($user->user_type === '1' ? URL::asset('img/farmer.png') 
                                : URL::asset('img/technician.png'))) ;
                @endphp
                <!-- Profile Image -->
                <div class="list-group-item d-flex flex-row justify-content-center align-items-center">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ $prof_img }}" alt="Logo" class="img-fluid rounded-circle my-3" style="width: 100px; height: 100px;">
                        <button class="btn btn-outline-success btn-sm mb-2" onclick="editProfileImage()">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </div>
                </div>
                <!-- Username -->
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-person-circle me-2"></i>
                        <strong id="user_name">{{ $user->user_name }}</strong>
                        <div class="text-muted">Username</div>
                    </div>
                    <button class="btn btn-outline-success btn-sm" onclick="editField('user_name', '{{ $user->user_name }}')">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

            {{--    <!-- Age -->
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-calendar me-2"></i>
                        <strong id="age">{{ $user->age }}</strong>
                        <div class="text-muted">Age</div>
                    </div>
                    <button class="btn btn-outline-success btn-sm" onclick="editField('age', '{{ $user->age }}')">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                <!-- Gender -->
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-gender-female me-2"></i>
                        <strong id="gender">{{ $user->gender }}</strong>
                        <div class="text-muted">Gender</div>
                    </div>
                    <button class="btn btn-outline-success btn-sm" onclick="editField('gender', '{{ $user->gender }}')">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
                --}}
                <!-- Contact Number -->
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-telephone me-2"></i>
                        <strong id="contact">{{ $user->contact }}</strong>
                        <div class="text-muted">Contact No.</div>
                    </div>
                    <button class="btn btn-outline-success btn-sm" onclick="editField('contact', '{{ $user->contact}}')">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                <!-- Email -->
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-envelope me-2"></i>
                        <strong id="email">{{ $user->email }}</strong>
                        <div class="text-muted">E-mail</div>
                    </div>
                    <button class="btn btn-outline-success btn-sm" onclick="editField('email', '{{ $user->email }}')">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                <!-- Municipality -->
             {{--   <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-building me-2"></i>
                        <strong id="municipality">{{ $user->municipality }}</strong>
                        <div class="text-muted">Municipality</div>
                    </div>
                    <button class="btn btn-outline-success btn-sm" onclick="editField('municipality', '{{ $user->municipality }}')">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                <!-- District -->
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-geo-alt me-2"></i>
                        <strong id="district">{{ $user->district }}</strong>
                        <div class="text-muted">District</div>
                    </div>
                    <button class="btn btn-outline-success btn-sm" onclick="editField('district', '{{ $user->district }}')">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>--}}
            </div>

            <div class="mt-3">
                <button class="btn btn-success w-100" onclick="updateProfile()">Edit Profile</button>
            </div>
        </div>

        <!-- Modal for Editing -->
        <div class="modal fade" id="editFieldModal" tabindex="-1" aria-labelledby="editFieldModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFieldModalLabel"><strong>Edit Field</strong></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editFieldForm">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="fieldName">
                            <div class="mb-3">
                                <label for="fieldValue" class="form-label">Value</label>
                                <input type="text" id="fieldValue" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @include('user.others.update_profile')

        <!-- Modal for Editing Profile Image -->
        <div class="modal fade" id="editProfileImageModal" tabindex="-1" aria-labelledby="editProfileImageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileImageModalLabel"><strong>Edit Profile Image</strong></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editProfileImageForm" method="POST" action="{{ route('user.update-profile-image') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="profileImage" class="form-label"><strong>Upload New Profile Image</strong></label>
                                <input type="file" id="profileImage" name="profile_image" class="form-control" accept="image/*" required onchange="previewImage(event)">
                            </div>
                            <div class="mb-3 d-flex flex-column align-items-center justify-content-center">
                                <img id="profileImagePreview" src="#" alt="Profile Image Preview" class="img-fluid rounded-circle" style="display: none; width: 100px; height: 100px;">
                                <label id="preview-label" class="mt-2" for="profileImagePreview"><u>Preview</u></label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                Save
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
</main>
@include('user.partials.__footer')

<script>

    function editProfileImage() {
        const editProfileImageModal = new bootstrap.Modal(document.getElementById('editProfileImageModal'));
        editProfileImageModal.show();
    }
    const preview = document.getElementById('preview-label');
    preview.style.display = 'none';
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profileImagePreview');
            preview.style.display = 'block';
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function editField(field, value) {
        document.getElementById('fieldName').value = field;
        document.getElementById('fieldValue').value = value;
        const editModal = new bootstrap.Modal(document.getElementById('editFieldModal'));
        editModal.show();
    }
    function updateProfile() {
        const editModal = new bootstrap.Modal(document.getElementById('updateProfileModal'));
        editModal.show();
    }
</script>
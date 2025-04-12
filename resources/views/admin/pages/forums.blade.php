@include('admin.partials.__header')
@include('admin.partials.__nav')

<main id="main" class="main">
    <div class="pagetitle">
        <h1><Strong>Forums</Strong></h1>
    </div>
    <section class="section forums m-3 mt-4">
        @php
            $prof_img = Auth::user()->profile_img ? URL::asset('storage/' . Auth::user()->profile_img) 
                        : URL::asset('img/admin.png');
        @endphp
        <h3 class="fw-bold text-muted text-center">Create Topics</h3>
        <div class="card p-3 text-white" style="background-color: #1a4d2e;">
            <div class="d-flex flex-row">
                <img src="{{ $prof_img }}" alt="Logo" class="img-fluid rounded-circle" style="width: 50px; height: 50px;">
                <div class="ms-2">
                    <h4 class="p-0 m-0 fw-bold"> {{ $username }} </h4>
                    <p> {{ $usertype }} </p>
                </div>
            </div>
            <form method="POST" action="{{ route('forum.storePost') }}" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <textarea name="caption" class="form-control w-100" rows="3" placeholder="Write some thoughts . ." required></textarea>
                    <div class="d-flex flex-row mt-2">
                            <img id="image_preview" src="#" alt="Image Preview" class="img-fluid rounded me-2" style="display: none; width: 50px; height: auto;">
                            <label id="default_img" class="input-group-text me-2" for="image_proof"><i class="bi bi-image"></i></label>
                        <input type="file" class="form-control w-75" id="image_proof" name="image_proof" accept="image/*" onchange="previewImage(event)">
                    </div>
                </div>
                <div class="d-flex flex-row justify-content-end">
                    <button type="submit" class="btn btn-outline-success border-white m-2 fw-bold text-white"><i class="bi bi-send-fill"></i> Post</button>
                </div>
            </form>
        </div>
        <div class="d-flex flex-row justify-content-between my-3">
            <hr class="w-50 me-3"> <h5 class="text-muted text-center fw-bold w-25"><i class="bi bi-people mx-2"></i>Discussions</h5> <hr class="w-50 ms-3">
        </div>
        <div>
            <ul class="list-unstyled">
                @foreach ($posts as $post)
                    @php
                        $post_prof_img = $post->user->profile_img  ? asset('storage/' . $post->user->profile_img) 
                                        : ($post->user->user_type === '0' ? URL::asset('img/admin.png') 
                                        : ($post->user->user_type === '1' ? URL::asset('img/farmer.png') 
                                        : URL::asset('img/technician.png')));
                    @endphp  
                    <li>
                        <div class="card p-3 text-white" style="background-color: #1a4d2e;">
                            <div class="d-flex flex-row">
                                <img src="{{ $post_prof_img }}" alt="Logo" class="img-fluid rounded-circle" style="width: 50px; height: 50px;">
                                <div class="ms-2">
                                    @php
                                        $userTypePost = $post->user->user_type === '0' ? 'Administrator' 
                                                        : ($post->user->user_type === '1' ? 'Farmer' : 'Technician');  
                                    @endphp
                                    <h4 class="p-0 m-0 fw-bold"> {{ $post->user->full_name }}</h4> 
                                    <p>{{ $userTypePost }}</p> 
                                </div>
                            </div>
                            <div class="mx-2 my-0">
                                @if ($post->image_path)
                                    <img class="align-self-center p-1 rounded rounded-3 border border-1 border-secondary"
                                        src="{{ URL::asset('storage/' . $post->image_path) }}" width="100%" height="auto" alt="post-image"> 
                                  {{--    src="{{ asset('storage/' . $post->image_path) }}" width="100%" height="auto" alt="post-image">  --}}
                                    
                                @endif
                                <div class="px-3 m-0 py-3">
                                    <p class="text-white"><strong>`{{ $post->caption }}.</strong> </p>
                                </div>
                            </div>
                            <div class="d-flex flex-row justify-content-between mx-3 my-0 py-0">
                                <div class="d-flex flex-row">
                                    <form method="POST" action="{{ route('forum.react', $post->id) }}" class="react-form" data-post-id="{{ $post->id }}">
                                        @csrf
                                        <input type="hidden" name="type" value="like">
                                        <button type="submit" class="btn {{ $post->userReaction() === 'like' ? 'btn-success' : 'btn-outline-success' }} me-2 text-white border-white like-button" data-post-id="{{ $post->id }}">
                                            <i class="bi bi-hand-thumbs-up-fill"></i> <span id="like-count-{{ $post->id }}">{{ $post->likesCount() }}</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('forum.react', $post->id) }}" class="react-form" data-post-id="{{ $post->id }}">
                                        @csrf
                                        <input type="hidden" name="type" value="dislike">
                                        <button type="submit" class="btn {{ $post->userReaction() === 'dislike' ? 'btn-secondary' : 'btn-outline-secondary' }} me-2 text-white border-white dislike-button" data-post-id="{{ $post->id }}">
                                            <i class="bi bi-hand-thumbs-down-fill"></i> <span id="dislike-count-{{ $post->id }}">{{ $post->dislikesCount() }}</span>
                                        </button>
                                    </form>
                                    <button class="btn btn-outline-primary text-white border-white" onclick="toggleComments({{ $post->id }})">
                                        <i class="bi bi-chat-fill"></i> <span id="comment-count-{{ $post->id }}">{{ $post->comments->count() }}</span> | Comment
                                    </button>
                                </div>
                                <div class="d-flex flex-row">
                                    @if ($post->user_id == Auth::id())
                                        <button class="btn btn-outline-primary btn-md me-2 text-white border-white" data-bs-toggle="modal" data-bs-target="#editPostModal{{ $post->id }}">
                                            <i class="bi bi-pencil-fill"></i></button>
                                        <button type="submit" class="btn btn-outline-danger btn-md text-white border-white" data-bs-toggle="modal" data-bs-target="#deletePostModal{{ $post->id }}">
                                            <i class="bi bi-trash-fill"></i></button>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex flex-row m-3 mt-4">
                                <img src="{{ $prof_img }}" alt="Logo" class="img-fluid rounded-circle" style="width: 50px; height: 50px;">
                                <form method="POST" action="{{ route('forum.storeComment', $post->id) }}" class="comment-form w-100" data-post-id="{{ $post->id }}">
                                    @csrf
                                    <textarea name="text" class="form-control ms-2" rows="3" placeholder="Write a comment . ." required></textarea>
                                    <div class="d-flex flex-row justify-content-end mt-1">
                                        <button type="submit" class="btn btn-outline-success ms-2 text-white border-white"><i class="bi bi-send-fill"></i></button>
                                    </div>
                                </form>
                            </div>
                            <hr class="p-0 m-0">
                            <div id="comments-{{ $post->id }}" class="m-3" style="display: none;">
                                <ul class="list-unstyled pe-3" style="max-height: 300px; overflow-y: auto;">
                                    @foreach ($post->comments as $comment)
                                        <li class="my-2" id="comment-{{ $comment->id }}">
                                            @php
                                                $comment_list_prof_img = $comment->user->profile_img ? URL::asset('storage/' . $comment->user->profile_img) 
                                                            : ($comment->user->user_type === '0' ? URL::asset('img/admin.png') 
                                                            : ($comment->user->user_type === '1' ? URL::asset('img/farmer.png') 
                                                            : URL::asset('img/technician.png')));
                                            @endphp
                                            <div class="p-2 d-flex flex-row align-items-center">
                                                <img src="{{ $comment_list_prof_img }}" alt="Logo" class="img-fluid rounded-circle align-self-start" style="width: 40px; height: 40px;">
                                                <div class="d-flex flex-column justify-content-end mx-3 w-100">
                                                    <p class="text-bottom my-0 fw-bold">{{ $comment->user->full_name }}</p>
                                                    <p class="text-bottom my-0">{{ $comment->text }}</p>
                                                </div>
                                                <!-- Added -->
                                                @if ($comment->user_id == Auth::id())
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-secondary btn-sm text-white border-white" type="button" id="dropdownMenuButton{{ $comment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu p-0 m-0" aria-labelledby="dropdownMenuButton{{ $comment->id }}">
                                                            <li>
                                                                <button class="dropdown-item fs-6 fw-bold text-muted" data-bs-toggle="modal" data-bs-target="#editCommentModal{{ $comment->id }}">
                                                                <i class="bi bi-pencil-fill"></i> Edit
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item fs-6 fw-bold text-muted" data-bs-toggle="modal" data-bs-target="#deleteCommentModal{{ $comment->id }}">
                                                                    <i class="bi bi-trash-fill"></i> Delete
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                        <!-- for editing comment -->
                                        <div class="modal fade text-black" id="editCommentModal{{ $comment->id }}" tabindex="-1" aria-labelledby="editCommentModalLabel{{ $comment->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold" id="editCommentModalLabel{{ $comment->id }}">Edit Comment</h5>
                                                        <button type="button" class="btn-close fs-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST" action="{{ route('forum.updateComment', $comment->id) }}" class="edit-comment-form" data-post-id="{{ $post->id }}" data-comment-id="{{ $comment->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="text{{ $comment->id }}" class="form-label fw-bold">Comment</label>
                                                                <textarea name="text" id="text{{ $comment->id }}" class="form-control" rows="3" required>{{ $comment->text }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- for deleting comment -->
                                        <div class="modal fade text-black" id="deleteCommentModal{{ $comment->id }}" tabindex="-1" aria-labelledby="deleteCommentModalLabel{{ $comment->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold" id="deleteCommentModalLabel{{ $comment->id }}">Confirm Deletion</h5>
                                                        <button type="button" class="btn-close fs-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this comment?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form method="POST" action="{{ route('forum.deleteComment', $comment->id) }}" class="delete-comment-form" data-post-id="{{ $post->id }}" data-comment-id="{{ $comment->id }}" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </li>
                    <!-- for editing post -->
                    <div class="modal fade" id="editPostModal{{ $post->id }}" tabindex="-1" aria-labelledby="editPostModalLabel{{ $post->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="editPostModalLabel{{ $post->id }}">Edit Post</h5>
                                    <button type="button" class="btn-close fs-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('forum.updatePost', $post->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="caption{{ $post->id }}" class="form-label fw-bold">Caption</label>
                                            <textarea name="caption" id="caption{{ $post->id }}" class="form-control" rows="3" required>{{ $post->caption }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image_proof{{ $post->id }}" class="form-label fw-bold">Update Image</label>
                                            <input type="file" class="form-control" id="image_proof{{ $post->id }}" name="image_proof" accept="image/*">
                                        </div>
                                        @if ($post->image_path)
                                            <div class="mb-3">
                                                <img src="{{ URL::asset('storage/' . $post->image_path) }}" alt="Current Image" class="img-fluid">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- for deleting post -->
                    <div class="modal fade" id="deletePostModal{{ $post->id }}" tabindex="-1" aria-labelledby="deletePostModalLabel{{ $post->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="deletePostModalLabel{{ $post->id }}">Confirm Deletion</h5>
                                    <button type="button" class="btn-close fs-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete this post ? This action cannot be undone.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form method="POST" action="{{ route('forum.deletePost', $post->id) }}" id="deletePostForm{{ $post->id }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </ul>
        </div>
    </section>
</main>

@include('admin.partials.__footer')

<script>

    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('image_preview');
            var dflt  =  document.getElementById('default_img');
            output.src = reader.result;
            output.style.display = 'block';
            dflt.style.display = 'none';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function toggleComments(postId) {
        var commentsDiv = document.getElementById('comments-' + postId);
        commentsDiv.style.display = commentsDiv.style.display === 'none' ? 'block' : 'none';
    }

    $(document).ready(function() {
        // Define CSRF token and current user ID globally
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var currentUserId = {{ Auth::id() }};

        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        // Handle like and dislike submissions (unchanged)
        $('form.react-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        var postId = form.data('post-id');
                        $('#like-count-' + postId).text(response.likes);
                        $('#dislike-count-' + postId).text(response.dislikes);

                        // Update button classes
                        var likeButton = $('.like-button[data-post-id="' + postId + '"]');
                        var dislikeButton = $('.dislike-button[data-post-id="' + postId + '"]');

                        // Reset to outline styles
                        likeButton.removeClass('btn-success').addClass('btn-outline-success');
                        dislikeButton.removeClass('btn-secondary').addClass('btn-outline-secondary');

                        // Apply filled style based on reaction
                        if (response.userReaction === 'like') {
                            likeButton.removeClass('btn-outline-success').addClass('btn-success');
                        } else if (response.userReaction === 'dislike') {
                            dislikeButton.removeClass('btn-outline-secondary').addClass('btn-secondary');
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        });

        // Handle comment form submission
        $('.comment-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var postId = $form.data('post-id');
            var $textarea = $form.find('textarea');
            $.ajax({
                type: 'POST',
                url: $form.attr('action'),
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Construct new comment HTML with modals
                        // if (postId) {
                            var commentHtml = `
                                <li class="my-2" id="comment-${response.comment.id}">
                                    <div class="p-2 d-flex flex-row align-items-center">
                                        <img src="${response.prof_img}" alt="User" class="img-fluid rounded-circle" style="width: 40px; height: 40px;">
                                        <div class="d-flex flex-column justify-content-end mx-3 w-100">
                                            <p class="text-bottom my-0 fw-bold">${response.comment.user.full_name}</p>
                                            <p class="text-bottom my-0">${response.comment.text}</p>
                                        </div>
                                        ${response.comment.user_id == currentUserId ? `
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm text-white border-white" type="button" id="dropdownMenuButton${response.comment.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu p-0 m-0" aria-labelledby="dropdownMenuButton${response.comment.id}">
                                                    <li>
                                                        <button class="dropdown-item fs-6 fw-bold text-muted" data-bs-toggle="modal" data-bs-target="#editCommentModal${response.comment.id}">
                                                        <i class="bi bi-pencil-fill"></i> Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item fs-6 fw-bold text-muted" data-bs-toggle="modal" data-bs-target="#deleteCommentModal${response.comment.id}">
                                                            <i class="bi bi-trash-fill"></i> Delete
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        ` : ''}
                                    </div>
                                    <!-- Edit Modal -->
                                    <div class="modal fade text-black" id="editCommentModal${response.comment.id}" tabindex="-1" aria-labelledby="editCommentModalLabel${response.comment.id}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold" id="editCommentModalLabel${response.comment.id}">Edit Comment</h5>
                                                    <button type="button" class="btn-close fs-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form class="edit-comment-form" data-post-id="${ postId }" data-comment-id="${response.comment.id}" action="/forum/comment/${response.comment.id}">
                                                    <input type="hidden" name="_token" value="${csrfToken}">
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="text${response.comment.id}" class="form-label fw-bold">Comment</label>
                                                            <textarea name="text" id="text${response.comment.id}" class="form-control" rows="3" required>${response.comment.text}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Delete Modal -->
                                    <div class="modal fade text-black" id="deleteCommentModal${response.comment.id}" tabindex="-1" aria-labelledby="deleteCommentModalLabel${response.comment.id}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold" id="deleteCommentModalLabel${response.comment.id}">Confirm Deletion</h5>
                                                    <button type="button" class="btn-close fs-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this comment?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form class="delete-comment-form" data-post-id="${ postId }" data-comment-id="${response.comment.id}" action="/forum/comment/${response.comment.id}" style="display:inline;">
                                                        <input type="hidden" name="_token" value="${csrfToken}">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            `;
                            $('#comments-' + postId + ' ul').prepend(commentHtml);
                            // $('#comments-' + postId + ' ul').append(commentHtml);
                            $('#comments-' + postId).show();
                            $textarea.val('');
                            var $commentCount = $('#comment-count-' + postId);
                            if ($commentCount.length) {
                                $commentCount.text(parseInt($commentCount.text()) + 1);
                            }

                        // }
           
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Failed to add comment. Please try again.');
                }
            });
        });
        // Handle edit comment form submission
        $(document).on('submit', '.edit-comment-form', function(e) {
            e.preventDefault(); // Stop default form submission
            var $form = $(this);
            var commentId = $form.data('comment-id');
            $.ajax({
                type: 'POST', // Laravel interprets as PUT due to _method
                url: $form.attr('action'),
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var modal = $('#editCommentModal' + commentId);
                        modal.on('hidden.bs.modal', function () {
                            // Update comment text in the DOM
                            $('#comment-' + commentId + ' p.text-bottom').text(response.comment.text);
                            // Close the modal
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css('overflow', '');
                        });
                        modal.modal('hide');
                    }
                },
                error: function(xhr) {
                    console.error('Error updating comment:', xhr.responseText);
                    alert('Failed to update comment. Please try again.');
                }
            });
        });

        // Handle delete comment form submission
        $(document).on('submit', '.delete-comment-form', function(e) {
            e.preventDefault(); // Stop default form submission
            var $form = $(this);
            var postId = $form.data('post-id');
            var commentId = $form.data('comment-id');
            $.ajax({
                type: 'POST', // Laravel interprets as DELETE due to _method
                url: $form.attr('action'),
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var modal = $('#deleteCommentModal' + commentId);
                        modal.on('hidden.bs.modal', function () {
                            $('#comment-' + commentId).remove();
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css('overflow', '');
                            var $commentCount = $('#comment-count-' + postId);
                            if ($commentCount.length) {
                                $commentCount.text(parseInt($commentCount.text()) - 1);
                            }
                        });
                        modal.modal('hide');
                    }
                },
                error: function(xhr) {
                    console.error('Error deleting comment:', xhr.responseText);
                    alert('Failed to delete comment. Please try again.');
                }
            });
        });
    });
</script>

<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure users are logged in
    }

    public function index()
    {
        $user = Auth::user();
        $username = $user->full_name; 
        $usertype = $user->user_type === '0' ? 'Administrator' 
                    : ($user->user_type === '1' ? 'Farmer' : 'Technician'); 
        $posts = Post::with(['user', 'comments.user', 'reactions'])->latest()->get();
        // $userImg = User::where('id', $posts->user_id)->pluck('profile_img')->toArray();
        // dd($posts->user->profile_img);
        return view($user->user_type === '0' ? 'admin.pages.forums' : 'user.forums', compact('user', 'username', 'usertype', 'posts'));
                    // ? 'user.pages.forums' : 'user.technician.pagesforums' ), compact('user', 'username', 'usertype', 'posts'));
    }

    // public function storePost(Request $request)
    // {
    //     $request->validate([
    //         'caption' => 'required|string',
    //         'image_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);
    //     $post = new Post();
    //     $post->user_id = Auth::id();
    //     $post->caption = $request->caption;

    //     if ($request->hasFile('image_proof')) {
    //         $path = $request->file('image_proof')->store('posts', 'public');
    //         $post->image_path = $path;
    //     }
    //     $post->save();

    //     return redirect()->route('admin.forums')->with('success', 'Post created successfully.');
    // }



    public function storePost(Request $request)
    {
        $request->validate([
            'caption' => 'required|string',
            // 'image_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|min:0', 
            'image_proof' => 'image|mimes:jpeg,png,jpg,gif,svg|min:0', 
        ]);

        $post = new Post();
        $post->user_id = Auth::id();
        $post->caption = $request->caption;

        if ($request->hasFile('image_proof')) {
            // Store in storage/app/public/posts
            $path = $request->file('image_proof')->store('posts', 'public');
            $post->image_path = $path;

            // Manually copy to public/storage/posts
            $filename = basename($path); // Extracts 'filename.ext' from full path
            $storagePath = storage_path('app/public/posts/' . $filename);
            $publicPath = public_path('storage/posts/' . $filename);

            // Ensure the directory exists
            if (!File::exists(dirname($publicPath))) {
                File::makeDirectory(dirname($publicPath), 0755, true);
            }

            File::copy($storagePath, $publicPath);
        }

        $post->save();
        return redirect()->route('admin.forums')->with('success', 'Post created successfully.');
    }

    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'text' => 'required|string',
        ]);
        $comment = new Comment();
        $comment->post_id = $postId;
        $comment->user_id = Auth::id();
        $comment->text = $request->text;
        $comment->save();
        // Load user for profile image
        $comment->load('user');
        // $prof_img = $comment->user->profile_img ? asset('storage/' . $comment->user->profile_img) : asset('img/default.png');
        $prof_img = $comment->user->profile_img ? asset('storage/' . $comment->user->profile_img) 
                    : ($comment->user->user_type === '0' ? asset('img/admin.png') 
                    : ($comment->user->user_type === '1' ? asset('img/farmer.png') 
                    : asset('img/technician.png')));
        return response()->json([
            'success' => true,
            'comment' => $comment,
            'prof_img' => $prof_img,
        ]);
    }
   
    public function react(Request $request, $postId)
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        $userId = Auth::id();
        $reaction = Reaction::where('post_id', $postId)->where('user_id', $userId)->first();

        if ($reaction) {
            if ($reaction->type === $request->type) {
                $reaction->delete();
            } else {
                $reaction->type = $request->type;
                $reaction->save();
            }
        } else {
            Reaction::create([
                'post_id' => $postId,
                'user_id' => $userId,
                'type' => $request->type,
            ]);
        }

        $post = Post::findOrFail($postId);
        $userReaction = Reaction::where('post_id', $postId)
            ->where('user_id', $userId)
            ->value('type');

        return response()->json([
            'success' => true,
            'likes' => $post->likesCount(),
            'dislikes' => $post->dislikesCount(),
            'userReaction' => $userReaction,
        ]);
    }

    public function updatePost(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        // Check if the user owns the post
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('admin.forums')->with('error', 'You are not authorized to edit this post.');
        }

        // Validate the request
        $request->validate([
            'caption' => 'required|string',
            'image_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update caption
        $post->caption = $request->caption;

        // Handle image update if provided
        if ($request->hasFile('image_proof')) {
            // Delete old image from both locations
            if ($post->image_path) {
                // Delete from storage/app/public/posts
                Storage::disk('public')->delete($post->image_path);
                
                // Delete from public/storage/posts
                $oldFilename = basename($post->image_path);
                $oldPublicPath = public_path('storage/posts/' . $oldFilename);
                if (File::exists($oldPublicPath)) {
                    File::delete($oldPublicPath);
                }
            }

            // Store new image in storage/app/public/posts
            $path = $request->file('image_proof')->store('posts', 'public');
            $post->image_path = $path;

            // Manually copy to public/storage/posts
            $filename = basename($path);
            $storagePath = storage_path('app/public/posts/' . $filename);
            $publicPath = public_path('storage/posts/' . $filename);

            // Ensure the directory exists
            if (!File::exists(dirname($publicPath))) {
                File::makeDirectory(dirname($publicPath), 0755, true);
            }

            File::copy($storagePath, $publicPath);
        }

        $post->save();

        return redirect()->route('admin.forums')->with('success', 'Post updated successfully.');
    }
    
    // public function updatePost(Request $request, $postId)
    // {
    //     $post = Post::findOrFail($postId);

    //     // Check if the user owns the post
    //     if ($post->user_id !== Auth::id()) {
    //         return redirect()->route('admin.forums')->with('error', 'You are not authorized to edit this post.');
    //     }

    //     // Validate the request
    //     $request->validate([
    //         'caption' => 'required|string',
    //         'image_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     // Update caption
    //     $post->caption = $request->caption;

    //     // Handle image update if provided
    //     if ($request->hasFile('image_proof')) {
    //         if ($post->image_path) {
    //             Storage::disk('public')->delete($post->image_path); // Delete old image
    //         }
    //         $path = $request->file('image_proof')->store('posts', 'public');
    //         $post->image_path = $path;
    //     }

    //     $post->save();

    //     return redirect()->route('admin.forums')->with('success', 'Post updated successfully.');
    // }

    public function deletePost($postId)
    {
        $post = Post::findOrFail($postId);
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('admin.forums')->with('error', 'You are not authorized to delete this post.');
        }
        $post->delete();
        return redirect()->route('admin.forums')->with('success', 'Post deleted successfully.');
    }

    public function updateComment(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $request->validate(['text' => 'required|string']);
        $comment->text = $request->text;
        $comment->save();
        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $comment->delete();
        return response()->json(['success' => true]);
    }
}

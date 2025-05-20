<div>
    <div class="comments-container">
        <div class="comments-header">
            <h3>Comments</h3>
        </div>

        <div class="comments-list">
            @if($comments->isEmpty())
                <div class="no-comments">No comments yet. Be the first to comment!</div>
            @else
                @foreach($comments as $comment)
                    <div class="comment" wire:key="comment-{{ $comment->id }}">
                        <img src="{{ $comment->user->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile"
                            class="comment-avatar">
                        <div class="comment-content">
                            <span class="comment-user">{{ $comment->user->username ?? $comment->user->name }}</span>
                            @if($editingCommentId === $comment->id)
                                <form wire:submit.prevent="updateComment" class="edit-comment-form">
                                    <input type="text" wire:model.defer="editCommentContent" class="comment-input" required>
                                    <button type="submit" class="comment-submit"><i class="fas fa-check"></i></button>
                                    <button type="button" class="comment-submit" wire:click="cancelEdit"><i
                                            class="fas fa-times"></i></button>
                                </form>
                            @else
                                <p class="comment-body">{{ $comment->content }}</p>
                            @endif
                            <div class="comment-actions">
                                <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                @if(Auth::id() === $comment->user_id)
                                    <span class="comment-action" wire:click="startEdit({{ $comment->id }})">Edit</span>
                                @endif
                                <span class="comment-action" wire:click="startReply({{ $comment->id }})">Reply</span>
                            </div>
                            @if($replyingToCommentId === $comment->id)
                                <form wire:submit.prevent="addReply" class="reply-form">
                                    <input type="text" wire:model.defer="replyContent" class="comment-input" required
                                        placeholder="Write a reply...">
                                    <button type="submit" class="comment-submit"><i class="fas fa-paper-plane"></i></button>
                                    <button type="button" class="comment-submit" wire:click="cancelReply"><i
                                            class="fas fa-times"></i></button>
                                </form>
                            @endif
                            @if($comment->replies && $comment->replies->count())
                                <div class="comment-replies">
                                    @foreach($comment->replies as $reply)
                                        <div class="comment" wire:key="reply-{{ $reply->id }}">
                                            <img src="{{ $reply->user->profile_picture ?? asset('images/default-profile.png') }}"
                                                alt="Profile" class="comment-avatar">
                                            <div class="comment-content">
                                                <span class="comment-user">{{ $reply->user->username ?? $reply->user->name }}</span>
                                                <p class="comment-body">{{ $reply->content }}</p>
                                                <div class="comment-actions">
                                                    <span class="comment-time">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <form wire:submit.prevent="addComment" class="comment-form">
            @csrf
            <img src="{{ Auth::user()->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile"
                class="comment-avatar">
            <input type="text" wire:model="newComment" class="comment-input" placeholder="Write a comment..." required>
            <button type="submit" class="comment-submit">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

    <style>
        .comments-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            max-height: calc(80vh - 40px);
        }

        .comments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }

        .comments-header h3 {
            margin: 0;
            font-size: 1.2em;
            color: #2d5a41;
        }

        .comments-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
            margin-bottom: 15px;
        }

        .comment {
            display: flex;
            gap: 10px;
            padding: 8px 0;
        }

        .comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-content {
            flex: 1;
            background: #f0f2f5;
            border-radius: 18px;
            padding: 8px 12px;
        }

        .comment-user {
            font-weight: 600;
            color: #2d5a41;
            font-size: 0.9em;
        }

        .comment-body {
            margin: 4px 0 0;
            color: #050505;
            font-size: 0.9em;
        }

        .comment-actions {
            display: flex;
            gap: 12px;
            margin-top: 4px;
            padding-left: 12px;
        }

        .comment-time {
            font-size: 0.75em;
            color: #65676b;
        }

        .comment-form {
            display: flex;
            gap: 8px;
            align-items: center;
            padding: 10px 0;
            border-top: 1px solid #eee;
        }

        .comment-input {
            flex: 1;
            border: none;
            background: #f0f2f5;
            border-radius: 20px;
            padding: 8px 12px;
            font-size: 0.9em;
        }

        .comment-input:focus {
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 2px rgba(63, 125, 88, 0.1);
        }

        .comment-submit {
            background: none;
            border: none;
            color: #3F7D58;
            cursor: pointer;
            padding: 8px;
            transition: all 0.2s ease;
        }

        .comment-submit:hover {
            color: #2d5a41;
            transform: scale(1.1);
        }

        .comment-submit:disabled {
            color: #bec3c9;
            cursor: not-allowed;
        }

        .no-comments {
            text-align: center;
            color: #65676b;
            padding: 20px;
            font-style: italic;
        }
    </style>
</div>
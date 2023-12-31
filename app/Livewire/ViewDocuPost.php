<?php

namespace App\Livewire;

use Livewire\Attributes\Js;
use App\Models\BookmarkList;
use App\Models\ReportedComment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\DocuPost;
use App\Models\DocuPostComment;
use App\Models\PdfKey;
use App\Models\User;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ViewDocuPost extends Component
{
    public $citation;


    public $parameter, $data, $authenticatedUser;

    public function mount($reference)
    {

        $this->parameter = $reference;
        $this->authenticatedUser = auth()->user();
        $this->data = DocuPost::where('reference', $this->parameter)->first();
        $this->citation = $this->data->recommended_citation;
    }


    public function booted()
    {
        $this->data->update(['view_count' => \DB::raw('COALESCE(view_count, 0) + 1')]);
    }
    public function citeMe()
    {
        $this->dispatch('open-shr');
        $this->data->increment('citation_count');
    }
    public $isBookmarked;

    public function checkBookmark()
    {
        $user = auth()->id();

        // Check if a record with the specified conditions exists
        $checkReference = BookmarkList::where('reference', $this->parameter)
            ->where('user_id', $user)
            ->where('docu_post_id', $this->data->id)
            ->exists(); // Use exists() with parentheses

        if ($checkReference) {
            $this->isBookmarked = true;
        } else {
            $this->isBookmarked = false;
        }
    }

    public function deleteComment($id)
    {
        $isDeleted = DocuPostComment::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->delete();

        if (!$isDeleted) {
            request()->session()->flash('error', 'Deleting comment failed, contact developer!');
        }
    }

    public $editingCommentId = null;
    public $editedComment = '';

    public function editComment($commentId)
    {
        $this->editingCommentId = $commentId;
        $this->editedComment = $this->findComment($commentId)->comment_content;
    }

    public function updateComment($commentId)
    {
        $this->validate([
            'editedComment' => 'required',
        ], [
            'editedComment.required' => 'You cannot update an empty comment.'
        ]);

        $comment = $this->findComment($commentId);
        $comment->comment_content = $this->editedComment;
        $comment->save();

        $this->cancelEditing();
    }

    protected function findComment($commentId)
    {
        return DocuPostComment::find($commentId);
    }

    public function cancelEditing()
    {
        $this->editingCommentId = null;
        $this->editedComment = '';
    }

    public function toggleBookmark()
    {
        if (!auth()->check()) {
            request()->session()->flash('message', 'You need to sign in first');
        } else {
            $checkInfoData = empty($this->authenticatedUser->last_name && $this->authenticatedUser->first_name && $this->authenticatedUser->last_name &&
                $this->authenticatedUser->address &&
                $this->authenticatedUser->phone_no &&
                $this->authenticatedUser->student_id &&
                $this->authenticatedUser->bachelor_degree);

            if ($checkInfoData) {
                request()->session()->flash('message', 'Account information details are incomplete, fill out now here.');
            } else {
                if ($this->authenticatedUser->is_verified == 0) {
                    request()->session()->flash('message', 'Verify your account now to enjoy the full features for free.');
                } else {
                    $this->isBookmarked = !$this->isBookmarked;

                    if ($this->isBookmarked) {
                        BookmarkList::create([
                            'user_id' => auth()->id(),
                            'docu_post_id' => $this->data->id,
                            'reference' => $this->parameter,
                        ]);
                    } else {
                        BookmarkList::where('user_id', auth()->id())
                            ->where('docu_post_id', $this->data->id)
                            ->where('reference', $this->parameter)
                            ->delete();
                    }
                }
            }
        }
    }

    #[Rule('required|min:1')]
    public $comment = '';

    public function createDocuPostComment()
    {
        $this->validateOnly('comment');

        if (!auth()->check()) {
            request()->session()->flash('message', 'You need to sign in first');
        } else {
            $checkInfoData = empty($this->authenticatedUser->last_name && $this->authenticatedUser->first_name && $this->authenticatedUser->last_name &&
                $this->authenticatedUser->address &&
                $this->authenticatedUser->phone_no &&
                $this->authenticatedUser->student_id &&
                $this->authenticatedUser->bachelor_degree);

            if ($checkInfoData) {
                request()->session()->flash('message', 'Account information details are incomplete, fill out now here.');
            } else {
                if ($this->authenticatedUser->is_verified == 0) {
                    request()->session()->flash('message', 'Verify your account now to enjoy the full features for free.');
                } else {
                    // Remove special characters
                    $sanitizedComment = preg_replace('/[^\w\s]/u', '', $this->comment);

                    // Check for offensive words
                    $offensiveWords = DB::table('filter_words')->pluck('word')->toArray();
                    $offensiveWordsInComment = collect($offensiveWords)->filter(function ($offensiveWord) use ($sanitizedComment) {
                        return stripos(strtolower($sanitizedComment), $offensiveWord) !== false;
                    })->toArray();

                    if (!empty($offensiveWordsInComment)) {
                        $offensiveWordsList = implode(', ', $offensiveWordsInComment);
                        $this->addError('comment', "Don't use offensive words: ($offensiveWordsList)");
                        return null;
                    }

                    $checkIfSuccess = DocuPostComment::create([
                        'post_id' => $this->data->id,
                        'user_id' => $this->authenticatedUser->id,
                        'comment_content' => $this->comment,
                    ]);

                    if ($checkIfSuccess) {
                        request()->session()->flash('success', 'Comment created');
                    } else {
                        request()->session()->flash('warning', 'Comment failed');
                    }

                    $this->dispatch('$refresh');
                    return $this->comment = '';
                }
            }
        }
    }

    public $showReplyBox = false;
    public $targetReply = null;
    public $replyingTo;
    public $currentReplyingID;

    public function toggleReplyBox($commentId, $commentMainAuthor)
    {

        $this->showReplyBox = true;
        $this->targetReply = $commentId;

        $mainAuthorDetails = User::where('id', $commentMainAuthor)->first();

        if ($mainAuthorDetails) {
            $fullName = $mainAuthorDetails->first_name . ' ' . $mainAuthorDetails->last_name;
            $this->replyingTo = $fullName;
            $this->currentReplyingID = $commentMainAuthor;
        } else {
            $this->replyingTo = 'user';
        }
    }

    #[Rule('required|min:1', message: 'Reply should not be empty.')]
    public $replyCommentContent = '';

    #[Rule('required|min:1', message: 'You\'re about to reply a replied comment. It should not be empty.')]
    public $replyOfRepliedCommentContent = '';

    public function createReply()
    {
        $this->validateOnly('replyCommentContent');

        if (!auth()->check()) {
            request()->session()->flash('message', 'You need to sign in first');
        } else {
            $checkInfoData = empty($this->authenticatedUser->last_name && $this->authenticatedUser->first_name && $this->authenticatedUser->last_name &&
                $this->authenticatedUser->address &&
                $this->authenticatedUser->phone_no &&
                $this->authenticatedUser->student_id &&
                $this->authenticatedUser->bachelor_degree);

            if ($checkInfoData) {
                request()->session()->flash('message', 'Account information details are incomplete, fill out now here.');
            } else {
                if ($this->authenticatedUser->is_verified == 0) {
                    request()->session()->flash('message', 'Verify your account now to enjoy the full features for free.');
                } else {
                    // Remove special characters
                    $sanitizedReplyComment = preg_replace('/[^\w\s]/u', '', $this->replyCommentContent);

                    // Check for bad words
                    $badWords = DB::table('filter_words')->pluck('word')->toArray();
                    $badWordsInReplyComment = collect($badWords)->filter(function ($badWord) use ($sanitizedReplyComment) {
                        return stripos(strtolower($sanitizedReplyComment), $badWord) !== false;
                    })->toArray();

                    if (!empty($badWordsInReplyComment)) {
                        $badWordsList = implode(', ', $badWordsInReplyComment);
                        $this->addError('replyCommentContent', "Please avoid using inappropriate language: ($badWordsList)");
                        return null;
                    }

                    $checkIfSuccess = DocuPostComment::create([
                        'post_id' => $this->data->id,
                        'parent_id' => $this->targetReply,
                        'user_id' => $this->authenticatedUser->id,
                        'comment_content' => $this->replyCommentContent,
                        'reply_parent_author' => $this->currentReplyingID,
                    ]);

                    if ($checkIfSuccess) {
                        request()->session()->flash('success', 'Comment created');
                    } else {
                        request()->session()->flash('warning', 'Comment failed');
                    }

                    $this->dispatch('$refresh');
                    $this->showReplyBox = false;
                    return $this->comment = '';
                }
            }
        }
    }


    public function createRepliesOfReply()
    {
        $this->validateOnly('replyOfRepliedCommentContent');
        $checkIfSuccess = DocuPostComment::create([
            'post_id' => $this->data->id,
            'parent_id' => $this->parentIDCommentValue,
            'user_id' => $this->authenticatedUser->id,
            'comment_content' => $this->replyOfRepliedCommentContent,
            'reply_parent_author' => $this->parentCommentUserId,
        ]);

        if ($checkIfSuccess) {
            request()->session()->flash('success', 'Comment created');
        } else {
            request()->session()->flash('warning', 'Comment failed');
        }

        $this->dispatch('$refresh');
        $this->replyBoxOfReplies = false;
        return $this->replyOfRepliedCommentContent = '';
    }

    public $replyBoxOfReplies = false;
    public $replyingIDtarget;
    public $replyingToReplyName;
    public $parentCommentUserId;
    public $parentIDCommentValue;

    public function toggleReplyBoxFromReplies($replyingIdOfComment, $UserIdOfComment, $parenIDOfComment)
    {
        $this->parentIDCommentValue = $parenIDOfComment;
        $this->parentCommentUserId = $UserIdOfComment;
        $this->replyingIDtarget = $replyingIdOfComment;
        $this->replyBoxOfReplies = true;

        $findUserID = DocuPostComment::where('id', $replyingIdOfComment)->first();

        if ($findUserID) {
            $nameOfComment = $findUserID->user_id;

            $namingTheAuthor = User::where('id', $nameOfComment)->first();

            if ($namingTheAuthor) {
                $this->replyingToReplyName = $namingTheAuthor->first_name . ' ' . $namingTheAuthor->last_name;
            } else {
                $this->replyingToReplyName = 'Unknown User';
            }
        } else {
            $this->replyingToReplyName = 'Unknown Comment';
        }
    }

    public $InputPDFKey = 'Generate key';
    protected $debug = true;

    public function generatePDFKey($id)
    {
        if (!auth()->check()) {
            return request()->session()->flash('message', 'You need to sign in first');
        } else {
            $checkInfoData = empty($this->authenticatedUser->last_name && $this->authenticatedUser->first_name && $this->authenticatedUser->last_name &&
                $this->authenticatedUser->address &&
                $this->authenticatedUser->phone_no &&
                $this->authenticatedUser->student_id &&
                $this->authenticatedUser->bachelor_degree);

            if ($checkInfoData) {
                return request()->session()->flash('message', 'Account information details are incomplete, fill out now here.');
            } else {
                if ($this->authenticatedUser->is_verified == 0) {
                    return request()->session()->flash('message', 'Verify your account now to enjoy the full features for free.');
                } else {
                    $isKeyGenerated = PdfKey::where('docu_post_id', $id)
                        ->where('user_id', $this->authenticatedUser->id)
                        ->whereDate('created_at', today())
                        ->first();

                    if ($isKeyGenerated) {
                        $isKeyGenerated->delete();
                    }
                    return $this->keyGenerator($id);
                }
            }
        }
    }

    // public function copyKey($pdfKey){
    //     dd($this->InputPDFKey);
    //     if ($this->InputPDFKey == 'Generate key'){
    //         request()->session()->flash('message', 'The key is empty please click the generate key first');
    //     }
    //     request()->session()->flash('message', 'Succesfully copied to your clipboard!!');
    // }

    #[Js]
    public function copyToClip()
    {
        return <<<'JS'
            const shareInput = document.getElementById('valueBox');
            try {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(shareInput.value).then(() => {
                        console.log('Link copied to clipboard!');
                    }).catch((err) => {
                        console.error('Error copying to clipboard:', err);
                    });
                } else {
                    fallbackCopyTextToClipboard(shareInput.value);
                }
            } catch (err) {
                console.error('Error copying to clipboard:', err);
            }

            function fallbackCopyTextToClipboard(text) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();

                try {
                    document.execCommand('copy');
                    console.log('Link copied to clipboard using fallback method!');
                } catch (err) {
                    console.error('Error copying to clipboard:', err);
                }

                document.body.removeChild(textArea);
            }
        JS;
    }

    #[Js]
    public function copyCite()
    {
        return <<<'JS'

            const shareInput = document.getElementById('citeTxt');
            try {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(shareInput.value).then(() => {
                        console.log('Link copied to clipboard!');
                    }).catch((err) => {
                        console.error('Error copying to clipboard:', err);
                    });
                } else {
                    fallbackCopyTextToClipboard(shareInput.value);
                }
            } catch (err) {
                console.error('Error copying to clipboard:', err);
            }

            function fallbackCopyTextToClipboard(text) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();

                try {
                    document.execCommand('copy');
                    console.log('Link copied to clipboard using fallback method!');
                } catch (err) {
                    console.error('Error copying to clipboard:', err);
                }

                document.body.removeChild(textArea);
            }
        JS;
    }


    protected function keyGenerator($id)
    {
        do {
            $secureKey = Str::random(18);
        } while (PdfKey::where('keys', $secureKey)->exists());

        $createKey = PdfKey::create([
            'user_id' => auth()->user()->id,
            'docu_post_id' => $id,
            'keys' => $secureKey,
        ]);

        if ($createKey) {
            $this->InputPDFKey = $secureKey;
            return request()->session()->flash('success', 'Key generated success, you can use it now');
        } else {
            return request()->session()->flash('warning', 'Error generating Keys, contact IT Administrator.');
        }
    }

    public $reportingCommentData;

    public function showReportBox($commentID)
    {
        $reportedCommentData = DocuPostComment::where('id', $commentID)->first();
        $this->reportingCommentData = $reportedCommentData;
        $this->dispatch('open-rep');
    }

    public function closeReportBox()
    {
        $this->dispatch('close-rep');
        $this->reportReason = '';
        return $this->report_other_context = '';
    }

    #[Rule('required|min:5', message: 'You need to specify the reason for reporting this comment.')]
    public $report_other_context;

    #[Rule('required', message: 'Please select a reason.')]
    public $reportReason;

    public function createReportComment($id)
    {
        $this->validateOnly('reportReason');

        if ($this->reportReason === 'other') {
            $this->validateOnly('report_other_context');
        }

        $createCommentReport = ReportedComment::create([
            'docu_post_id' => $this->reportingCommentData->post_id,
            'reporter_user_id' => auth()->user()->id,
            'reported_user_id' => $this->reportingCommentData->user_id,
            'reported_comment_id' => $this->reportingCommentData->id,
            'report_title' => $this->reportReason,
            'report_other_context' => $this->report_other_context,
            'report_status' => 0,
        ]);

        $this->closeReportBox();

        if ($createCommentReport) {
            request()->session()->flash('success', 'Report created ');
        } else {
            request()->session()->flash('error', 'Creating report failed, contact devs.');
        }

        return;
    }




    public function render()
    {
        $comments = DocuPostComment::where('post_id', $this->data->id)
            ->where('parent_id', null)
            ->where('reply_parent_author', null)
            ->latest()->get();
        $replyComments = DocuPostComment::where('post_id', $this->data->id)
            ->whereNotNull('parent_id')
            ->orderBy('created_at')
            ->get();

        if (auth()->check()) {
            $idAdmin = auth()->user()->is_admin;
        } else {
            $idAdmin = false;
        }
        $this->checkBookmark();
        $layout = $idAdmin ? 'layout.admin' : 'layout.app';
        return view('livewire.view-docu-post', [
            'comments' => $comments,
            'replyComments' => $replyComments,
            // 'repliesToReplyComments' => $repliesToReplyComments,
        ])->layout($layout);
    }
}
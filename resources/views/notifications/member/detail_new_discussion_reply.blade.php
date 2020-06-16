<div class="media">
    <div class="media-body">
    <h5 class="media-heading"><span class="btn btn-circle btn-warning"><i class="ti-comments"></i></span> {{ ucwords($notification->data['user']) . ' '. __('email.discussionReply.subject') . $notification->data['title'] }}</h5>
    </div>
    <h6><i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</i></h6>
</div>

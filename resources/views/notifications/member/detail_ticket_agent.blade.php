<div class="media">
    <div class="media-body">
    <h5 class="media-heading"><span class="btn btn-circle btn-warning"><i class="icon-note"></i></span> {{ __('email.ticketAgent.subject') }} - {{ $notification->data['subject'].' # '.$notification->data['id'] }}</h5>
    </div>
    <h6><i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</i></h6>
</div>

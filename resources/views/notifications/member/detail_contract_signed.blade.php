
<div class="media">
    <div class="media-body">
        <h5 class="media-heading"><span class="btn btn-circle btn-success"><i class="icon-user"></i></span> {{ __('email.contractSign.subject') }} ({{ ucfirst($notification->data['subject']) }}) on {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->format('d M, Y') }}.</h5>
    </div>
    <h6><i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</i></h6>
</div>
